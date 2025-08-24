<?php

namespace App\Http\Controllers;
use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function MostrarProducto(Request $request)
    {
        // Creamos la consulta con la relación 'imagenes'
        $query = Producto::with('imagenes');

        // Filtrar por búsqueda si hay texto
        if ($request->has('search') && !empty($request->search)) {
            $query->where('nombre', 'like', '%' . $request->search . '%');
        }

        // Ejecutamos la consulta
        $productos = $query->get();

        // Retornamos la vista con los productos
        return view('home', compact('productos'));
    }

}
