<?php

namespace Database\Factories;

use App\Models\Producto;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductoFactory extends Factory
{
    protected $model = Producto::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->word(),
            'descripcion' => $this->faker->sentence(),
            'precio' => $this->faker->randomFloat(2, 10, 500),
            'destacado' => $this->faker->boolean(),
            'publico' => $this->faker->boolean(),
            'stock' => $this->faker->numberBetween(0, 100),
            'fecha_publicacion' => now(),
            'emprendedor_id' => User::factory(),
        ];
    }
}
