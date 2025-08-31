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
<<<<<<< Updated upstream
=======

    public function cupones()
    {
        return $this->belongsToMany(Cupon::class, 'cupone_producto')->withPivot('created_at', 'updated_at');
    }

    public function resenas()
    {
        return $this->hasMany(Reseña::class);
    }

    public function ventas()
    {
        return $this->hasMany(VentaProducto::class);
    }

    public function cuponesActivos()
    {
        return $this->belongsToMany(Cupon::class, 'cupone_producto')
            ->withPivot('created_at', 'updated_at')
            ->where(function ($query) {
                $query->whereNull('fecha_vencimiento')
                    ->orWhere('fecha_vencimiento', '>=', now());
            });
    }

    protected static function booted()
    {
        static::created(function ($product) {
            event(new \App\Events\ProductChanged($product, 'created'));
        });

        static::updated(function ($product) {
            event(new \App\Events\ProductChanged($product, 'updated'));
        });

        static::deleted(function ($product) {
            event(new \App\Events\ProductChanged($product, 'deleted'));
        });
    }

    protected function setDescripcionAttribute($value)
    {
        $this->attributes['descripcion'] = strip_tags($value);
    }
>>>>>>> Stashed changes
}
