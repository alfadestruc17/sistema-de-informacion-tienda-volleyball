@extends('layouts.app')

@section('title', 'Nueva reserva')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Nueva reserva</h1>
    <p class="text-slate-600 mt-1">Crear reserva para un cliente</p>
</div>

<div class="bg-white border border-slate-200 rounded-lg shadow-sm p-6">
    <form method="POST" action="{{ route('admin.reservations.store') }}">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="user_id" class="block text-sm font-medium text-slate-700 mb-2">Cliente *</label>
                <select name="user_id" id="user_id" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-sky-400 focus:border-sky-400" required>
                    <option value="">Seleccionar cliente...</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->nombre }} ({{ $user->email }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="court_id" class="block text-sm font-medium text-slate-700 mb-2">Cancha *</label>
                <select name="court_id" id="court_id" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-sky-400 focus:border-sky-400" required>
                    <option value="">Seleccionar cancha...</option>
                    @foreach($courts as $court)
                        <option value="{{ $court->id }}">{{ $court->nombre }} - ${{ $court->precio_por_hora }}/hora</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="fecha" class="block text-sm font-medium text-slate-700 mb-2">Fecha *</label>
                <input type="date" name="fecha" id="fecha" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-sky-400 focus:border-sky-400" min="{{ date('Y-m-d') }}" required>
            </div>
            <div>
                <label for="hora_inicio" class="block text-sm font-medium text-slate-700 mb-2">Hora de inicio *</label>
                <select name="hora_inicio" id="hora_inicio" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-sky-400 focus:border-sky-400" required>
                    <option value="">Seleccionar hora...</option>
                    @for($hour = 8; $hour <= 20; $hour++)
                        @for($minute = 0; $minute < 60; $minute += 30)
                            <option value="{{ sprintf('%02d:%02d', $hour, $minute) }}">{{ sprintf('%02d:%02d', $hour, $minute) }}</option>
                        @endfor
                    @endfor
                </select>
            </div>
            <div>
                <label for="duracion_horas" class="block text-sm font-medium text-slate-700 mb-2">Duración (horas) *</label>
                <select name="duracion_horas" id="duracion_horas" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-sky-400 focus:border-sky-400" required>
                    @for($h = 1; $h <= 8; $h++) <option value="{{ $h }}">{{ $h }} hora(s)</option> @endfor
                </select>
            </div>
            <div>
                <label for="estado" class="block text-sm font-medium text-slate-700 mb-2">Estado *</label>
                <select name="estado" id="estado" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-sky-400 focus:border-sky-400" required>
                    <option value="pendiente">Pendiente</option>
                    <option value="confirmada">Confirmada</option>
                    <option value="cancelada">Cancelada</option>
                </select>
            </div>
        </div>
        <div class="mb-6 p-4 bg-slate-50 border border-slate-200 rounded-lg">
            <h3 class="font-semibold text-slate-800 mb-2">Resumen</h3>
            <p class="text-sm text-slate-600">Precio/hora: <span id="precio-hora" class="font-medium text-slate-800">$0.00</span></p>
            <p class="text-sm text-slate-600 mt-1">Total estimado: <span id="total-estimado" class="font-semibold text-emerald-600 text-lg">$0.00</span></p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.reservations.index') }}" class="border border-slate-300 text-slate-700 px-4 py-2 rounded-lg hover:bg-slate-50">Cancelar</a>
            <button type="submit" class="bg-sky-600 text-white px-4 py-2 rounded-lg hover:bg-sky-700">Crear reserva</button>
        </div>
    </form>
</div>

<script>
(function() {
    var courts = @json($courts);
    function updatePrices() {
        var courtId = document.getElementById('court_id').value;
        var duracion = parseInt(document.getElementById('duracion_horas').value) || 0;
        var court = courts.find(function(c) { return c.id == courtId; });
        if (court) {
            var precio = parseFloat(court.precio_por_hora);
            document.getElementById('precio-hora').textContent = '$' + precio.toFixed(2);
            document.getElementById('total-estimado').textContent = '$' + (precio * duracion).toFixed(2);
        } else {
            document.getElementById('precio-hora').textContent = '$0.00';
            document.getElementById('total-estimado').textContent = '$0.00';
        }
    }
    document.getElementById('court_id').addEventListener('change', updatePrices);
    document.getElementById('duracion_horas').addEventListener('change', updatePrices);
    updatePrices();
})();
</script>
@endsection
