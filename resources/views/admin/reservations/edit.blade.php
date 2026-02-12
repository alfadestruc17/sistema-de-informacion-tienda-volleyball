@extends('layouts.app')

@section('title', 'Editar reserva')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Editar reserva #{{ $reservation->id }}</h1>
        <p class="text-slate-600 mt-1">Modifica los datos de la reserva</p>
    </div>
    <a href="{{ route('admin.reservations.show', $reservation) }}" class="border border-slate-300 text-slate-700 px-4 py-2 rounded-lg hover:bg-slate-50">← Volver</a>
</div>

<div class="bg-white border border-slate-200 rounded-lg shadow-sm p-6">
    <form method="POST" action="{{ route('admin.reservations.update', $reservation) }}">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="user_id" class="block text-sm font-medium text-slate-700 mb-2">Cliente *</label>
                <select name="user_id" id="user_id" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-sky-400 focus:border-sky-400" required>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ $reservation->user_id == $user->id ? 'selected' : '' }}>
                            {{ $user->nombre }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="court_id" class="block text-sm font-medium text-slate-700 mb-2">Cancha *</label>
                <select name="court_id" id="court_id" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-sky-400 focus:border-sky-400" required>
                    @foreach($courts as $court)
                        <option value="{{ $court->id }}" {{ $reservation->court_id == $court->id ? 'selected' : '' }}>
                            {{ $court->nombre }} - ${{ $court->precio_por_hora }}/hora
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="fecha" class="block text-sm font-medium text-slate-700 mb-2">Fecha *</label>
                <input type="date" name="fecha" id="fecha" value="{{ $reservation->fecha->format('Y-m-d') }}" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-sky-400 focus:border-sky-400" required>
            </div>
            <div>
                <label for="hora_inicio" class="block text-sm font-medium text-slate-700 mb-2">Hora de inicio *</label>
                <select name="hora_inicio" id="hora_inicio" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-sky-400 focus:border-sky-400" required>
                    @for($hour = 8; $hour <= 20; $hour++)
                        @for($minute = 0; $minute < 60; $minute += 30)
                            <option value="{{ sprintf('%02d:%02d', $hour, $minute) }}" {{ $reservation->hora_inicio == sprintf('%02d:%02d', $hour, $minute) ? 'selected' : '' }}>
                                {{ sprintf('%02d:%02d', $hour, $minute) }}
                            </option>
                        @endfor
                    @endfor
                </select>
            </div>
            <div>
                <label for="duracion_horas" class="block text-sm font-medium text-slate-700 mb-2">Duración (horas) *</label>
                <select name="duracion_horas" id="duracion_horas" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-sky-400 focus:border-sky-400" required>
                    @for($i = 1; $i <= 8; $i++)
                        <option value="{{ $i }}" {{ $reservation->duracion_horas == $i ? 'selected' : '' }}>
                            {{ $i }} hora{{ $i > 1 ? 's' : '' }}
                        </option>
                    @endfor
                </select>
            </div>
            <div>
                <label for="estado" class="block text-sm font-medium text-slate-700 mb-2">Estado *</label>
                <select name="estado" id="estado" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-sky-400 focus:border-sky-400" required>
                    <option value="pendiente" {{ $reservation->estado == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="confirmada" {{ $reservation->estado == 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                    <option value="cancelada" {{ $reservation->estado == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label for="pagado_bool" class="block text-sm font-medium text-slate-700 mb-2">¿Pagado?</label>
                <div class="flex items-center">
                    <input type="checkbox" name="pagado_bool" id="pagado_bool" value="1" {{ $reservation->pagado_bool ? 'checked' : '' }} class="h-4 w-4 text-sky-600 focus:ring-sky-500 border-slate-300 rounded">
                    <label for="pagado_bool" class="ml-2 text-sm text-slate-700">Marcar como pagado</label>
                </div>
            </div>
        </div>
        <div class="mb-6 p-4 bg-slate-50 border border-slate-200 rounded-lg">
            <h3 class="text-lg font-semibold text-slate-800 mb-2">Resumen de costos</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-slate-600">Precio por hora</p>
                    <p id="precio-hora" class="font-semibold text-slate-800">$0.00</p>
                </div>
                <div>
                    <p class="text-sm text-slate-600">Total estimado</p>
                    <p id="total-estimado" class="font-semibold text-lg text-emerald-600">$0.00</p>
                </div>
            </div>
        </div>
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.reservations.show', $reservation) }}" class="border border-slate-300 text-slate-700 px-4 py-2 rounded-lg hover:bg-slate-50">Cancelar</a>
            <button type="submit" class="bg-sky-600 text-white px-4 py-2 rounded-lg hover:bg-sky-700">Guardar cambios</button>
        </div>
    </form>
</div>

<script>
(function() {
    const courts = @json($courts);
    const courtEl = document.getElementById('court_id');
    const duracionEl = document.getElementById('duracion_horas');
    if (!courtEl || !duracionEl) return;
    function updatePrices() {
        const courtId = courtEl.value;
        const duracion = parseInt(duracionEl.value, 10) || 0;
        const court = courts.find(function(c) { return c.id == courtId; });
        const precioEl = document.getElementById('precio-hora');
        const totalEl = document.getElementById('total-estimado');
        if (court && precioEl && totalEl) {
            const precioHora = parseFloat(court.precio_por_hora);
            const total = precioHora * duracion;
            precioEl.textContent = '$' + precioHora.toFixed(2);
            totalEl.textContent = '$' + total.toFixed(2);
        } else if (precioEl && totalEl) {
            precioEl.textContent = '$0.00';
            totalEl.textContent = '$0.00';
        }
    }
    courtEl.addEventListener('change', updatePrices);
    duracionEl.addEventListener('change', updatePrices);
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', updatePrices);
    } else {
        updatePrices();
    }
})();
</script>
@endsection
