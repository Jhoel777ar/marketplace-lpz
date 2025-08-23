<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmprendedorVerificacion extends Model
{
    use HasFactory;

    protected $table = 'emprendedor_verificaciones';

    protected $fillable = [
        'user_id',
        'image_path',
        'is_verified',
        'verified_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function setImagePathAttribute($value)
    {
        if (str_starts_with($value, 'http')) {
            $this->attributes['image_path'] = $value;
            return;
        }
        $this->attributes['image_path'] = 'https://' . env('AWS_BUCKET') . '.s3.' . env('AWS_DEFAULT_REGION') . '.amazonaws.com/' . ltrim($value, '/');
    }

    public function scopeOwnedBy($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
