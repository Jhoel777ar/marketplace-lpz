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
        'categoria',
        'destacado',
        'fecha_publicacion',
        'emprendedor_id',
        'url_imagen'
    ];

    public function emprendedor()
    {
        return $this->belongsTo(User::class, 'emprendedor_id');
    }

    public function imagenes()
    {
        return $this->hasMany(ProductoImagen::class);
    }
    
}
