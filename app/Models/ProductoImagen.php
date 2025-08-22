<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoImagen extends Model
{
    use HasFactory;

    protected $table = 'producto_imagenes';

    protected $fillable = [
        'producto_id',
        'ruta',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function setRutaAttribute($value)
    {
        $bucketUrl = 'https://' . env('AWS_BUCKET') . '.s3.' . env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
        $this->attributes['ruta'] = str_starts_with($value, 'http')
            ? $value
            : $bucketUrl . ltrim($value, '/');
    }
}
