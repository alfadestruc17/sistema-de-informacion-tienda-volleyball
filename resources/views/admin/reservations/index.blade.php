<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Reservas - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-xl font-bold text-gray-800">🏐 Arena Sport C.B - Reservas</span>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700">Admin: {{ Auth::user()->nombre }}</span>
                    <a href="{{ route('dashboard') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        ← Volver al Dashboard
                    </a>
                    <a href="{{ route('admin.reservations.create') }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                        ➕ Nueva Reserva
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
        <div class="mb-6">
            <h1 class="text-3xl font-bold">Gestión de Reservas</h1>
            <p class="text-gray-600">Administra todas las reservas del sistema</p>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cancha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha/Hora</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duración</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($reservations as $reservation)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    #{{ $reservation->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $reservation->user->nombre }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $reservation->court->nombre }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $reservation->fecha->format('d/m/Y') }}<br>
                                    <span class="text-xs text-gray-500">{{ $reservation->hora_inicio }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $reservation->duracion_horas }}h
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($reservation->estado == 'confirmada') bg-green-100 text-green-800
                                        @elseif($reservation->estado == 'pendiente') bg-yellow text-yellow
                                        @else bg-red text-red @endif">
                                        {{ ucfirst($reservation->estado) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ${{ number_format($reservation->total_estimado, 2) }}
                                    @if($reservation->pagado_bool)
                                        <span class="text-green-600 text-xs">✓</span>
                                    @else
                                        <span class="text-red-600 text-xs">✗</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('admin.reservations.show', $reservation) }}" class="text-blue-600 hover:text-blue-900 mr-3">Ver</a>
                                    <a href="{{ route('admin.reservations.edit', $reservation) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</a>
                                    @if($reservation->estado != 'confirmada' || !$reservation->pagado_bool)
                                        <form method="POST" action="{{ route('admin.reservations.destroy', $reservation) }}" class="inline"
                                              onsubmit="return confirm('¿Estás seguro de eliminar esta reserva?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                    No hay reservas registradas
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($reservations->hasPages())
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $reservations->links() }}
                </div>
            @endif
        </div>
    </div>
</body>
</html>