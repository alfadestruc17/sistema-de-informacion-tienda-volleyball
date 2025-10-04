<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CashierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario cajero si no existe
        if (!User::where('email', 'cajero@example.com')->exists()) {
            User::create([
                'nombre' => 'Cajero Test',
                'email' => 'cajero@example.com',
                'password' => bcrypt('password'),
                'rol_id' => 2, // cajero
                'telefono' => '3001234568',
            ]);
        }
    }
}