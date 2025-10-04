<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Reservas de Voleibol</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .kpi-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .chart-container {
            position: relative;
            height: 300px;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-xl font-bold text-gray-800">🏐 Volleyball Booking</span>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700">Hola, {{ Auth::user()->nombre }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                            Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto p-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Dashboard Administrador</h1>
            <div class="flex gap-2">
                <a href="{{ route('admin.pos.index') }}" class="bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600">
                    🛒 POS
                </a>
                <a href="{{ route('admin.sales.index') }}" class="bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-600">
                    💰 Gestionar Ventas
                </a>
                <a href="{{ route('admin.reservations.index') }}" class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600">
                    📅 Gestionar Reservas
                </a>
                <a href="{{ route('dashboard.export.sales') }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                    📊 Exportar Ventas
                </a>
                <a href="{{ route('dashboard.export.reservations') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    📅 Exportar Reservas
                </a>
            </div>
        </div>

        <!-- KPIs Principales -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="kpi-card p-6 rounded-lg shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white text-sm opacity-80">Ventas del Día</p>
                        <p class="text-2xl font-bold">${{ number_format($kpis['daily_sales'], 2) }}</p>
                    </div>
                    <div class="text-4xl">💰</div>
                </div>
            </div>

            <div class="kpi-card p-6 rounded-lg shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white text-sm opacity-80">Reservas Activas</p>
                        <p class="text-2xl font-bold">{{ $kpis['active_reservations'] }}</p>
                    </div>
                    <div class="text-4xl">📅</div>
                </div>
            </div>

            <div class="kpi-card p-6 rounded-lg shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white text-sm opacity-80">Ingresos Semanales</p>
                        <p class="text-2xl font-bold">${{ number_format($kpis['weekly_revenue'], 2) }}</p>
                    </div>
                    <div class="text-4xl">📈</div>
                </div>
            </div>

            <div class="kpi-card p-6 rounded-lg shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white text-sm opacity-80">Reservas Semanales</p>
                        <p class="text-2xl font-bold">{{ $kpis['weekly_reservations'] }}</p>
                    </div>
                    <div class="text-4xl">🎾</div>
                </div>
            </div>
        </div>

        <!-- Gráficas -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Ingresos Semanales -->
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold mb-4">Ingresos Semanales</h3>
                <div class="chart-container">
                    <canvas id="weeklyRevenueChart"></canvas>
                </div>
            </div>

            <!-- Productos Más Vendidos -->
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold mb-4">Productos Más Vendidos</h3>
                <div class="chart-container">
                    <canvas id="topProductsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Calendario Semanal -->
        <div class="bg-white p-6 rounded-lg shadow-lg mb-8">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Calendario Semanal de Reservas</h3>
                <div class="flex gap-2">
                    <button onclick="previousWeek()" class="bg-gray-500 text-white px-3 py-1 rounded text-sm">← Semana Anterior</button>
                    <span class="font-semibold" id="week-range">{{ $weeklyCalendar['week_start'] }} - {{ $weeklyCalendar['week_end'] }}</span>
                    <button onclick="nextWeek()" class="bg-gray-500 text-white px-3 py-1 rounded text-sm">Semana Siguiente →</button>
                </div>
            </div>

            <div id="calendar-container" class="overflow-x-auto">
                {{ generateCalendarHTML($weeklyCalendar['courts']) }}
            </div>
        </div>

        <!-- Estadísticas Generales -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white p-6 rounded-lg shadow-lg text-center">
                <h4 class="text-lg font-semibold mb-2">Ingresos del Mes</h4>
                <p class="text-3xl font-bold text-green-600">${{ number_format($stats['total_revenue'], 2) }}</p>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-lg text-center">
                <h4 class="text-lg font-semibold mb-2">Total Órdenes</h4>
                <p class="text-3xl font-bold text-blue-600">{{ $stats['total_orders'] }}</p>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-lg text-center">
                <h4 class="text-lg font-semibold mb-2">Valor Promedio Orden</h4>
                <p class="text-3xl font-bold text-purple-600">${{ number_format($stats['average_order_value'], 2) }}</p>
            </div>
        </div>
    </div>

    <script>
        // Datos para las gráficas
        const weeklyRevenueData = @json($weeklyRevenue);
        const topProductsData = @json($topProducts);

        // Gráfica de ingresos semanales
        const ctxRevenue = document.getElementById('weeklyRevenueChart').getContext('2d');
        new Chart(ctxRevenue, {
            type: 'line',
            data: {
                labels: weeklyRevenueData.map(d => d.day_name),
                datasets: [{
                    label: 'Ingresos Diarios',
                    data: weeklyRevenueData.map(d => d.revenue),
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toFixed(2);
                            }
                        }
                    }
                }
            }
        });

        // Gráfica de productos más vendidos
        const ctxProducts = document.getElementById('topProductsChart').getContext('2d');
        new Chart(ctxProducts, {
            type: 'bar',
            data: {
                labels: topProductsData.map(p => p.nombre),
                datasets: [{
                    label: 'Unidades Vendidas',
                    data: topProductsData.map(p => p.total_vendido),
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Navegación de semanas (placeholder - requeriría AJAX)
        function previousWeek() {
            alert('Funcionalidad de navegación de semanas próximamente');
        }

        function nextWeek() {
            alert('Funcionalidad de navegación de semanas próximamente');
        }
    </script>

    @php
        function generateCalendarHTML($courts) {
            $days = ['Hora', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
            $hours = ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00'];

            $html = '<table class="w-full border-collapse border border-gray-300">';

            // Header con días
            $html .= '<thead><tr>';
            foreach ($days as $day) {
                $html .= '<th class="border border-gray-300 p-2 bg-gray-100 font-semibold">' . $day . '</th>';
            }
            $html .= '</tr></thead><tbody>';

            // Filas por hora
            foreach ($hours as $hour) {
                $html .= '<tr>';
                $html .= '<td class="border border-gray-300 p-2 bg-gray-50 font-medium">' . $hour . '</td>';

                // Celdas por día
                for ($i = 1; $i <= 7; $i++) {
                    $reservations = getReservationsForDayAndHour($courts, $i, $hour);
                    $cellClass = count($reservations) > 0 ? 'bg-red-100' : 'bg-green-50';

                    $html .= '<td class="border border-gray-300 p-2 ' . $cellClass . '">';
                    if (count($reservations) > 0) {
                        foreach ($reservations as $reservation) {
                            $html .= '<div class="text-xs mb-1 p-1 bg-red-200 rounded">';
                            $html .= htmlspecialchars($reservation['cliente']) . '<br>';
                            $html .= $reservation['duracion_horas'] . 'h - $' . number_format($reservation['total'], 2);
                            $html .= '</div>';
                        }
                    }
                    $html .= '</td>';
                }
                $html .= '</tr>';
            }

            $html .= '</tbody></table>';
            return $html;
        }

        function getReservationsForDayAndHour($courts, $dayIndex, $hour) {
            $reservations = [];
            foreach ($courts as $court) {
                foreach ($court['reservations'] as $reservation) {
                    $resDate = new DateTime($reservation['fecha']);
                    $resDay = $resDate->format('N'); // 1 = Lunes, 7 = Domingo
                    $resHour = substr($reservation['hora_inicio'], 0, 5);

                    if ($resDay == $dayIndex && $resHour == $hour) {
                        $reservations[] = $reservation;
                    }
                }
            }
            return $reservations;
        }
    @endphp
</body>
</html>