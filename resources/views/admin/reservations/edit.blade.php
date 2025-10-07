<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Reserva #{{ $reservation->id }} - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-xl font-bold text-gray-800">🏐 Arena Sport C.B - Editar Reserva #{{ $reservation->id }}</span>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700">Admin: {{ Auth::user()->nombre }}</span>
                    <a href="{{ route('admin.reservations.show', $reservation) }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        ← Volver
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
            <h1 class="text-2xl font-bold mb-6">Editar Reserva #{{ $reservation->id }}</h1>

            <form method="POST" action="{{ route('admin.reservations.update', $reservation) }}">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">Cliente *</label>
                        <select name="user_id" id="user_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ $reservation->user_id == $user->id ? 'selected' : '' }}>
                                    {{ $user->nombre }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="court_id" class="block text-sm font-medium text-gray-700 mb-2">Cancha *</label>
                        <select name="court_id" id="court_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            @foreach($courts as $court)
                                <option value="{{ $court->id }}" {{ $reservation->court_id == $court->id ? 'selected' : '' }}>
                                    {{ $court->nombre }} - ${{ $court->precio_por_hora }}/hora
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="fecha" class="block text-sm font-medium text-gray-700 mb-2">Fecha *</label>
                        <input type="date" name="fecha" id="fecha" value="{{ $reservation->fecha->format('Y-m-d') }}"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>

                    <div>
                        <label for="hora_inicio" class="block text-sm font-medium text-gray-700 mb-2">Hora de Inicio *</label>
                        <select name="hora_inicio" id="hora_inicio" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            @for($hour = 8; $hour <= 20; $hour++)
                                @for($minute = 0; $minute < 60; $minute += 30)
                                    <option value="{{ sprintf('%02d:%02d', $hour, $minute) }}"
                                            {{ $reservation->hora_inicio == sprintf('%02d:%02d', $hour, $minute) ? 'selected' : '' }}>
                                        {{ sprintf('%02d:%02d', $hour, $minute) }}
                                    </option>
                                @endfor
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label for="duracion_horas" class="block text-sm font-medium text-gray-700 mb-2">Duración (horas) *</label>
                        <select name="duracion_horas" id="duracion_horas" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            @for($i = 1; $i <= 8; $i++)
                                <option value="{{ $i }}" {{ $reservation->duracion_horas == $i ? 'selected' : '' }}>
                                    {{ $i }} hora{{ $i > 1 ? 's' : '' }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label for="estado" class="block text-sm font-medium text-gray-700 mb-2">Estado *</label>
                        <select name="estado" id="estado" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="pendiente" {{ $reservation->estado == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="confirmada" {{ $reservation->estado == 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                            <option value="cancelada" {{ $reservation->estado == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                        </select>
                    </div>

                    <div>
                        <label for="pagado_bool" class="block text-sm font-medium text-gray-700 mb-2">¿Pagado?</label>
                        <div class="flex items-center">
                            <input type="checkbox" name="pagado_bool" id="pagado_bool" value="1"
                                   {{ $reservation->pagado_bool ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="pagado_bool" class="ml-2 block text-sm text-gray-900">
                                Marcar como pagado
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <div class="bg-gray-50 p-4 rounded">
                        <h3 class="text-lg font-semibold mb-2">Resumen de Costos</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Precio por hora:</p>
                                <p id="precio-hora" class="font-semibold">$0.00</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Total estimado:</p>
                                <p id="total-estimado" class="font-semibold text-lg text-green-600">$0.00</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('admin.reservations.show', $reservation) }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                        Cancelar
                    </a>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        💾 Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Datos de canchas
        const courts = @json($courts);

        // Actualizar precios cuando cambie la cancha
        document.getElementById('court_id').addEventListener('change', updatePrices);
        document.getElementById('duracion_horas').addEventListener('change', updatePrices);

        function updatePrices() {
            const courtId = document.getElementById('court_id').value;
            const duracion = parseInt(document.getElementById('duracion_horas').value) || 0;

            const court = courts.find(c => c.id == courtId);
            if (court) {
                const precioHora = parseFloat(court.precio_por_hora);
                const total = precioHora * duracion;

                document.getElementById('precio-hora').textContent = `$${precioHora.toFixed(2)}`;
                document.getElementById('total-estimado').textContent = `$${total.toFixed(2)}`;
            } else {
                document.getElementById('precio-hora').textContent = '$0.00';
                document.getElementById('total-estimado').textContent = '$0.00';
            }
        }

        // Inicializar precios con valores actuales
        document.addEventListener('DOMContentLoaded', function() {
            updatePrices();
        });
    </script>
</body>
</html>