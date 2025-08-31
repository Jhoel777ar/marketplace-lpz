<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Producto;
use App\Models\ProductoImagen;

class Productos extends Component
{// propiedad pública
    public $productos; // propiedad pública para la vista

    public function mount()
    {
        // Traemos solo 12 productos con sus imágenes
        $this->productos = Producto::with('imagenes')
            ->take(12)
            ->get();
    }

    public function render()
    {
        return view('livewire.productos', [
            'productos' => $this->productos
        ]);
    }

}
