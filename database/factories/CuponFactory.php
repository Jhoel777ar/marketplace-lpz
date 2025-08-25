<?php

namespace Database\Factories;

use App\Models\Cupon;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CuponFactory extends Factory
{
    protected $model = Cupon::class;

    public function definition(): array
    {
        return [
            'codigo' => strtoupper($this->faker->unique()->bothify('CUPON-####')),
            'descuento' => $this->faker->numberBetween(5, 50),
            'limite_usos' => 5,
            'usos_realizados' => 0,
            'fecha_vencimiento' => now()->addDays(30),
            'user_id' => User::factory(),
        ];
    }
}
