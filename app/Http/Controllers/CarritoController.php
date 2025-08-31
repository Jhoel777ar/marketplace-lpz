<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Carrito;
use App\Models\CarritoProducto;
use App\Models\Producto;
use App\Models\Cupon;
use Illuminate\Support\Facades\Auth;

class CarritoController extends Controller
{
    // ------- Helper centralizado -------
    private function recalcularTotales(Carrito $carrito): array
    {
        // Subtotal SIEMPRE desde los items
        $subtotal = (float) $carrito->productos()->sum('subtotal');
        $descuento = 0.0;

        // Limpia cupón si no hay items
        if ($carrito->productos()->count() === 0) {
            session()->forget(['coupon_id', 'coupon_code']);
        }

        // Aplica cupón si existe
        $cupon = session()->has('coupon_id') ? Cupon::find(session('coupon_id')) : null;

        if ($subtotal > 0 && $cupon) {
            if ($cupon->productos()->exists()) {
                $ids = $cupon->productos()->pluck('id');
                $subtotalAfectado = (float) $carrito->productos()
                    ->whereIn('producto_id', $ids)
                    ->sum('subtotal');
            } else {
                $subtotalAfectado = $subtotal;
            }
            $descuento = round($subtotalAfectado * ($cupon->descuento / 100), 2);
        }

        $total = round($subtotal - $descuento, 2);

        // Guarda en BD el total con descuento (como pediste)
        $carrito->total = $total;
        $carrito->save();

        return compact('subtotal', 'descuento', 'total');
    }

    // Agregar producto
    public function agregar(Request $request, $producto_id)
    {
        $user = Auth::user();
        $carrito = Carrito::firstOrCreate(
            ['user_id' => $user->id],
            ['total' => 0]
        );

        $producto = Producto::findOrFail($producto_id);

        $carritoProducto = CarritoProducto::where('carrito_id', $carrito->id)
            ->where('producto_id', $producto->id)
            ->first();

        if ($carritoProducto) {
            $carritoProducto->cantidad += 1;
            $carritoProducto->subtotal = $carritoProducto->cantidad * $producto->precio;
            $carritoProducto->save();
        } else {
            CarritoProducto::create([
                'carrito_id' => $carrito->id,
                'producto_id' => $producto->id,
                'cantidad'   => 1,
                'subtotal'   => $producto->precio,
            ]);
        }

        // Recalcula y persiste (total con descuento si hubiera)
        $calc = $this->recalcularTotales($carrito);

        if ($request->expectsJson()) {
            return response()->json([
                'cantidad' => $carrito->productos()->sum('cantidad'),
                'total'    => $calc['total'],
            ]);
        }

        return back()->with('success', 'Producto agregado al carrito');
    }

    public function ver()
    {
        $user = Auth::user();
        $carrito = Carrito::where('user_id', $user->id)->first();

        $productos = collect();
        $cantidadTotal = 0;
        $subtotal = 0;
        $descuento = 0;
        $total = 0;

        if ($carrito) {
            $productos = $carrito->productos()->with('producto')->paginate(5);
            $cantidadTotal = (int) $carrito->productos()->sum('cantidad');

            // Calcula SIEMPRE desde los items; guarda total en BD
            $calc = $this->recalcularTotales($carrito);
            $descuento = $calc['descuento'];
            $total     = $calc['total'];
        }

        return view('carrito.comprar', compact(
            'carrito',
            'productos',
            'cantidadTotal',
            'subtotal',
            'descuento',
            'total'
        ));
    }

    // Actualizar cantidad
    public function actualizar(Request $request, $producto_id)
    {
        $user = Auth::user();
        $carrito = Carrito::where('user_id', $user->id)->firstOrFail();

        $carritoProducto = CarritoProducto::where('carrito_id', $carrito->id)
            ->where('producto_id', $producto_id)
            ->firstOrFail();

        $cantidad = max(1, (int) $request->cantidad);
        $carritoProducto->cantidad = $cantidad;
        $carritoProducto->subtotal = $cantidad * $carritoProducto->producto->precio;
        $carritoProducto->save();

        $this->recalcularTotales($carrito);

        return redirect()->route('carrito.ver');
    }

    // Eliminar producto
    public function eliminar($producto_id)
    {
        $user = Auth::user();
        $carrito = Carrito::where('user_id', $user->id)->firstOrFail();

        $carritoProducto = CarritoProducto::where('carrito_id', $carrito->id)
            ->where('producto_id', $producto_id)
            ->first();

        if ($carritoProducto) {
            $carritoProducto->delete();
        }

        $this->recalcularTotales($carrito);

        return redirect()->route('carrito.ver');
    }

    // Contador de productos
    public function contador()
    {
        $user = Auth::user();
        $carrito = Carrito::where('user_id', $user->id)->first();
        return $carrito ? (int) $carrito->productos()->sum('cantidad') : 0;
    }

    // Aplicar cupón
    public function aplicarCupon(Request $request)
    {
        $request->validate(['coupon' => 'required|string']);

        $user = Auth::user();
        $carrito = Carrito::where('user_id', $user->id)->first();

        if (!$carrito || $carrito->productos()->count() === 0) {
            return back()->with('coupon_message', 'Tu carrito está vacío.');
        }

        $codigo = strtoupper(trim($request->coupon));
        $cupon = Cupon::where('codigo', $codigo)->first();

        if (!$cupon) {
            return back()->with('coupon_message', 'Cupón inválido ❌');
        }
        if ($cupon->fecha_vencimiento && $cupon->fecha_vencimiento < now()) {
            return back()->with('coupon_message', 'El cupón está vencido ❌');
        }
        if ($cupon->limite_usos && $cupon->usos_realizados >= $cupon->limite_usos) {
            return back()->with('coupon_message', 'El cupón ya alcanzó su límite de usos ❌');
        }

        // Guarda cupón en sesión
        session(['coupon_id' => $cupon->id, 'coupon_code' => $cupon->codigo]);

        // Recalcula y persiste total con descuento
        $this->recalcularTotales($carrito);

        return back()->with('coupon_message', 'Cupón aplicado correctamente ✅');
    }

    // Quitar cupón
    public function quitarCupon()
    {
        $user = Auth::user();
        $carrito = Carrito::where('user_id', $user->id)->first();

        session()->forget(['coupon_id', 'coupon_code']);

        if ($carrito) {
            $this->recalcularTotales($carrito);
        }

        return back()->with('coupon_message', 'Cupón eliminado ❌');
    }
}
