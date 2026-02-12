@extends('layouts.app')

@section('title', 'Reservar cancha')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@push('styles')
<style>
    .time-slot { cursor: pointer; transition: all 0.2s; }
    .time-slot.available { background-color: #d1fae5; border: 2px solid #10b981; }
    .time-slot.available:hover { background-color: #a7f3d0; border-color: #059669; }
    .time-slot.occupied { background-color: #fee2e2; border: 2px solid #ef4444; cursor: not-allowed; color: #b91c1c; }
    .time-slot.selected { background-color: #e0f2fe; border: 2px solid #0ea5e9; box-shadow: 0 0 0 2px #0ea5e9; }
</style>
@endpush


@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Reservar cancha</h1>
    <p class="text-slate-600 mt-1">Elige cancha, fecha y hora disponible (verde = disponible, rojo = ocupado)</p>
</div>

<div class="bg-white border border-slate-200 rounded-lg shadow-sm p-6 mb-6">
    <h2 class="text-lg font-semibold text-slate-800 mb-4 border-l-2 border-sky-400 pl-2">Seleccionar cancha</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach($courts as $court)
            <div class="border-2 rounded-lg p-4 cursor-pointer court-option {{ $selectedCourt && $selectedCourt->id == $court->id ? 'border-sky-500 bg-sky-50' : 'border-slate-200 hover:border-sky-300' }}"
                 onclick="selectCourt({{ $court->id }})">
                <h3 class="font-semibold text-slate-800">{{ $court->nombre }}</h3>
                <p class="text-sm text-slate-600 mt-1">{{ $court->descripcion }}</p>
                <p class="text-sm font-medium text-emerald-600 mt-2">${{ number_format($court->precio_por_hora, 2) }}/hora</p>
            </div>
        @endforeach
    </div>
</div>

@if($selectedCourt)
<div class="bg-white border border-slate-200 rounded-lg shadow-sm p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold text-slate-800 border-l-2 border-sky-400 pl-2">Disponibilidad – {{ $selectedCourt->nombre }}</h2>
        <div class="flex gap-2 items-center">
            <span class="text-sm text-slate-500">Leyenda: <span class="inline-block w-4 h-4 rounded bg-emerald-200 border border-emerald-400"></span> Disponible &nbsp; <span class="inline-block w-4 h-4 rounded bg-red-200 border border-red-400"></span> Ocupado</span>
            <span class="font-medium text-slate-700" id="week-range">{{ $currentWeek['start'] }} - {{ $currentWeek['end'] }}</span>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full border-collapse border border-slate-200 text-sm">
            <thead>
                <tr>
                    <th class="border border-slate-200 p-2 bg-slate-50 font-semibold text-slate-700">Hora</th>
                    @foreach($currentWeek['days'] as $day)
                        <th class="border border-slate-200 p-2 bg-slate-50 font-semibold text-slate-700">{{ $day['name'] }}<br><span class="text-xs font-normal">{{ $day['date'] }}</span></th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($hours as $hour)
                    <tr>
                        <td class="border border-slate-200 p-2 bg-slate-50 font-medium text-slate-700">{{ $hour }}</td>
                        @foreach($currentWeek['days'] as $dayIndex => $day)
                            @php
                                $isAvailable = $availability[$day['date']][$hour] ?? false;
                                $status = $isAvailable ? 'available' : 'occupied';
                                $dateTime = $day['date'] . ' ' . $hour;
                            @endphp
                            <td class="border border-slate-200 p-2">
                                <div class="time-slot {{ $status }} rounded p-2 text-center text-sm"
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

<div id="reservation-form" class="bg-white border border-slate-200 rounded-lg shadow-sm p-6 hidden">
    <h2 class="text-lg font-semibold text-slate-800 mb-4 border-l-2 border-emerald-400 pl-2">Confirmar reserva</h2>
    <form method="POST" action="{{ route('client.createReservation') }}">
        @csrf
        <input type="hidden" name="court_id" value="{{ $selectedCourt->id }}">
        <input type="hidden" id="selected_date" name="fecha">
        <input type="hidden" id="selected_time" name="hora_inicio">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-slate-700">Fecha</label>
                <input type="text" id="display_date" class="mt-1 block w-full border border-slate-300 rounded-lg px-3 py-2 bg-slate-50 text-slate-800" readonly>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Hora de inicio</label>
                <input type="text" id="display_time" class="mt-1 block w-full border border-slate-300 rounded-lg px-3 py-2 bg-slate-50 text-slate-800" readonly>
            </div>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700">Duración (horas)</label>
            <select name="duracion_horas" id="duration_select" class="mt-1 block w-full border border-slate-300 rounded-lg px-3 py-2 text-slate-800" onchange="calculateTotal()">
                <option value="1">1 hora</option>
                <option value="2">2 horas</option>
                <option value="3">3 horas</option>
            </select>
        </div>
        <div class="mb-4 flex justify-between items-center">
            <span class="text-lg font-semibold text-slate-800">Total:</span>
            <span id="total_amount" class="text-lg font-bold text-emerald-600">$0.00</span>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-emerald-600 text-white px-6 py-2 rounded-lg hover:bg-emerald-700 transition">Confirmar reserva</button>
            <button type="button" onclick="cancelSelection()" class="border border-slate-300 text-slate-700 px-6 py-2 rounded-lg hover:bg-slate-50 transition">Cancelar</button>
        </div>
    </form>
</div>
@endif

<script>
var selectedCourtId = {{ $selectedCourt ? $selectedCourt->id : 'null' }};
var selectedSlot = null;

function selectCourt(courtId) {
    window.location.href = '{{ url("calendar") }}?court_id=' + courtId;
}

function selectTimeSlot(element, date, time) {
    if (selectedSlot) selectedSlot.classList.remove('selected');
    element.classList.add('selected');
    selectedSlot = element;
    document.getElementById('reservation-form').classList.remove('hidden');
    document.getElementById('selected_date').value = date;
    document.getElementById('selected_time').value = time;
    document.getElementById('display_date').value = date;
    document.getElementById('display_time').value = time;
    calculateTotal();
}

function cancelSelection() {
    if (selectedSlot) { selectedSlot.classList.remove('selected'); selectedSlot = null; }
    document.getElementById('reservation-form').classList.add('hidden');
}

function calculateTotal() {
    var duration = parseInt(document.getElementById('duration_select').value);
    var pricePerHour = {{ $selectedCourt ? $selectedCourt->precio_por_hora : 0 }};
    document.getElementById('total_amount').textContent = '$' + (duration * pricePerHour).toFixed(2);
}

@if(session('success'))
    Swal.fire({ icon: 'success', title: '¡Éxito!', text: '{{ session("success") }}', confirmButtonText: 'Aceptar' });
@endif
@if(session('error'))
    Swal.fire({ icon: 'error', title: 'Error', text: '{{ session("error") }}', confirmButtonText: 'Aceptar' });
@endif
</script>
@endsection
