<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Producto;
use App\Models\Carrito;
use App\Models\CarritoProducto;
use Illuminate\Support\Facades\Auth;

class ProductoDetalle extends Component
{
    public $producto;

    public function mount(Producto $producto)
    {
        $this->producto = $producto->load(['imagenes', 'emprendedor', 'categorias', 'resenas.usuario']);
    }

    public function agregarAlCarrito()
    {
        if (!Auth::check()) {
            $this->dispatch('alerta', type: 'error', message: 'Debes iniciar sesión para agregar productos.');
            return;
        }

        $carrito = Carrito::firstOrCreate(
            ['user_id' => Auth::id()],
            ['total' => 0]
        );

        $producto = $this->producto;

        if ($producto->stock <= 0) {
            $this->dispatch('alerta', type: 'warning', message: 'No hay stock disponible para este producto.');
            return;
        }

        $item = CarritoProducto::firstOrNew([
            'carrito_id' => $carrito->id,
            'producto_id' => $producto->id,
        ]);

        if ($item->exists) {
            if ($item->cantidad >= $producto->stock) {
                $this->dispatch('alerta', type: 'warning', message: 'Ya tienes la cantidad máxima de este producto en tu carrito.');
                return;
            }
            $item->cantidad += 1;
        } else {
            $item->cantidad = 1;
        }

        $item->subtotal = $item->cantidad * $producto->precio;
        $item->save();

        $carrito->update([
            'total' => $carrito->productos()->sum('subtotal')
        ]);

        return redirect()->route('carrito');
    }

    public function render()
    {
        return view('livewire.producto-detalle');
    }
}
