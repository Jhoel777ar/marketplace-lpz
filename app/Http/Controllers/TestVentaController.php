<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use App\Models\VentaProducto;
use Illuminate\Http\Request;

class TestVentaController extends Controller
{
    public function create()
    {
        $buyer = User::find(1);
        $producto = Producto::where('emprendedor_id', auth()->id())->first();
        $venta = Venta::create([
            'user_id' => $buyer->id,
            'total' => $producto->precio,
            'estado' => 'pendiente',
        ]);

        VentaProducto::create([
            'venta_id' => $venta->id,
            'producto_id' => $producto->id,
            'cantidad' => 1,
            'subtotal' => $producto->precio,
        ]);

        $emprendedores = $venta->productos->pluck('producto.emprendedor_id')->unique();
        foreach ($emprendedores as $emprendedor_id) {
            $emprendedor = User::find($emprendedor_id);
            $emprendedor->notify(new \App\Notifications\NuevaVentaNotification($venta));
        }

        return 'Venta simulada y notificación enviada.';
    }
}