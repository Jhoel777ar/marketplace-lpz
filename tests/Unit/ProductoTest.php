<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Producto;
use App\Models\User;
use App\Models\Categoria;
use App\Models\Cupon;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_puede_crear_producto_y_persistir_en_bd(): void
    {
        $user = User::factory()->create();

        $producto = Producto::create([
            'nombre' => 'Producto Test',
            'descripcion' => 'Descripción de prueba',
            'precio' => 150.50,
            'destacado' => true,
            'publico' => true,
            'stock' => 10,
            'emprendedor_id' => $user->id,
        ]);

        $this->assertDatabaseHas('productos', [
            'nombre' => 'Producto Test',
            'precio' => 150.50,
            'emprendedor_id' => $user->id,
        ]);
    }

    public function test_puede_asignar_categorias_y_cupones(): void
    {
        $user = User::factory()->create();
        $producto = Producto::factory()->create(['emprendedor_id' => $user->id]);

        $categoria = Categoria::factory()->create();
        $cupon = Cupon::factory()->create(['user_id' => $user->id]);

        $producto->categorias()->attach($categoria->id);
        $producto->cupon()->attach($cupon->id);

        $this->assertTrue($producto->categorias->contains($categoria));
        $this->assertTrue($producto->cupon->contains($cupon));
    }

    public function test_scope_owned_by_funciona_correctamente(): void
    {
        $user = User::factory()->create();
        $otroUser = User::factory()->create();

        $producto1 = Producto::factory()->create(['emprendedor_id' => $user->id]);
        $producto2 = Producto::factory()->create(['emprendedor_id' => $otroUser->id]);

        $productosDelUser = Producto::ownedBy($user->id)->get();

        $this->assertTrue($productosDelUser->contains($producto1));
        $this->assertFalse($productosDelUser->contains($producto2));
    }
}
