<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Producto;

class ProductosPublicos extends Component
{
    use WithPagination;

    public $search = '';
    public $priceMin = null;
    public $priceMax = null;
    public $orderBy = 'recientes';

    protected $queryString = [
        'search' => ['except' => ''],
        'orderBy' => ['except' => 'recientes'],
    ];

    public function updating($property)
    {
        if (in_array($property, ['search', 'priceMin', 'priceMax', 'orderBy'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $query = Producto::query()
            ->where('publico', 1)
            ->where('stock', '>', 0)
            ->with(['imagenes', 'emprendedor'])
            ->withCount('resenas')
            ->when($this->search, fn($q) => $q->where('nombre', 'like', "%{$this->search}%"))
            ->when($this->priceMin, fn($q) => $q->where('precio', '>=', (float)$this->priceMin))
            ->when($this->priceMax, fn($q) => $q->where('precio', '<=', (float)$this->priceMax));
        $query->when($this->orderBy === 'precio_asc', fn($q) => $q->orderBy('precio', 'asc'))
            ->when($this->orderBy === 'precio_desc', fn($q) => $q->orderBy('precio', 'desc'))
            ->when($this->orderBy === 'popularidad', fn($q) => $q->orderByDesc('resenas_count'))
            ->when(true, fn($q) => $q->orderByDesc('created_at'));
        $productos = $query->paginate(9);

        return view('livewire.productos-publicos', compact('productos'));
    }
}
