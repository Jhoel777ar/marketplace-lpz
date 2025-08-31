<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Carrito;
use App\Models\Cupon;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class MetodoPago extends Component
{
    public $carrito;
    public $items;
    public $subtotal = 0;
    public $total = 0;

    /** @var \Illuminate\Support\Collection */
    public $cuponesAplicados;

    public $codigoCupon = '';

    public function mount()
    {
        $this->carrito = Carrito::with('productos.producto.cuponesActivos')
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $this->cuponesAplicados = collect();
        $this->cargarDatos();
    }

    public function cargarDatos()
    {
        $this->items = $this->carrito->productos()->with('producto.cuponesActivos')->get();
        $this->subtotal = $this->items->sum('subtotal');
        $this->calcularTotal();
    }

    public function aplicarCupon()
    {
        if ($this->cuponesAplicados->count() >= 2) {
            $this->dispatch('alerta', type: 'error', message: 'Solo puedes aplicar 2 cupones máximo.');
            return;
        }

        $cupon = Cupon::where('codigo', $this->codigoCupon)
            ->where(function ($q) {
                $q->whereNull('fecha_vencimiento')
                    ->orWhere('fecha_vencimiento', '>=', now());
            })
            ->first();

        if (!$cupon) {
            $this->dispatch('alerta', type: 'error', message: 'Cupón no válido.');
            return;
        }
        $producto = $this->items->first(fn($item) => $item->producto->cuponesActivos->contains('id', $cupon->id));

        if (!$producto) {
            $this->dispatch('alerta', type: 'error', message: 'Cupón no aplica a productos en tu carrito.');
            return;
        }
        if ($this->cuponesAplicados->contains('producto_id', $producto->id)) {
            $this->dispatch('alerta', type: 'error', message: 'Ya aplicaste un cupón en ese producto.');
            return;
        }
        $this->cuponesAplicados->push((object)[
            'producto_id' => $producto->id,
            'codigo' => $cupon->codigo,
            'descuento' => $cupon->descuento,
        ]);
        $this->dispatch('alerta', type: 'success', message: 'Cupón aplicado.');
        $this->calcularTotal();
    }

    public function calcularTotal()
    {
        $this->total = $this->subtotal;

        foreach ($this->cuponesAplicados as $cupon) {
            $item = $this->items->firstWhere('id', $cupon->producto_id);
            if ($item) {
                $descuento = ($item->subtotal * $cupon->descuento) / 100;
                $this->total -= $descuento;
            }
        }
    }

    public function render()
    {
        return view('livewire.metodo-pago');
    }

    public function pagarConStripe($token)
    {
        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));
            $paymentIntent = PaymentIntent::create([
                'amount' => intval($this->total * 100),
                'currency' => config('cashier.currency'),
                'payment_method_types' => ['card'],
                'description' => "Depósito desde Tu ex Market por " . auth()->user()->name,
                'receipt_email' => auth()->user()->email,
                'payment_method_data' => [
                    'type' => 'card',
                    'card' => [
                        'token' => $token,
                    ],
                ],
                'confirm' => true,
            ]);

            $this->dispatch('alerta', type: 'success', message: 'Pago realizado con éxito!');
        } catch (\Exception $e) {
            $this->dispatch('alerta', type: 'error', message: $e->getMessage());
        }
    }
}
