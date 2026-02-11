<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\ReservationRepositoryInterface;
use App\Repositories\Contracts\SaleRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DashboardService
{
    public function __construct(
        private SaleRepositoryInterface $saleRepository,
        private ReservationRepositoryInterface $reservationRepository,
        private ReservationService $reservationService,
        private OrderRepositoryInterface $orderRepository
    ) {
    }

    /**
     * KPIs para vista web (usa Sales).
     *
     * @return array{daily_sales: float, active_reservations: int, weekly_revenue: float, weekly_reservations: int}
     */
    public function getKpisForWeb(): array
    {
        $date = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();

        return [
            'daily_sales' => $this->saleRepository->getDailyTotal($date),
            'active_reservations' => \App\Models\Reservation::whereDate('fecha', $date)->where('estado', 'confirmada')->count(),
            'weekly_revenue' => $this->saleRepository->getWeeklyTotal($weekStart, $weekEnd),
            'weekly_reservations' => $this->reservationRepository->countByDateRange($weekStart->toDateString(), $weekEnd->toDateString()),
        ];
    }

    /**
     * Datos de ingresos por día de la semana actual (web).
     *
     * @return array<int, array{date: string, day_name: string, revenue: float, reservations: int}>
     */
    public function getWeeklyRevenueDataForWeb(): array
    {
        $weekStart = Carbon::now()->startOfWeek();
        $data = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $weekStart->copy()->addDays($i);
            $revenue = $this->saleRepository->getDailyTotal($date);
            $reservations = \App\Models\Reservation::whereDate('fecha', $date)->count();

            $data[] = [
                'date' => $date->format('Y-m-d'),
                'day_name' => $date->locale('es')->dayName,
                'revenue' => $revenue,
                'reservations' => $reservations,
            ];
        }

        return $data;
    }

    /**
     * Top productos más vendidos (web, por ventas Sales).
     *
     * @return array<int, object>
     */
    public function getTopProductsForWeb(int $limit = 10): array
    {
        return $this->saleRepository
            ->getTopProductsByQuantity($limit, Carbon::now()->startOfMonth())
            ->toArray();
    }

    /**
     * Calendario semanal para dashboard web.
     *
     * @return array{week_start: string, week_end: string, courts: array}
     */
    public function getWeeklyCalendarForWeb(): array
    {
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = $weekStart->copy()->endOfWeek();

        return $this->reservationService->getWeeklyCalendarData(
            $weekStart->toDateString(),
            $weekEnd->toDateString()
        );
    }

    /**
     * Estadísticas del mes actual (web).
     *
     * @return array{total_revenue: float, total_sales: int, total_reservations: int, average_sale_value: float}
     */
    public function getStatsForWeb(): array
    {
        $dateFrom = Carbon::now()->startOfMonth();

        $totalRevenue = $this->saleRepository->getMonthlyTotal($dateFrom);
        $totalSales = \App\Models\Sale::where('created_at', '>=', $dateFrom)->count();
        $totalReservations = \App\Models\Reservation::where('fecha', '>=', $dateFrom)->count();
        $avgSale = $totalSales > 0 ? $totalRevenue / $totalSales : 0;

        return [
            'total_revenue' => $totalRevenue,
            'total_sales' => $totalSales,
            'total_reservations' => $totalReservations,
            'average_sale_value' => round((float) $avgSale, 2),
        ];
    }

    // --- Métodos para API (usan Order en lugar de Sale) ---

    /**
     * KPIs para API.
     *
     * @return array{daily_sales: float, active_reservations_today: int, weekly_revenue: float, weekly_reservations: int, date: string}
     */
    public function getKpisForApi(string $date): array
    {
        $dateCarbon = Carbon::parse($date);
        $weekStart = $dateCarbon->copy()->startOfWeek();
        $weekEnd = $dateCarbon->copy()->endOfWeek();

        return [
            'daily_sales' => $this->orderRepository->getDailyTotal($dateCarbon),
            'active_reservations_today' => \App\Models\Reservation::whereDate('fecha', $date)->where('estado', 'confirmada')->count(),
            'weekly_revenue' => $this->orderRepository->getWeeklyTotal($weekStart, $weekEnd),
            'weekly_reservations' => $this->reservationRepository->countByDateRange($weekStart->toDateString(), $weekEnd->toDateString()),
            'date' => $date,
        ];
    }

    /**
     * Calendario semanal para API (misma estructura que web).
     */
    public function getWeeklyCalendarForApi(string $weekStart): array
    {
        $weekStartCarbon = Carbon::parse($weekStart)->startOfWeek();
        $weekEnd = $weekStartCarbon->copy()->endOfWeek()->toDateString();

        return $this->reservationService->getWeeklyCalendarData(
            $weekStartCarbon->toDateString(),
            $weekEnd
        );
    }

    /**
     * Top productos por ordenes (API).
     *
     * @return Collection<int, object>
     */
    public function getTopProductsForApi(int $limit, string $period): Collection
    {
        $dateFrom = match ($period) {
            'day' => Carbon::today(),
            'week' => Carbon::now()->startOfWeek(),
            'month' => Carbon::now()->startOfMonth(),
            default => Carbon::now()->startOfMonth(),
        };

        return \App\Models\OrderItem::selectRaw('product_id, products.nombre, SUM(cantidad) as total_vendido, SUM(cantidad * precio_unitario) as total_ingresos')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.estado_pago', true)
            ->where('orders.created_at', '>=', $dateFrom)
            ->groupBy('product_id', 'products.nombre')
            ->orderBy('total_vendido', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Ingresos por día de la semana (API).
     *
     * @return array<int, array{date: string, day_name: string, revenue: float, reservations: int}>
     */
    public function getWeeklyRevenueForApi(string $weekStart): array
    {
        $weekStartCarbon = Carbon::parse($weekStart)->startOfWeek();
        $data = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $weekStartCarbon->copy()->addDays($i);
            $revenue = $this->orderRepository->getDailyTotal($date);
            $reservations = \App\Models\Reservation::whereDate('fecha', $date)->count();
            $data[] = [
                'date' => $date->toDateString(),
                'day_name' => $date->locale('es')->dayName,
                'revenue' => $revenue,
                'reservations' => $reservations,
            ];
        }

        return $data;
    }

    /**
     * Estadísticas generales API.
     *
     * @return array{period: string, total_revenue: float, total_orders: int, total_reservations: int, average_order_value: float}
     */
    public function getStatsForApi(string $period): array
    {
        $dateFrom = match ($period) {
            'week' => Carbon::now()->startOfWeek(),
            'month' => Carbon::now()->startOfMonth(),
            'year' => Carbon::now()->startOfYear(),
            default => Carbon::now()->startOfMonth(),
        };

        $totalRevenue = $this->orderRepository->getMonthlyTotal($dateFrom);
        $totalOrders = \App\Models\Order::where('created_at', '>=', $dateFrom)->count();
        $totalReservations = \App\Models\Reservation::where('fecha', '>=', $dateFrom)->count();
        $avgOrder = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        return [
            'period' => $period,
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'total_reservations' => $totalReservations,
            'average_order_value' => round($avgOrder, 2),
        ];
    }
}
