@extends('layouts.app')

@section('title', 'Dashboard')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-slate-800">Dashboard</h1>
    <p class="text-slate-600 mt-1">Resumen del negocio</p>
</div>

{{-- KPIs con toque de color sutil (borde izquierdo) --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white border border-slate-200 border-l-4 border-l-sky-500 rounded-lg shadow-sm p-6">
        <p class="text-sm text-slate-500">Ventas del día</p>
        <p class="text-xl font-semibold text-slate-800 mt-1">${{ number_format($kpis['daily_sales'], 2) }}</p>
    </div>
    <div class="bg-white border border-slate-200 border-l-4 border-l-emerald-500 rounded-lg shadow-sm p-6">
        <p class="text-sm text-slate-500">Reservas activas</p>
        <p class="text-xl font-semibold text-slate-800 mt-1">{{ $kpis['active_reservations'] }}</p>
    </div>
    <div class="bg-white border border-slate-200 border-l-4 border-l-amber-500 rounded-lg shadow-sm p-6">
        <p class="text-sm text-slate-500">Ingresos semanales</p>
        <p class="text-xl font-semibold text-slate-800 mt-1">${{ number_format($kpis['weekly_revenue'], 2) }}</p>
    </div>
    <div class="bg-white border border-slate-200 border-l-4 border-l-sky-500 rounded-lg shadow-sm p-6">
        <p class="text-sm text-slate-500">Reservas semanales</p>
        <p class="text-xl font-semibold text-slate-800 mt-1">{{ $kpis['weekly_reservations'] }}</p>
    </div>
</div>

{{-- Gráficas --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white border border-slate-200 rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-slate-800 mb-4 border-l-2 border-sky-400 pl-2">Ingresos semanales</h3>
        <div class="h-64">
            <canvas id="weeklyRevenueChart"></canvas>
        </div>
    </div>
    <div class="bg-white border border-slate-200 rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-slate-800 mb-4 border-l-2 border-emerald-400 pl-2">Productos más vendidos</h3>
        <div class="h-64">
            <canvas id="topProductsChart"></canvas>
        </div>
    </div>
</div>

{{-- Calendario semanal --}}
<div class="bg-white border border-slate-200 rounded-lg shadow-sm p-6 mb-8">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-slate-800 border-l-2 border-amber-400 pl-2">Calendario semanal</h3>
        <span class="text-sm text-slate-600" id="week-range">{{ $weeklyCalendar['week_start'] }} - {{ $weeklyCalendar['week_end'] }}</span>
    </div>
    <div id="calendar-container" class="overflow-x-auto">
        @include('partials.calendar-week', ['courts' => $weeklyCalendar['courts'], 'weeklyCalendar' => $weeklyCalendar])
    </div>
</div>

{{-- Estadísticas --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="bg-white border border-slate-200 rounded-lg shadow-sm p-6 text-center bg-gradient-to-br from-sky-50/50 to-white">
        <p class="text-sm text-slate-500">Ingresos del mes</p>
        <p class="text-2xl font-semibold text-sky-700 mt-1">${{ number_format($stats['total_revenue'], 2) }}</p>
    </div>
    <div class="bg-white border border-slate-200 rounded-lg shadow-sm p-6 text-center bg-gradient-to-br from-emerald-50/50 to-white">
        <p class="text-sm text-slate-500">Total ventas</p>
        <p class="text-2xl font-semibold text-emerald-700 mt-1">{{ $stats['total_sales'] }}</p>
    </div>
    <div class="bg-white border border-slate-200 rounded-lg shadow-sm p-6 text-center bg-gradient-to-br from-amber-50/50 to-white">
        <p class="text-sm text-slate-500">Valor promedio venta</p>
        <p class="text-2xl font-semibold text-amber-700 mt-1">${{ number_format($stats['average_sale_value'], 2) }}</p>
    </div>
</div>

<script>
const weeklyRevenueData = @json($weeklyRevenue);
const topProductsData = @json($topProducts);

new Chart(document.getElementById('weeklyRevenueChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: weeklyRevenueData.map(d => d.day_name),
        datasets: [{
            label: 'Ingresos',
            data: weeklyRevenueData.map(d => d.revenue),
            borderColor: 'rgb(14, 165, 233)',
            backgroundColor: 'rgba(14, 165, 233, 0.15)',
            tension: 0.2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: { beginAtZero: true, ticks: { callback: v => '$' + Number(v).toFixed(0) } }
        }
    }
});

new Chart(document.getElementById('topProductsChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: topProductsData.map(p => p.nombre),
        datasets: [{
            label: 'Unidades',
            data: topProductsData.map(p => p.total_vendido),
            backgroundColor: 'rgba(16, 185, 129, 0.5)',
            borderColor: 'rgb(16, 185, 129)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: { y: { beginAtZero: true } }
    }
});
</script>
@endsection
