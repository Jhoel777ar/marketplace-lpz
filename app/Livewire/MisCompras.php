<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Venta;

class MisCompras extends Component
{
    public $ventas;

    public function mount()
    {
        $this->ventas = Venta::with(['productos.producto.imagenes', 'envio'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function render()
    {
        return view('livewire.mis-compras');
    }
}
