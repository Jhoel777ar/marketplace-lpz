<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Reseña;
use Illuminate\Support\Facades\Auth;

class ResenasProducto extends Component
{
    use WithPagination;

    public $producto;
    public $calificacion_producto;
    public $calificacion_servicio;
    public $reseña;

    public $editingResenaId = null; 

    protected $rules = [
        'calificacion_producto' => 'required|integer|min:1|max:5',
        'calificacion_servicio' => 'nullable|integer|min:1|max:5',
        'reseña' => 'nullable|string|max:200',
    ];

    public function mount($producto)
    {
        $this->producto = $producto;
    }

    public function submit()
    {
        $this->validate();

        if ($this->editingResenaId) {
            $resena = Reseña::findOrFail($this->editingResenaId);

            if ($resena->user_id !== Auth::id()) {
                abort(403);
            }

            $resena->update([
                'calificacion_producto' => $this->calificacion_producto,
                'calificacion_servicio' => $this->calificacion_servicio,
                'reseña' => $this->reseña,
            ]);

            $this->dispatch('resena-actualizada');

        } else {
            Reseña::create([
                'producto_id' => $this->producto->id,
                'user_id' => Auth::id(),
                'emprendedor_id' => $this->producto->emprendedor_id,
                'calificacion_producto' => $this->calificacion_producto,
                'calificacion_servicio' => $this->calificacion_servicio,
                'reseña' => $this->reseña,
                'aprobada' => true,
            ]);

            $this->dispatch('resena-agregada');
        }

        $this->reset(['calificacion_producto', 'calificacion_servicio', 'reseña', 'editingResenaId']);
        $this->resetPage();
    }

    public function edit($id)
    {
        $resena = Reseña::findOrFail($id);

        if ($resena->user_id !== Auth::id()) {
            abort(403);
        }

        $this->editingResenaId = $id;
        $this->calificacion_producto = $resena->calificacion_producto;
        $this->calificacion_servicio = $resena->calificacion_servicio;
        $this->reseña = $resena->reseña;
    }

    public function delete($id)
    {
        $resena = Reseña::findOrFail($id);

        if ($resena->user_id !== Auth::id()) {
            abort(403);
        }

        $resena->delete();
        $this->dispatch('resena-eliminada');
    }

    public function render()
    {
        return view('livewire.resenas-producto', [
            'resenas' => Reseña::where('producto_id', $this->producto->id)
                ->where('aprobada', true)
                ->with('usuario')
                ->latest()
                ->paginate(5),
        ]);
    }
}
