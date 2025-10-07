<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Reserva #{{ $reservation->id }} - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-xl font-bold text-gray-800">🏐 Arena Sport C.B - Reserva #{{ $reservation->id }}</span>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700">Admin: {{ Auth::user()->nombre }}</span>
                    <a href="{{ route('admin.reservations.index') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        ← Volver
                    </a>
                    <a href="{{ route('admin.reservations.edit', $reservation) }}" class="bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-600">
                        ✏️ Editar
                    </a>
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
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Reserva #{{ $reservation->id }}</h1>
                <a href="{{ route('admin.reservations.edit', $reservation) }}" class="bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-600">
                    ✏️ Editar Reserva
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h3 class="text-lg font-semibold mb-3">Información General</h3>
                    <div class="space-y-2">
                        <p><strong>ID:</strong> #{{ $reservation->id }}</p>
                        <p><strong>Cliente:</strong> {{ $reservation->user->nombre }} ({{ $reservation->user->email }})</p>
                        <p><strong>Cancha:</strong> {{ $reservation->court->nombre }}</p>
                        <p><strong>Fecha:</strong> {{ $reservation->fecha->format('d/m/Y') }}</p>
                        <p><strong>Hora de Inicio:</strong> {{ $reservation->hora_inicio }}</p>
                        <p><strong>Duración:</strong> {{ $reservation->duracion_horas }} horas</p>
                        <p><strong>Estado:</strong>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($reservation->estado == 'confirmada') bg-green-100 text-green-800
                                @elseif($reservation->estado == 'pendiente') bg-yellow text-yellow
                                @else bg-red text-red @endif">
                                {{ ucfirst($reservation->estado) }}
                            </span>
                        </p>
                        <p><strong>Pagado:</strong>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($reservation->pagado_bool) bg-green text-green @else bg-red-100 text-red-800 @endif">
                                {{ $reservation->pagado_bool ? 'Sí' : 'No' }}
                            </span>
                        </p>
                        <p><strong>Fecha de Creación:</strong> {{ $reservation->created_at->format('d/m/Y H:i') }}</p>
                        <p><strong>Última Actualización:</strong> {{ $reservation->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-3">Información de Costos</h3>
                    <div class="bg-gray-50 p-4 rounded">
                        <div class="space-y-2">
                            <p><strong>Precio por Hora:</strong> ${{ number_format($reservation->court->precio_por_hora, 2) }}</p>
                            <p><strong>Duración:</strong> {{ $reservation->duracion_horas }} horas</p>
                            <p><strong>Total Estimado:</strong> ${{ number_format($reservation->total_estimado, 2) }}</p>
                        </div>
                    </div>

                    @if($reservation->orders->count() > 0)
                        <h4 class="text-md font-semibold mt-4 mb-2">Órdenes Asociadas</h4>
                        <div class="space-y-1">
                            @foreach($reservation->orders as $order)
                                <div class="bg-blue-50 p-2 rounded text-sm">
                                    <p><strong>Orden #{{ $order->id }}:</strong> ${{ number_format($order->total, 2) }}
                                    <span class="px-1 inline-flex text-xs leading-4 font-semibold rounded-full
                                        @if($order->estado_pago == 'pagado') bg-green-100 text-green-800
                                        @elseif($order->estado_pago == 'pendiente') bg-yellow text-yellow
                                        @else bg-red text-red @endif">
                                        {{ ucfirst($order->estado_pago) }}
                                    </span></p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            @if($reservation->reservationItems->count() > 0)
                <div>
                    <h3 class="text-lg font-semibold mb-3">Items Adicionales</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse border border-gray-300">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="border border-gray-300 px-4 py-2 text-left">Descripción</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center">Cantidad</th>
                                    <th class="border border-gray-300 px-4 py-2 text-right">Precio Unitario</th>
                                    <th class="border border-gray-300 px-4 py-2 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reservation->reservationItems as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="border border-gray-300 px-4 py-2">{{ $item->descripcion }}</td>
                                        <td class="border border-gray-300 px-4 py-2 text-center">{{ $item->cantidad }}</td>
                                        <td class="border border-gray-300 px-4 py-2 text-right">${{ number_format($item->precio_unitario, 2) }}</td>
                                        <td class="border border-gray-300 px-4 py-2 text-right">${{ number_format($item->cantidad * $item->precio_unitario, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</body>
</html>