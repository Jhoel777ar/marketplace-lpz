<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cupon extends Model
{
    use HasFactory;

    protected $table = 'cupones';

    protected $fillable = [
        'codigo',
        'descuento',
        'limite_usos',
        'usos_realizados',
        'fecha_vencimiento',
        'user_id',
    ];

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'cupone_producto');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
