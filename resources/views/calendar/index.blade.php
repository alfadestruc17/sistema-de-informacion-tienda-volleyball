<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservar Cancha - Sistema de Voleibol</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .time-slot {
            cursor: pointer;
            transition: all 0.2s;
        }
        .time-slot.available:hover {
            background-color: #d1fae5;
            border-color: #10b981;
        }
        .time-slot.occupied {
            background-color: #fee2e2;
            border-color: #ef4444;
            cursor: not-allowed;
        }
        .time-slot.reserved {
            background-color: #fef3c7;
            border-color: #f59e0b;
            cursor: not-allowed;
        }
        .time-slot.selected {
            background-color: #dbeafe;
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px #3b82f6;
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
                    <span class="text-gray-700">Cliente: {{ Auth::user()->nombre }}</span>
                    <a href="{{ route('client.reservations') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        📅 Mis Reservas
                    </a>
                    <a href="{{ route('calendar.index') }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                        🏐 Reservar Cancha
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
            <h1 class="text-3xl font-bold">Reservar Cancha de Voleibol</h1>
            <p class="text-gray-600">Selecciona una cancha, fecha y hora disponible</p>
        </div>

        <!-- Selector de Cancha -->
        <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
            <h2 class="text-lg font-semibold mb-4">Seleccionar Cancha</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($courts as $court)
                    <div class="border rounded-lg p-4 cursor-pointer court-option {{ $selectedCourt && $selectedCourt->id == $court->id ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}"
                         onclick="selectCourt({{ $court->id }})">
                        <h3 class="font-semibold">{{ $court->nombre }}</h3>
                        <p class="text-sm text-gray-600">{{ $court->descripcion }}</p>
                        <p class="text-sm font-medium text-green-600">${{ number_format($court->precio_por_hora, 2) }}/hora</p>
                    </div>
                @endforeach
            </div>
        </div>

        @if($selectedCourt)
        <!-- Calendario de Disponibilidad -->
        <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Disponibilidad - {{ $selectedCourt->nombre }}</h2>
                <div class="flex gap-2">
                    <button onclick="previousWeek()" class="bg-gray-500 text-white px-3 py-1 rounded text-sm">← Semana Anterior</button>
                    <span class="font-semibold" id="week-range">{{ $currentWeek['start'] }} - {{ $currentWeek['end'] }}</span>
                    <button onclick="nextWeek()" class="bg-gray-500 text-white px-3 py-1 rounded text-sm">Semana Siguiente →</button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300">
                    <thead>
                        <tr>
                            <th class="border border-gray-300 p-2 bg-gray-100 font-semibold">Hora</th>
                            @foreach($currentWeek['days'] as $day)
                                <th class="border border-gray-300 p-2 bg-gray-100 font-semibold">{{ $day['name'] }}<br>{{ $day['date'] }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($hours as $hour)
                            <tr>
                                <td class="border border-gray-300 p-2 bg-gray-50 font-medium">{{ $hour }}</td>
                                @foreach($currentWeek['days'] as $dayIndex => $day)
                                    @php
                                        $isAvailable = $availability[$day['date']][$hour] ?? false;
                                        $status = $isAvailable ? 'available' : 'occupied';
                                        $dateTime = $day['date'] . ' ' . $hour;
                                    @endphp
                                    <td class="border border-gray-300 p-2">
                                        <div class="time-slot {{ $status }} border-2 rounded p-2 text-center text-sm"
                                             data-date="{{ $day['date'] }}"
                                             data-time="{{ $hour }}"
                                             @if($isAvailable) onclick="selectTimeSlot(this, '{{ $day['date'] }}', '{{ $hour }}')" @endif>
                                            {{ $hour }}
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Formulario de Reserva -->
        <div id="reservation-form" class="bg-white p-6 rounded-lg shadow-lg" style="display: none;">
            <h2 class="text-lg font-semibold mb-4">Confirmar Reserva</h2>
            <form method="POST" action="{{ route('client.createReservation') }}">
                @csrf
                <input type="hidden" name="court_id" value="{{ $selectedCourt->id }}">
                <input type="hidden" id="selected_date" name="fecha">
                <input type="hidden" id="selected_time" name="hora_inicio">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fecha</label>
                        <input type="text" id="display_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Hora de Inicio</label>
                        <input type="text" id="display_time" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" readonly>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Duración (horas)</label>
                    <select name="duracion_horas" id="duration_select" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" onchange="calculateTotal()">
                        <option value="1">1 hora</option>
                        <option value="2">2 horas</option>
                        <option value="3">3 horas</option>
                    </select>
                </div>

                <div class="mb-4">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-semibold">Total:</span>
                        <span id="total_amount" class="text-lg font-bold text-green-600">$0.00</span>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600">
                        ✅ Confirmar Reserva
                    </button>
                    <button type="button" onclick="cancelSelection()" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
        @endif
    </div>

    <script>
        let selectedCourtId = {{ $selectedCourt ? $selectedCourt->id : 'null' }};
        let selectedSlot = null;

        function selectCourt(courtId) {
            window.location.href = '{{ route("calendar.index") }}?court_id=' + courtId;
        }

        function selectTimeSlot(element, date, time) {
            // Remover selección anterior
            if (selectedSlot) {
                selectedSlot.classList.remove('selected');
            }

            // Seleccionar nuevo slot
            element.classList.add('selected');
            selectedSlot = element;

            // Mostrar formulario
            document.getElementById('reservation-form').style.display = 'block';
            document.getElementById('selected_date').value = date;
            document.getElementById('selected_time').value = time;
            document.getElementById('display_date').value = date;
            document.getElementById('display_time').value = time;

            calculateTotal();
        }

        function cancelSelection() {
            if (selectedSlot) {
                selectedSlot.classList.remove('selected');
                selectedSlot = null;
            }
            document.getElementById('reservation-form').style.display = 'none';
        }

        function calculateTotal() {
            const duration = parseInt(document.getElementById('duration_select').value);
            const pricePerHour = {{ $selectedCourt ? $selectedCourt->precio_por_hora : 0 }};
            const total = duration * pricePerHour;

            document.getElementById('total_amount').textContent = '$' + total.toFixed(2);
        }

        function previousWeek() {
            // Implementar navegación de semanas
            alert('Funcionalidad próximamente');
        }

        function nextWeek() {
            // Implementar navegación de semanas
            alert('Funcionalidad próximamente');
        }

        // Mostrar mensajes de éxito/error con SweetAlert
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '{{ session("success") }}',
                confirmButtonText: 'Aceptar'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session("error") }}',
                confirmButtonText: 'Aceptar'
            });
        @endif
    </script>
</body>
</html>