<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'cliente', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'emprendedor', 'guard_name' => 'web']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function usuario_existente_puede_iniciar_sesion_con_google(): void
    {
        $user = User::factory()->create([
            'email' => 'existente@example.com',
            'google_id' => 'google123',
            'email_verified_at' => now(),
        ]);
        $user->assignRole('cliente');
        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200); 
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function usuario_nuevo_se_crea_con_google(): void
    {
        $user = User::factory()->create([
            'email' => 'nuevo@example.com',
            'google_id' => 'google999',
            'email_verified_at' => now(),
        ]);
        $user->assignRole('cliente');
        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
    }
}
