<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nueva Reserva - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-xl font-bold text-gray-800">🏐 Volleyball Booking - Nueva Reserva</span>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700">Admin: {{ Auth::user()->nombre }}</span>
                    <a href="{{ route('admin.reservations.index') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
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
            <h1 class="text-2xl font-bold mb-6">Crear Nueva Reserva</h1>

            <form method="POST" action="{{ route('admin.reservations.store') }}">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">Cliente *</label>
                        <select name="user_id" id="user_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="">Seleccionar cliente...</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->nombre }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="court_id" class="block text-sm font-medium text-gray-700 mb-2">Cancha *</label>
                        <select name="court_id" id="court_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="">Seleccionar cancha...</option>
                            @foreach($courts as $court)
                                <option value="{{ $court->id }}">{{ $court->nombre }} - ${{ $court->precio_por_hora }}/hora</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="fecha" class="block text-sm font-medium text-gray-700 mb-2">Fecha *</label>
                        <input type="date" name="fecha" id="fecha" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               min="{{ date('Y-m-d') }}" required>
                    </div>

                    <div>
                        <label for="hora_inicio" class="block text-sm font-medium text-gray-700 mb-2">Hora de Inicio *</label>
                        <select name="hora_inicio" id="hora_inicio" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="">Seleccionar hora...</option>
                            @for($hour = 8; $hour <= 20; $hour++)
                                @for($minute = 0; $minute < 60; $minute += 30)
                                    <option value="{{ sprintf('%02d:%02d', $hour, $minute) }}">
                                        {{ sprintf('%02d:%02d', $hour, $minute) }}
                                    </option>
                                @endfor
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label for="duracion_horas" class="block text-sm font-medium text-gray-700 mb-2">Duración (horas) *</label>
                        <select name="duracion_horas" id="duracion_horas" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="1">1 hora</option>
                            <option value="2">2 horas</option>
                            <option value="3">3 horas</option>
                            <option value="4">4 horas</option>
                            <option value="5">5 horas</option>
                            <option value="6">6 horas</option>
                            <option value="7">7 horas</option>
                            <option value="8">8 horas</option>
                        </select>
                    </div>

                    <div>
                        <label for="estado" class="block text-sm font-medium text-gray-700 mb-2">Estado *</label>
                        <select name="estado" id="estado" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="pendiente">Pendiente</option>
                            <option value="confirmada">Confirmada</option>
                            <option value="cancelada">Cancelada</option>
                        </select>
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
                    <a href="{{ route('admin.reservations.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                        Cancelar
                    </a>
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                        📅 Crear Reserva
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

        // Inicializar precios
        updatePrices();
    </script>
</body>
</html>