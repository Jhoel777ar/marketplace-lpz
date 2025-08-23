<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password123'),
            ]
        );
        $admin->assignRole('admin');

        $emprendedor = User::firstOrCreate(
            ['email' => 'emprendedor@example.com'],
            [
                'name' => 'Emprendedor Demo',
                'password' => Hash::make('password123'),
            ]
        );
        $emprendedor->assignRole('emprendedor');

        $cliente = User::firstOrCreate(
            ['email' => 'cliente@example.com'],
            [
                'name' => 'Cliente Demo',
                'password' => Hash::make('password123'),
            ]
        );
        $cliente->assignRole('cliente');
    }
}
