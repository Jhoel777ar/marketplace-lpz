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

        $producto3 = Producto::firstOrCreate(
            ['nombre' => 'zapatos'],
            [
                'descripcion' => 'muy buenos',
                'precio' => 20.00,
                'publico' => true,
                'stock' => 50,
                'emprendedor_id' => $emprendedor->id,
            ]
        );

        $producto4 = Producto::firstOrCreate(
            ['nombre' => 'pantalones'],
            [
                'descripcion' => 'muy buenos',
                'precio' => 20.00,
                'publico' => true,
                'stock' => 50,
                'emprendedor_id' => $emprendedor->id,
            ]
        );

        $producto5 = Producto::firstOrCreate(
            ['nombre' => 'corbata'],
            [
                'descripcion' => 'muy buenos',
                'precio' => 20.00,
                'publico' => true,
                'stock' => 50,
                'emprendedor_id' => $emprendedor->id,
            ]
        );
        
        $producto6 = Producto::firstOrCreate(
            ['nombre' => 'guantes'],
            [
                'descripcion' => 'muy buenos',
                'precio' => 20.00,
                'publico' => true,
                'stock' => 50,
                'emprendedor_id' => $emprendedor->id,
            ]
        );

        $producto7 = Producto::firstOrCreate(
            ['nombre' => 'zapatillas'],
            [
                'descripcion' => 'muy buenos',
                'precio' => 20.00,
                'publico' => true,
                'stock' => 50,
                'emprendedor_id' => $emprendedor->id,
            ]
        );

        $producto8 = Producto::firstOrCreate(
            ['nombre' => 'bufanda'],
            [
                'descripcion' => 'muy buenos',
                'precio' => 20.00,
                'publico' => true,
                'stock' => 50,
                'emprendedor_id' => $emprendedor->id,
            ]
        );

        $producto9 = Producto::firstOrCreate(
            ['nombre' => 'Gorro negro'],
            [
                'descripcion' => 'muy buenos',
                'precio' => 20.00,
                'publico' => true,
                'stock' => 50,
                'emprendedor_id' => $emprendedor->id,
            ]
        );

        $producto10 = Producto::firstOrCreate(
            ['nombre' => 'Camisa azul'],
            [
                'descripcion' => 'muy buenos',
                'precio' => 20.00,
                'publico' => true,
                'stock' => 50,
                'emprendedor_id' => $emprendedor->id,
            ]
        );

        $producto11 = Producto::firstOrCreate(
            ['nombre' => 'Camisa roja'],
            [
                'descripcion' => 'muy buenos',
                'precio' => 20.00,
                'publico' => true,
                'stock' => 50,
                'emprendedor_id' => $emprendedor->id,
            ]
        );

        $producto1->categorias()->sync([Categoria::where('nombre', 'Tecnología')->first()->id]);
        $producto2->categorias()->sync([Categoria::where('nombre', 'Ropa')->first()->id]);
        $producto3->categorias()->sync([Categoria::where('nombre', 'Ropa')->first()->id]);
        $producto4->categorias()->sync([Categoria::where('nombre', 'Ropa')->first()->id]);
        $producto5->categorias()->sync([Categoria::where('nombre', 'Ropa')->first()->id]);
        $producto6->categorias()->sync([Categoria::where('nombre', 'Ropa')->first()->id]);
        $producto7->categorias()->sync([Categoria::where('nombre', 'Ropa')->first()->id]);
        $producto8->categorias()->sync([Categoria::where('nombre', 'Ropa')->first()->id]);
        $producto9->categorias()->sync([Categoria::where('nombre', 'Ropa')->first()->id]);
        $producto10->categorias()->sync([Categoria::where('nombre', 'Ropa')->first()->id]);
        $producto11->categorias()->sync([Categoria::where('nombre', 'Ropa')->first()->id]);
        $producto1->imagenes()->create(['ruta' => 'https://www.cnet.com/a/img/resize/bb8a2aa9c31f8ec08d82228a51eabf05f00e54d2/hub/2025/03/10/d190e21d-9634-440d-8f33-396c8cb3da6a/m4-macbook-air-15-11.jpg?auto=webp&height=500']);
        $producto2->imagenes()->create(['ruta' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRGXYFch3pnLhH9hEnpkHSA2G8L6XqXCp1dKg&s']);
        $producto3->imagenes()->create(['ruta' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT5qglXWTgu72oG96n0i-Pv1u5RpxRrQw8xGnK0odTIkIWILE86fA0az-aVdvYW2RyCjwg&usqp=CAU']);
        $producto4->imagenes()->create(['ruta' => 'https://media.istockphoto.com/id/173239968/es/foto/fino-herm%C3%A9tico-jeans-azul-sobre-fondo-blanco.jpg?s=612x612&w=0&k=20&c=owv-YVYZRxIGPAvwkFWc3p5GgFgC8kAssFc1NeWxv0c=']);
        $producto5->imagenes()->create(['ruta' => 'https://i.pinimg.com/736x/55/17/81/551781fd7c5dab6ac4d372bcb4142d8b.jpg']);
        $producto6->imagenes()->create(['ruta' => 'https://i5.walmartimages.cl/asr/5ac1ef5b-2ff0-47dc-bd96-584e20bd6ead.e950363e5d657e34a8bdff848ad95007.jpeg?odnHeight=2000&odnWidth=2000&odnBg=ffffff']);
        $producto7->imagenes()->create(['ruta' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSWLeQ_9AjRos-xy2Q3fTl-qCfIFkg3W7HapA&s']);
        $producto8->imagenes()->create(['ruta' => 'https://uesti.es/480-large_default/bufanda-trenzada-.jpg']);
        $producto9->imagenes()->create(['ruta' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQWa6DgCBZzzvexr8ZWLfDvAb1pW3ona520EA&s']);
        $producto10->imagenes()->create(['ruta' => 'https://m.media-amazon.com/images/I/519-6fgzdQL._UY1000_.jpg']);
        $producto11->imagenes()->create(['ruta' => 'https://png.pngtree.com/png-clipart/20240819/original/pngtree-modern-men-s-red-shirt-outfit-png-image_15804740.png']);
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
