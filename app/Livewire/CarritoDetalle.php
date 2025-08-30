<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Carrito;
use App\Models\CarritoProducto;
use Illuminate\Support\Facades\Auth;

class CarritoDetalle extends Component
{
    public $carrito;
    public $total = 0;

    public function mount()
    {
        $this->carrito = Carrito::with(['productos.producto.imagenes', 'productos.producto.resenas'])
            ->where('user_id', Auth::id())
            ->firstOrFail();
        $this->limpiarCarritoInvalido();
        $this->calcularTotal();
    }

    public function actualizarCantidad($id, $cantidad)
    {
        $item = CarritoProducto::findOrFail($id);

        if ($cantidad <= 0) {
            $item->delete();
        } else {
            if ($item->producto->stock < $cantidad) {
                $this->dispatch('alerta', type: 'error', message: 'No hay stock suficiente.');
                return;
            }
            $item->cantidad = $cantidad;
            $item->subtotal = $cantidad * $item->producto->precio;
            $item->save();
        }

        $this->limpiarCarritoInvalido();
        $this->calcularTotal();
    }

    public function eliminar($id)
    {
        CarritoProducto::findOrFail($id)->delete();
        $this->calcularTotal();
    }

    public function calcularTotal()
    {
        $this->total = $this->carrito->productos()->sum('subtotal');
        $this->carrito->update(['total' => $this->total]);
    }

    public function limpiarCarritoInvalido()
    {
        foreach ($this->carrito->productos as $item) {
            if (!$item->producto->publico || $item->producto->stock <= 0) {
                $item->delete();
            }
        }
        $this->carrito->load('productos.producto.imagenes', 'productos.producto.resenas');
    }

    public function render()
    {
        $items = $this->carrito->productos()->with('producto.imagenes', 'producto.resenas')->get();
        foreach ($items as $item) {
            $item->promedioResena = $item->producto->resenas()->aprobadas()->avg('calificacion_producto');
            $item->imagenPrincipal = $item->producto->imagenes->first()?->ruta
                ?? 'https://png.pngtree.com/png-vector/20230224/ourmid/pngtree-image-icone-png-image_6617630.png';
        }
        return view('livewire.carrito-detalle', [
            'items' => $items,
            'total' => $this->total,
        ]);
    }
}
