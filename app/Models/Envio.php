<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Envio extends Model
{
    use HasFactory;

    protected $table = 'envios';

    protected $fillable = [
        'venta_id',
        'direccion',
        'ciudad',
        'departamento',
        'pais',
        'codigo_postal',
        'estado',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }
}
