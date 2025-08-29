<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Producto;

class ProductoDetalle extends Component
{
    public $producto;

    public function mount(Producto $producto)
    {
        $this->producto = $producto->load(['imagenes', 'emprendedor', 'categorias', 'resenas.usuario']);
    }

    public function render()
    {
        return view('livewire.producto-detalle');
    }
}
