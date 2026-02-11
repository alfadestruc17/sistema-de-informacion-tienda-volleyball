<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Services\DashboardService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService
    ) {
        $this->middleware('auth');
    }

    public function index(): View|\Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();

        if ($user->role->nombre === 'cliente') {
            return redirect()->route('client.calendar');
        }

        $kpis = $this->dashboardService->getKpisForWeb();
        $weeklyRevenue = $this->dashboardService->getWeeklyRevenueDataForWeb();
        $topProducts = $this->dashboardService->getTopProductsForWeb();
        $weeklyCalendar = $this->dashboardService->getWeeklyCalendarForWeb();
        $stats = $this->dashboardService->getStatsForWeb();

        return view('dashboard.index', compact('kpis', 'weeklyRevenue', 'topProducts', 'weeklyCalendar', 'stats'));
    }

    public function admin(): View
    {
        $kpis = $this->dashboardService->getKpisForWeb();
        $weeklyRevenue = $this->dashboardService->getWeeklyRevenueDataForWeb();
        $topProducts = $this->dashboardService->getTopProductsForWeb();
        $weeklyCalendar = $this->dashboardService->getWeeklyCalendarForWeb();
        $stats = $this->dashboardService->getStatsForWeb();

        return view('dashboard.index', compact('kpis', 'weeklyRevenue', 'topProducts', 'weeklyCalendar', 'stats'));
    }
}
