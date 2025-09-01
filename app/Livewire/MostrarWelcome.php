<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Producto;
use Illuminate\Support\Facades\Auth;

class MostrarWelcome extends Component
{
    use WithPagination;

    public $search = '';
    protected $paginationTheme = 'tailwind';

    public function verProducto($productoId)
    {
        if (Auth::check()) {
            return redirect()->route('productos.detalle', $productoId);
        } else {
            return redirect()->route('login');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $productos = Producto::where('publico', true)
            ->where('stock', '>', 0)
            ->when($this->search, function ($query) {
                $query->where('nombre', 'like', '%' . $this->search . '%');
            })
            ->with('imagenes', 'resenas')
            ->paginate(12);

        return view('livewire.mostrar-welcome', compact('productos'));
    }
}
