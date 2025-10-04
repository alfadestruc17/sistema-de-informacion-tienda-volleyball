<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Reservation;
use App\Models\Court;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Dashboard principal
     */
    public function index()
    {
        // KPIs
        $kpis = $this->getKPIs();

        // Datos para gráficas
        $weeklyRevenue = $this->getWeeklyRevenueData();
        $topProducts = $this->getTopProductsData();

        // Calendario semanal
        $weeklyCalendar = $this->getWeeklyCalendarData();

        // Estadísticas generales
        $stats = $this->getStatsData();

        return view('dashboard.index', compact(
            'kpis',
            'weeklyRevenue',
            'topProducts',
            'weeklyCalendar',
            'stats'
        ));
    }

    /**
     * Dashboard de administrador
     */
    public function admin()
    {
        $this->middleware('role:admin');

        // Misma lógica que index pero con más permisos
        return $this->index();
    }

    private function getKPIs()
    {
        $date = Carbon::today();

        return [
            'daily_sales' => Order::whereDate('created_at', $date)->where('estado_pago', true)->sum('total'),
            'active_reservations' => Reservation::whereDate('fecha', $date)->where('estado', 'confirmada')->count(),
            'weekly_revenue' => Order::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('estado_pago', true)->sum('total'),
            'weekly_reservations' => Reservation::whereBetween('fecha', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
        ];
    }

    private function getWeeklyRevenueData()
    {
        $data = [];
        $weekStart = Carbon::now()->startOfWeek();

        for ($i = 0; $i < 7; $i++) {
            $date = $weekStart->copy()->addDays($i);
            $revenue = Order::whereDate('created_at', $date)->where('estado_pago', true)->sum('total');
            $reservations = Reservation::whereDate('fecha', $date)->count();

            $data[] = [
                'date' => $date->format('Y-m-d'),
                'day_name' => $date->locale('es')->dayName,
                'revenue' => $revenue,
                'reservations' => $reservations
            ];
        }

        return $data;
    }

    private function getTopProductsData()
    {
        return OrderItem::selectRaw('product_id, products.nombre, SUM(cantidad) as total_vendido, SUM(cantidad * precio_unitario) as total_ingresos')
                       ->join('products', 'order_items.product_id', '=', 'products.id')
                       ->join('orders', 'order_items.order_id', '=', 'orders.id')
                       ->where('orders.estado_pago', true)
                       ->where('orders.created_at', '>=', Carbon::now()->startOfMonth())
                       ->groupBy('product_id', 'products.nombre')
                       ->orderBy('total_vendido', 'desc')
                       ->limit(10)
                       ->get()
                       ->toArray();
    }

    private function getWeeklyCalendarData()
    {
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = $weekStart->copy()->endOfWeek();

        $courts = Court::all();
        $calendarData = [];

        foreach ($courts as $court) {
            $reservations = Reservation::where('court_id', $court->id)
                                     ->whereBetween('fecha', [$weekStart, $weekEnd])
                                     ->with('user')
                                     ->get();

            $courtData = [
                'court' => $court,
                'reservations' => $reservations->map(function ($reservation) {
                    return [
                        'id' => $reservation->id,
                        'fecha' => $reservation->fecha,
                        'hora_inicio' => $reservation->hora_inicio,
                        'duracion_horas' => $reservation->duracion_horas,
                        'estado' => $reservation->estado,
                        'cliente' => $reservation->user->nombre,
                        'total' => $reservation->total_estimado
                    ];
                })
            ];

            $calendarData[] = $courtData;
        }

        return [
            'week_start' => $weekStart->toDateString(),
            'week_end' => $weekEnd->toDateString(),
            'courts' => $calendarData
        ];
    }

    private function getStatsData()
    {
        $dateFrom = Carbon::now()->startOfMonth();

        return [
            'total_revenue' => Order::where('estado_pago', true)->where('created_at', '>=', $dateFrom)->sum('total'),
            'total_orders' => Order::where('created_at', '>=', $dateFrom)->count(),
            'total_reservations' => Reservation::where('fecha', '>=', $dateFrom)->count(),
            'average_order_value' => Order::where('estado_pago', true)->where('created_at', '>=', $dateFrom)->avg('total') ?? 0
        ];
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
