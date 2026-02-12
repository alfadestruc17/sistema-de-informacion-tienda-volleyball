@extends('layouts.app')

@section('title', 'Mis reservas')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Mis reservas</h1>
    <p class="text-slate-600 mt-1">Historial de tus reservas de canchas</p>
</div>

<div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
    @if($reservations->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-slate-600 uppercase tracking-wider">Cancha</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-600 uppercase tracking-wider">Fecha</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-600 uppercase tracking-wider">Hora</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-600 uppercase tracking-wider">Duración</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-600 uppercase tracking-wider">Total</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-600 uppercase tracking-wider">Estado</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-600 uppercase tracking-wider">Pago</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-600 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($reservations as $reservation)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-800">{{ $reservation->court->nombre }}</div>
                                <div class="text-slate-500 text-xs">{{ $reservation->court->descripcion }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-slate-800">{{ $reservation->fecha->format('d/m/Y') }}</div>
                                <div class="text-slate-500 text-xs">{{ $reservation->fecha->locale('es')->dayName }}</div>
                            </td>
                            <td class="px-4 py-3 text-slate-800">{{ $reservation->hora_inicio }}</td>
                            <td class="px-4 py-3 text-slate-800">{{ $reservation->duracion_horas }} h</td>
                            <td class="px-4 py-3 font-medium text-emerald-600">${{ number_format($reservation->total_estimado, 2) }}</td>
                            <td class="px-4 py-3">
                                @if($reservation->estado === 'confirmada')
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-emerald-100 text-emerald-800">Confirmada</span>
                                @elseif($reservation->estado === 'pendiente')
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-amber-100 text-amber-800">Pendiente</span>
                                @else
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-800">Cancelada</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($reservation->pagado_bool)
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-emerald-100 text-emerald-800">Pagado</span>
                                @else
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-amber-100 text-amber-800">Pendiente</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if(!$reservation->pagado_bool && $reservation->estado !== 'cancelada')
                                    <form method="POST" action="{{ route('client.payReservation', $reservation) }}" class="inline mr-2">
                                        @csrf
                                        <button type="submit" class="text-emerald-600 hover:text-emerald-800 font-medium">Pagar</button>
                                    </form>
                                @endif
                                @if($reservation->estado !== 'cancelada' && !$reservation->pagado_bool)
                                    <form method="POST" action="{{ route('client.cancelReservation', $reservation) }}" class="inline" onsubmit="return confirm('¿Cancelar esta reserva?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Cancelar</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-12 px-4">
            <div class="text-slate-300 text-5xl mb-4">📅</div>
            <h3 class="text-lg font-medium text-slate-800 mb-2">No tienes reservas</h3>
            <p class="text-slate-500 mb-6">Aún no has reservado ninguna cancha.</p>
            <a href="{{ route('calendar.index') }}" class="inline-block bg-sky-600 text-white px-6 py-3 rounded-lg hover:bg-sky-700 transition">Reservar cancha</a>
        </div>
    @endif
</div>
@endsection
