<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reseña extends Model
{
    use HasFactory;

    protected $table = 'reseñas';

    protected $fillable = [
        'producto_id',
        'user_id',
        'emprendedor_id',
        'calificacion_producto',
        'calificacion_servicio',
        'reseña',
        'aprobada',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function emprendedor()
    {
        return $this->belongsTo(User::class, 'emprendedor_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function scopeAprobadas($query)
    {
        return $query->where('aprobada', true);
    }

    public function scopeOwnedBy($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
