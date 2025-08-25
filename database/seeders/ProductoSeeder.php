<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Cupon;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $emprendedor = User::where('email', 'emprendedor@example.com')->first();

        if (! $emprendedor) {
            $this->command->warn("⚠️ No existe un emprendedor. Ejecuta primero UserSeeder.");
            return;
        }
        $categorias = [
            'Tecnología',
            'Ropa',
            'Accesorios',
            'Alimentos'
        ];

        foreach ($categorias as $cat) {
            Categoria::firstOrCreate(['nombre' => $cat]);
        }

        $producto1 = Producto::firstOrCreate(
            ['nombre' => 'Laptop Gamer'],
            [
                'descripcion' => 'Laptop potente para gaming y desarrollo.',
                'precio' => 1500.00,
                'destacado' => true,
                'publico' => true,
                'stock' => 10,
                'emprendedor_id' => $emprendedor->id,
            ]
        );

        $producto2 = Producto::firstOrCreate(
            ['nombre' => 'Camiseta Negra'],
            [
                'descripcion' => 'Camiseta básica de algodón, talla M.',
                'precio' => 20.00,
                'publico' => true,
                'stock' => 50,
                'emprendedor_id' => $emprendedor->id,
            ]
        );

        $producto1->categorias()->sync([Categoria::where('nombre', 'Tecnología')->first()->id]);
        $producto2->categorias()->sync([Categoria::where('nombre', 'Ropa')->first()->id]);
        $producto1->imagenes()->create(['ruta' => 'https://www.cnet.com/a/img/resize/bb8a2aa9c31f8ec08d82228a51eabf05f00e54d2/hub/2025/03/10/d190e21d-9634-440d-8f33-396c8cb3da6a/m4-macbook-air-15-11.jpg?auto=webp&height=500']);
        $producto2->imagenes()->create(['ruta' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRGXYFch3pnLhH9hEnpkHSA2G8L6XqXCp1dKg&s']);
        $cupon = Cupon::firstOrCreate(
            ['codigo' => 'DESCUENTO10'],
            [
                'descuento' => 10.00,
                'limite_usos' => 100,
                'user_id' => $emprendedor->id,
            ]
        );
        $producto1->cupones()->sync([$cupon->id]);
    }
}
