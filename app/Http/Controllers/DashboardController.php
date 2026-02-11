<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exports\ReservationsReportExport;
use App\Exports\SalesReportExport;
use App\Services\DashboardService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService
    ) {
        $this->middleware('auth:sanctum');
        $this->middleware('role:admin');
    }

    public function kpis(Request $request): JsonResponse
    {
        $date = $request->get('date', Carbon::today()->toDateString());
        return response()->json($this->dashboardService->getKpisForApi($date));
    }

    public function weeklyCalendar(Request $request): JsonResponse
    {
        $weekStart = $request->get('week_start', Carbon::now()->startOfWeek()->toDateString());
        $data = $this->dashboardService->getWeeklyCalendarForApi($weekStart);
        return response()->json([
            'week_start' => $data['week_start'],
            'week_end' => $data['week_end'],
            'courts' => $data['courts'],
        ]);
    }

    public function topProducts(Request $request): JsonResponse
    {
        $limit = (int) $request->get('limit', 10);
        $period = $request->get('period', 'month');
        return response()->json($this->dashboardService->getTopProductsForApi($limit, $period));
    }

    public function weeklyRevenue(Request $request): JsonResponse
    {
        $weekStart = $request->get('week_start', Carbon::now()->startOfWeek()->toDateString());
        return response()->json($this->dashboardService->getWeeklyRevenueForApi($weekStart));
    }

    public function stats(Request $request): JsonResponse
    {
        $period = $request->get('period', 'month');
        return response()->json($this->dashboardService->getStatsForApi($period));
    }

    public function exportSales(Request $request): BinaryFileResponse
    {
        $dateFrom = $request->get('date_from') ? Carbon::parse($request->get('date_from')) : null;
        $dateTo = $request->get('date_to') ? Carbon::parse($request->get('date_to')) : null;
        $fileName = 'reporte-ventas-' . ($dateFrom ? $dateFrom->format('Y-m-d') : 'inicio') . '-a-' . ($dateTo ? $dateTo->format('Y-m-d') : 'fin') . '.xlsx';
        return Excel::download(new SalesReportExport($dateFrom, $dateTo), $fileName);
    }

    public function exportReservations(Request $request): BinaryFileResponse
    {
        $dateFrom = $request->get('date_from') ? Carbon::parse($request->get('date_from')) : null;
        $dateTo = $request->get('date_to') ? Carbon::parse($request->get('date_to')) : null;
        $fileName = 'reporte-reservas-' . ($dateFrom ? $dateFrom->format('Y-m-d') : 'inicio') . '-a-' . ($dateTo ? $dateTo->format('Y-m-d') : 'fin') . '.xlsx';
        return Excel::download(new ReservationsReportExport($dateFrom, $dateTo), $fileName);
    }
}
