<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Carrito;
use App\Models\CarritoProducto;
use App\Models\Producto;
use Illuminate\Support\Facades\Auth;

class CarritoManager extends Component
{
    public $carrito;
    public $total = 0;

    protected $listeners = ['agregarAlCarrito' => 'agregarAlCarrito'];

    public function mount()
    {
        if (!Auth::check()) {
            $this->carrito = null;
            return;
        }

        $this->carrito = Carrito::firstOrCreate(
            ['user_id' => Auth::id()],
            ['total' => 0]
        );

        $this->calcularTotal();
    }

    public function agregarAlCarrito($id)
    {
        if (!Auth::check()) {
            $this->dispatch('alerta', type: 'error', message: 'Debes iniciar sesión para agregar productos.');
            return;
        }

        $producto = Producto::with('imagenes')->find($id);

        if (!$producto || !$producto->publico) {
            $this->dispatch('alerta', type: 'error', message: 'Este producto no está disponible públicamente.');
            return;
        }

        if ($producto->stock <= 0) {
            $this->dispatch('alerta', type: 'error', message: 'No hay stock disponible para este producto.');
            return;
        }

        $item = CarritoProducto::where('carrito_id', $this->carrito->id)
            ->where('producto_id', $producto->id)
            ->first();

        if ($item) {
            if ($item->cantidad >= $producto->stock) {
                $this->dispatch('alerta', type: 'warning', message: 'Ya tienes la cantidad máxima de este producto en tu carrito.');
                return;
            }

            $item->cantidad += 1;
            $item->subtotal = $item->cantidad * $producto->precio;
            $item->save();
        } else {
            CarritoProducto::create([
                'carrito_id' => $this->carrito->id,
                'producto_id' => $producto->id,
                'cantidad' => 1,
                'subtotal' => $producto->precio,
            ]);
        }

        $this->calcularTotal();
        $this->dispatch('alerta', type: 'success', message: 'Producto agregado al carrito.');
    }

    public function calcularTotal()
    {
        if (!$this->carrito) return;

        $this->total = $this->carrito->productos()->sum('subtotal');
        $this->carrito->update(['total' => $this->total]);
    }

    public function eliminarProducto($id)
    {
        $item = CarritoProducto::where('carrito_id', $this->carrito->id)
            ->where('id', $id)
            ->first();

        if ($item) {
            $item->delete();
            $this->calcularTotal();
        }
    }

    public function render()
    {
        return view('livewire.carrito-manager', [
            'items' => $this->carrito
                ? $this->carrito->productos()->with('producto')->get()
                : collect(),
            'total' => $this->total,
        ]);
    }
}
