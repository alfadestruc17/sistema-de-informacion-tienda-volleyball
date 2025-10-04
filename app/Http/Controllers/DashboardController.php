<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Reservation;
use App\Models\Court;
use App\Exports\SalesReportExport;
use App\Exports\ReservationsReportExport;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:admin');
    }

    /**
     * Obtener KPIs principales del dashboard
     */
    public function kpis(Request $request): JsonResponse
    {
        $date = $request->get('date', Carbon::today()->toDateString());

        // Ventas del día
        $dailySales = Order::whereDate('created_at', $date)
                          ->where('estado_pago', true)
                          ->sum('total');

        // Reservas activas del día
        $activeReservations = Reservation::whereDate('fecha', $date)
                                       ->where('estado', 'confirmada')
                                       ->count();

        // Ingresos de la semana actual
        $weekStart = Carbon::parse($date)->startOfWeek();
        $weekEnd = Carbon::parse($date)->endOfWeek();
        $weeklyRevenue = Order::whereBetween('created_at', [$weekStart, $weekEnd])
                             ->where('estado_pago', true)
                             ->sum('total');

        // Total de reservas de la semana
        $weeklyReservations = Reservation::whereBetween('fecha', [$weekStart, $weekEnd])
                                        ->count();

        return response()->json([
            'daily_sales' => $dailySales,
            'active_reservations_today' => $activeReservations,
            'weekly_revenue' => $weeklyRevenue,
            'weekly_reservations' => $weeklyReservations,
            'date' => $date
        ]);
    }

    /**
     * Datos para calendario semanal con reservas por cancha
     */
    public function weeklyCalendar(Request $request): JsonResponse
    {
        $weekStart = $request->get('week_start', Carbon::now()->startOfWeek()->toDateString());
        $weekStart = Carbon::parse($weekStart)->startOfWeek();
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

        return response()->json([
            'week_start' => $weekStart->toDateString(),
            'week_end' => $weekEnd->toDateString(),
            'courts' => $calendarData
        ]);
    }

    /**
     * Productos más vendidos
     */
    public function topProducts(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 10);
        $period = $request->get('period', 'month'); // day, week, month

        $dateFrom = match($period) {
            'day' => Carbon::today(),
            'week' => Carbon::now()->startOfWeek(),
            'month' => Carbon::now()->startOfMonth(),
            default => Carbon::now()->startOfMonth()
        };

        $topProducts = OrderItem::selectRaw('product_id, products.nombre, SUM(cantidad) as total_vendido, SUM(cantidad * precio_unitario) as total_ingresos')
                               ->join('products', 'order_items.product_id', '=', 'products.id')
                               ->join('orders', 'order_items.order_id', '=', 'orders.id')
                               ->where('orders.estado_pago', true)
                               ->where('orders.created_at', '>=', $dateFrom)
                               ->groupBy('product_id', 'products.nombre')
                               ->orderBy('total_vendido', 'desc')
                               ->limit($limit)
                               ->get();

        return response()->json($topProducts);
    }

    /**
     * Ingresos por día de la semana actual
     */
    public function weeklyRevenue(Request $request): JsonResponse
    {
        $weekStart = $request->get('week_start', Carbon::now()->startOfWeek()->toDateString());
        $weekStart = Carbon::parse($weekStart)->startOfWeek();

        $weeklyData = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $weekStart->copy()->addDays($i);
            $revenue = Order::whereDate('created_at', $date)
                           ->where('estado_pago', true)
                           ->sum('total');

            $reservations = Reservation::whereDate('fecha', $date)->count();

            $weeklyData[] = [
                'date' => $date->toDateString(),
                'day_name' => $date->locale('es')->dayName,
                'revenue' => $revenue,
                'reservations' => $reservations
            ];
        }

        return response()->json($weeklyData);
    }

    /**
     * Estadísticas generales
     */
    public function stats(Request $request): JsonResponse
    {
        $period = $request->get('period', 'month');

        $dateFrom = match($period) {
            'week' => Carbon::now()->startOfWeek(),
            'month' => Carbon::now()->startOfMonth(),
            'year' => Carbon::now()->startOfYear(),
            default => Carbon::now()->startOfMonth()
        };

        $totalRevenue = Order::where('estado_pago', true)
                            ->where('created_at', '>=', $dateFrom)
                            ->sum('total');

        $totalOrders = Order::where('created_at', '>=', $dateFrom)->count();

        $totalReservations = Reservation::where('fecha', '>=', $dateFrom)->count();

        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        return response()->json([
            'period' => $period,
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'total_reservations' => $totalReservations,
            'average_order_value' => round($avgOrderValue, 2)
        ]);
    }

    /**
     * Exportar reporte de ventas
     */
    public function exportSales(Request $request)
    {
        $dateFrom = $request->get('date_from') ? Carbon::parse($request->get('date_from')) : null;
        $dateTo = $request->get('date_to') ? Carbon::parse($request->get('date_to')) : null;

        $fileName = 'reporte-ventas-' . ($dateFrom ? $dateFrom->format('Y-m-d') : 'inicio') . '-a-' . ($dateTo ? $dateTo->format('Y-m-d') : 'fin') . '.xlsx';

        return Excel::download(new SalesReportExport($dateFrom, $dateTo), $fileName);
    }

    /**
     * Exportar reporte de reservas
     */
    public function exportReservations(Request $request)
    {
        $dateFrom = $request->get('date_from') ? Carbon::parse($request->get('date_from')) : null;
        $dateTo = $request->get('date_to') ? Carbon::parse($request->get('date_to')) : null;

        $fileName = 'reporte-reservas-' . ($dateFrom ? $dateFrom->format('Y-m-d') : 'inicio') . '-a-' . ($dateTo ? $dateTo->format('Y-m-d') : 'fin') . '.xlsx';

        return Excel::download(new ReservationsReportExport($dateFrom, $dateTo), $fileName);
    }
}
