<?php

namespace Database\Seeders;

use App\Models\Court;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DashboardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario admin si no existe
        $admin = User::where('email', 'admin@example.com')->first();
        if (!$admin) {
            $admin = User::create([
                'nombre' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'telefono' => '3000000000',
                'rol_id' => 1, // admin
            ]);
        }

        // Crear usuario cajero si no existe
        $cajero = User::where('email', 'cajero@example.com')->first();
        if (!$cajero) {
            $cajero = User::create([
                'nombre' => 'María Cajero',
                'email' => 'cajero@example.com',
                'password' => bcrypt('password'),
                'telefono' => '3002222222',
                'rol_id' => 2, // cajero
            ]);
        }

        // Crear usuario cliente si no existe
        $cliente = User::where('email', 'cliente@example.com')->first();
        if (!$cliente) {
            $cliente = User::create([
                'nombre' => 'Juan Cliente',
                'email' => 'cliente@example.com',
                'password' => bcrypt('password'),
                'telefono' => '3001111111',
                'rol_id' => 3, // cliente
            ]);
        }

        // Crear canchas si no existen
        $court1 = Court::firstOrCreate(
            ['nombre' => 'Cancha 1'],
            ['descripcion' => 'Cancha principal de voleibol', 'precio_por_hora' => 25.00, 'estado' => 'activo']
        );

        $court2 = Court::firstOrCreate(
            ['nombre' => 'Cancha 2'],
            ['descripcion' => 'Cancha secundaria de voleibol', 'precio_por_hora' => 20.00, 'estado' => 'activo']
        );

        // Crear algunas reservas de los últimos días
        $reservations = [
            [
                'user_id' => $cliente->id,
                'court_id' => $court1->id,
                'fecha' => Carbon::today()->subDays(2),
                'hora_inicio' => '14:00',
                'duracion_horas' => 2,
                'estado' => 'confirmada',
                'total_estimado' => 50.00,
                'pagado_bool' => true,
            ],
            [
                'user_id' => $cliente->id,
                'court_id' => $court2->id,
                'fecha' => Carbon::today()->subDays(1),
                'hora_inicio' => '16:00',
                'duracion_horas' => 1,
                'estado' => 'confirmada',
                'total_estimado' => 20.00,
                'pagado_bool' => true,
            ],
            [
                'user_id' => $cliente->id,
                'court_id' => $court1->id,
                'fecha' => Carbon::today(),
                'hora_inicio' => '10:00',
                'duracion_horas' => 2,
                'estado' => 'confirmada',
                'total_estimado' => 50.00,
                'pagado_bool' => false,
            ],
            [
                'user_id' => $cliente->id,
                'court_id' => $court1->id,
                'fecha' => Carbon::today()->addDays(1),
                'hora_inicio' => '18:00',
                'duracion_horas' => 1,
                'estado' => 'pendiente',
                'total_estimado' => 25.00,
                'pagado_bool' => false,
            ],
        ];

        foreach ($reservations as $reservationData) {
            Reservation::create($reservationData);
        }

        // Crear órdenes con items para las reservas pagadas
        $paidReservations = Reservation::where('pagado_bool', true)->get();

        foreach ($paidReservations as $reservation) {
            $order = Order::create([
                'user_id' => $reservation->user_id,
                'reservation_id' => $reservation->id,
                'total' => rand(15, 45),
                'estado_pago' => true,
            ]);

            // Agregar algunos productos aleatorios
            $products = Product::inRandomOrder()->limit(rand(1, 3))->get();
            foreach ($products as $product) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'cantidad' => rand(1, 3),
                    'precio_unitario' => $product->precio,
                ]);
            }

            // Recalcular total real
            $order->update(['total' => $order->calculateTotal()]);
        }
    }
}