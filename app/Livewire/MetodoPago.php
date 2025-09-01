<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\{Carrito, CarritoProducto, Producto, Cupon, Venta, VentaProducto, Envio};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Exception;
use App\Jobs\EnviarCorreoVenta;

class MetodoPago extends Component
{
    public $carrito;
    public $items;
    public $subtotal = 0;
    public $total = 0;
    public $codigoCupon = '';
    public $cuponesAplicados;

    public $direccion, $ciudad, $departamento, $codigo_postal, $pais;

    public $paso = 0;

    public function mount()
    {
        $this->cuponesAplicados = collect();
        $this->cargarDatos();
        $productosSinStock = $this->items->filter(fn($item) => $item->producto->stock < $item->cantidad);
        if ($productosSinStock->isNotEmpty()) {
            foreach ($productosSinStock as $item) {
                $this->dispatch(
                    'alerta',
                    type: 'warning',
                    message: "El producto '{$item->producto->nombre}' tiene stock insuficiente. Ajuste la cantidad."
                );
            }
            $this->paso = 0;
        }
    }

    public function cargarDatos()
    {
        $this->carrito = Carrito::with('productos.producto')
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $this->items = $this->carrito->productos()->with('producto')->get();
        $this->subtotal = $this->items->sum('subtotal');
        $this->calcularTotal();
    }

    public function aplicarCupon()
    {
        $cupon = Cupon::where('codigo', $this->codigoCupon)
            ->where(function ($q) {
                $q->whereNull('fecha_vencimiento')->orWhere('fecha_vencimiento', '>=', now());
            })->first();

        if (!$cupon) {
            $this->dispatch('alerta', type: 'error', message: 'Cupón no válido o vencido.');
            return;
        }

        if ($cupon->usos_realizados >= $cupon->limite_usos) {
            $this->dispatch('alerta', type: 'error', message: 'Cupón ya alcanzó su límite de usos.');
            return;
        }

        $productosAplicables = $this->items->filter(fn($i) => $i->producto->cuponesActivos->contains('id', $cupon->id));
        if ($productosAplicables->isEmpty()) {
            $this->dispatch('alerta', type: 'error', message: 'Cupón no aplica a tu carrito.');
            return;
        }

        foreach ($productosAplicables as $producto) {
            $this->cuponesAplicados->push((object)[
                'producto_id' => $producto->producto_id,
                'codigo' => $cupon->codigo,
                'descuento' => $cupon->descuento,
                'cupon_id' => $cupon->id,
            ]);
        }

        $this->calcularTotal();
        $this->dispatch('alerta', type: 'success', message: 'Cupón aplicado a los productos correspondientes.');
    }

    public function calcularTotal()
    {
        $this->total = $this->subtotal;

        foreach ($this->cuponesAplicados as $cupon) {
            $item = $this->items->firstWhere('producto_id', $cupon->producto_id);
            if ($item) {
                $descuento = ($item->subtotal * $cupon->descuento) / 100;
                $this->total -= $descuento;
            }
        }
    }

    public function pagarConStripe($token)
    {
        $this->validate([
            'direccion' => 'required|string|max:255',
            'ciudad' => 'required|string|max:100',
            'departamento' => 'required|string|max:100',
            'codigo_postal' => 'nullable|string|max:20',
            'pais' => 'required|string|max:100',
        ], [
            'direccion.required' => 'La dirección es obligatoria.',
            'ciudad.required' => 'La ciudad es obligatoria.',
            'departamento.required' => 'El departamento es obligatorio.',
            'pais.required' => 'El país es obligatorio.',
            'codigo_postal.required' => 'El código postal es obligatorio.',
        ]);

        $user = Auth::user();
        DB::beginTransaction();

        try {
            $this->paso = 1;
            foreach ($this->items as $item) {
                $producto = Producto::where('id', $item->producto_id)->lockForUpdate()->first();

                if ($producto->stock < $item->cantidad) {
                    DB::rollBack();
                    $this->dispatch(
                        'alerta',
                        type: 'error',
                        message: "El producto '{$producto->nombre}' ya fue vendido a otro usuario. Stock disponible: {$producto->stock}."
                    );
                    return redirect()->route('carrito');
                }
            }

            $this->paso = 2;

            Stripe::setApiKey(env('STRIPE_SECRET'));
            $paymentIntent = PaymentIntent::create([
                'amount' => intval($this->total * 100),
                'currency' => config('cashier.currency'),
                'payment_method_types' => ['card'],
                'description' => "Compra en Marketplace de " . $user->name,
                'receipt_email' => $user->email,
                'payment_method_data' => [
                    'type' => 'card',
                    'card' => ['token' => $token],
                ],
                'confirm' => true,
            ]);

            $this->paso = 3;

            $venta = Venta::create([
                'user_id' => $user->id,
                'total' => $this->total,
                'estado' => 'pagado',
            ]);

            foreach ($this->items as $item) {
                VentaProducto::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $item->producto_id,
                    'cantidad' => $item->cantidad,
                    'subtotal' => $item->subtotal,
                ]);

                $item->producto->decrement('stock', $item->cantidad);
            }

            Envio::create([
                'venta_id' => $venta->id,
                'direccion' => $this->direccion,
                'ciudad' => $this->ciudad,
                'departamento' => $this->departamento,
                'pais' => $this->pais,
                'codigo_postal' => $this->codigo_postal,
                'estado' => 'pendiente',
            ]);

            foreach ($this->cuponesAplicados as $c) {
                Cupon::where('id', $c->cupon_id)->increment('usos_realizados');
            }

            $this->carrito->productos()->delete();
            $this->carrito->update(['total' => 0]);

            DB::commit();
            $this->paso = 4;

            $emprendedores = $venta->productos->pluck('producto.emprendedor_id')->unique();
            foreach ($emprendedores as $emprendedor_id) {
                $emprendedor = \App\Models\User::find($emprendedor_id);
                if ($emprendedor) {
                    $emprendedor->notify(new \App\Notifications\NuevaVentaNotification($venta));
                }
            }
            EnviarCorreoVenta::dispatch($venta);
            $this->dispatch('alerta', type: 'success', message: 'Compra realizada con éxito!');
            $this->dispatch('compraExitosa');
        } catch (Exception $e) {
            DB::rollBack();
            $this->dispatch('alerta', type: 'error', message: $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.metodo-pago');
    }
}
