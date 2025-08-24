<?php

namespace App\Http\Controllers;
use App\Models\Producto; // Tu modelo de productos
use Illuminate\Http\Request;

class CarritoController extends Controller
{
    // Mostrar carrito
    public function comprar()
    {
        $carrito = session()->get('carrito', []);
        $productos = Producto::whereIn('id', array_keys($carrito))->get();

        return view('carrito.comprar', compact('carrito', 'productos'));
    }

    // Agregar producto
    public function agregar($id)
    {
        $carrito = session()->get('carrito', []);

        if(isset($carrito[$id])){
            $carrito[$id]++;
        } else {
            $carrito[$id] = 1;
        }

        session()->put('carrito', $carrito);

        return redirect()->back();
    }

    // Actualizar cantidad
    public function actualizar(Request $request, $id)
    {
        $cantidad = $request->input('cantidad');
        $carrito = session()->get('carrito', []);

        if(isset($carrito[$id])){
            $carrito[$id] = max(1, $cantidad); // mínimo 1
            session()->put('carrito', $carrito);
        }

        return redirect()->back();
    }

    // Eliminar producto
    public function eliminar($id)
    {
        $carrito = session()->get('carrito', []);

        if(isset($carrito[$id])){
            unset($carrito[$id]);
            session()->put('carrito', $carrito);
        }

        return redirect()->back();
    }
}
