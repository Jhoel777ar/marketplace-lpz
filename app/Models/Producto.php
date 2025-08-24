<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'destacado',
        'publico',
        'stock',
        'fecha_publicacion',
        'emprendedor_id',
    ];

    public function emprendedor()
    {
        return $this->belongsTo(User::class, 'emprendedor_id');
    }

    public function imagenes()
    {
        return $this->hasMany(ProductoImagen::class, 'producto_id');
    }

    public function categorias()
    {
        return $this->belongsToMany(Categoria::class, 'categoria_producto');
    }

    public function scopeOwnedBy($query, $userId)
    {
        return $query->where('emprendedor_id', $userId);
    }
}
