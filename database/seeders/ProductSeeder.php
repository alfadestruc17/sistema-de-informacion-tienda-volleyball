<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'nombre' => 'Coca Cola',
                'categoria' => 'Bebidas',
                'precio' => 3.50,
                'stock' => 50,
            ],
            [
                'nombre' => 'Pepsi',
                'categoria' => 'Bebidas',
                'precio' => 3.00,
                'stock' => 45,
            ],
            [
                'nombre' => 'Agua Mineral',
                'categoria' => 'Bebidas',
                'precio' => 2.00,
                'stock' => 60,
            ],
            [
                'nombre' => 'Papas Fritas',
                'categoria' => 'Snacks',
                'precio' => 4.50,
                'stock' => 30,
            ],
            [
                'nombre' => 'Hamburguesa',
                'categoria' => 'Comida',
                'precio' => 8.00,
                'stock' => 20,
            ],
            [
                'nombre' => 'Pelota de Voleibol',
                'categoria' => 'Equipo',
                'precio' => 15.00,
                'stock' => 10,
            ],
            [
                'nombre' => 'Red de Voleibol',
                'categoria' => 'Equipo',
                'precio' => 25.00,
                'stock' => 5,
            ],
            [
                'nombre' => 'Jugo de Naranja',
                'categoria' => 'Bebidas',
                'precio' => 3.50,
                'stock' => 40,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
