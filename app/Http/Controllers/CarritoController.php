<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Carrito;
use App\Models\CarritoProducto;
use App\Models\Producto;
use Illuminate\Support\Facades\Auth;

class CarritoController extends Controller
{


    public function agregar($producto_id)
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
                'cantidad' => 1,
                'subtotal' => $producto->precio,
            ]);
        }

        $carrito->total = $carrito->productos()->sum('subtotal');
        $carrito->save();

        // ✅ Retornar JSON en vez de redirect
        return response()->json([
            'cantidad' => $carrito->productos()->sum('cantidad'),
            'total' => $carrito->total
        ]);
    }


    public function ver()
    {
        $user = Auth::user();
        $carrito = Carrito::where('user_id', $user->id)->first();

        $productos = [];
        $cantidadTotal = 0;

        if ($carrito) {
            $productos = $carrito->productos()->with('producto')->get();
            $cantidadTotal = $carrito->productos()->sum('cantidad'); // ✅ total de productos
        }

        return view('carrito.comprar', compact('carrito', 'productos', 'cantidadTotal'));
    }


    // Actualizar cantidad
    public function actualizar(Request $request, $producto_id)
    {
        $user = Auth::user();
        $carrito = Carrito::where('user_id', $user->id)->firstOrFail();

        $carritoProducto = CarritoProducto::where('carrito_id', $carrito->id)
            ->where('producto_id', $producto_id)
            ->firstOrFail();

        $cantidad = max(1, (int) $request->cantidad); // mínimo 1
        $carritoProducto->cantidad = $cantidad;
        $carritoProducto->subtotal = $cantidad * $carritoProducto->producto->precio;
        $carritoProducto->save();

        $carrito->total = $carrito->productos()->sum('subtotal');
        $carrito->save();

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

        $carrito->total = $carrito->productos()->sum('subtotal');
        $carrito->save();

        return redirect()->route('carrito.ver');
    }

    // 🔥 Nuevo método: contador de productos en carrito
    public function contador()
    {
        $user = Auth::user();
        $carrito = Carrito::where('user_id', $user->id)->first();

        if (!$carrito) {
            return 0;
        }

        // suma total de cantidades de productos
        return $carrito->productos()->sum('cantidad');
    }
}
