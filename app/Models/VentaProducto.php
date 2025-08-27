<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VentaProducto extends Model
{
    use HasFactory;

    protected $table = 'venta_productos';

    protected $fillable = [
        'venta_id',
        'producto_id',
        'cantidad',
        'subtotal',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
