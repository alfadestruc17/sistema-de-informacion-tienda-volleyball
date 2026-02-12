@extends('layouts.app')

@section('title', 'Gestión de reservas')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Reservas</h1>
        <p class="text-slate-600 mt-1">Administra las reservas del sistema</p>
    </div>
    <a href="{{ route('admin.reservations.create') }}" class="bg-sky-600 text-white px-4 py-2 rounded-lg hover:bg-sky-700 text-sm font-medium transition">
        Nueva reserva
    </a>
</div>

<div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">ID</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Cliente</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Cancha</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Fecha / Hora</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Duración</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Estado</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Total</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($reservations as $reservation)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 text-sm font-medium text-slate-800">#{{ $reservation->id }}</td>
                        <td class="px-4 py-3 text-sm text-slate-800">{{ $reservation->user->nombre }}</td>
                        <td class="px-4 py-3 text-sm text-slate-800">{{ $reservation->court->nombre }}</td>
                        <td class="px-4 py-3 text-sm text-slate-800">
                            {{ $reservation->fecha->format('d/m/Y') }}<br>
                            <span class="text-xs text-slate-500">{{ $reservation->hora_inicio }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-800">{{ $reservation->duracion_horas }}h</td>
                        <td class="px-4 py-3">
                            @if($reservation->estado == 'confirmada')
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-emerald-100 text-emerald-800">Confirmada</span>
                            @elseif($reservation->estado == 'pendiente')
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-amber-100 text-amber-800">Pendiente</span>
                            @else
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-slate-100 text-slate-700">Cancelada</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-800">
                            ${{ number_format($reservation->total_estimado, 2) }}
                            @if($reservation->pagado_bool)<span class="text-emerald-600 text-xs">✓</span>@else<span class="text-slate-400 text-xs">—</span>@endif
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <a href="{{ route('admin.reservations.show', $reservation) }}" class="text-slate-600 hover:text-slate-800 mr-3">Ver</a>
                            <a href="{{ route('admin.reservations.edit', $reservation) }}" class="text-slate-600 hover:text-slate-800 mr-3">Editar</a>
                            @if($reservation->estado != 'confirmada' || !$reservation->pagado_bool)
                                <form method="POST" action="{{ route('admin.reservations.destroy', $reservation) }}" class="inline" onsubmit="return confirm('¿Eliminar esta reserva?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">Eliminar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-slate-500">No hay reservas</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($reservations->hasPages())
        <div class="px-4 py-3 border-t border-slate-200 bg-slate-50">
            {{ $reservations->links() }}
        </div>
    @endif
</div>
@endsection
