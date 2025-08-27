<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Reseña;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestReseñaController extends Controller
{
    public function create()
    {
        try {
            $emprendedor_id = auth()->id();
            if (!$emprendedor_id) {
                return response('Debes estar autenticado.', 401);
            }
            $buyer = User::find(1);
            if (!$buyer) {
                return response('Comprador no encontrado.', 404);
            }
            $producto = Producto::where('emprendedor_id', $emprendedor_id)->first();
            if (!$producto) {
                return response('No tienes productos registrados.', 404);
            }
            return DB::transaction(function () use ($buyer, $producto, $emprendedor_id) {
                $reseña = Reseña::create([
                    'producto_id' => $producto->id,
                    'user_id' => $buyer->id,
                    'emprendedor_id' => $emprendedor_id,
                    'calificacion_producto' => 5, 
                    'reseña' => '¡Excelente producto!', 
                    'aprobada' => false,
                ]);
                $emprendedor = User::find($emprendedor_id);
                $emprendedor->notify(new \App\Notifications\NuevaReseñaNotification($reseña));
                return 'Reseña simulada y notificación enviada.';
            });
        } catch (\Exception $e) {
            return response('Error: ' . $e->getMessage(), 500);
        }
    }
}