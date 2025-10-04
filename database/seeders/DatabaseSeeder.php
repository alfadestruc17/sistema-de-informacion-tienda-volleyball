<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(DashboardSeeder::class);

        // User::factory(10)->create();

        User::factory()->create([
            'nombre' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'rol_id' => 1, // admin
            'telefono' => '3001234567',
        ]);

        User::factory()->create([
            'nombre' => 'Cajero Test',
            'email' => 'cajero@example.com',
            'password' => bcrypt('password'),
            'rol_id' => 2, // cajero
            'telefono' => '3001234568',
        ]);

        User::factory()->create([
            'nombre' => 'Cliente Test',
            'email' => 'cliente@example.com',
            'password' => bcrypt('password'),
            'rol_id' => 3, // cliente
            'telefono' => '3001234569',
        ]);
    }
}
