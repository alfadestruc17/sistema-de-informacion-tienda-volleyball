@extends('layouts.app')

@section('title', 'Reserva')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Reserva #{{ $reservation->id }}</h1>
        <p class="text-slate-600 mt-1">Detalle de la reserva</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.reservations.edit', $reservation) }}" class="bg-sky-600 text-white px-4 py-2 rounded-lg hover:bg-sky-700 text-sm">Editar</a>
        <a href="{{ route('admin.reservations.index') }}" class="border border-slate-300 text-slate-700 px-4 py-2 rounded-lg hover:bg-slate-50 text-sm">Volver</a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div class="bg-white border border-slate-200 rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-slate-800 mb-3">Información general</h3>
        <div class="space-y-2 text-sm">
            <p><span class="text-slate-500">ID:</span> #{{ $reservation->id }}</p>
            <p><span class="text-slate-500">Cliente:</span> {{ $reservation->user->nombre }} ({{ $reservation->user->email }})</p>
            <p><span class="text-slate-500">Cancha:</span> {{ $reservation->court->nombre }}</p>
            <p><span class="text-slate-500">Fecha:</span> {{ $reservation->fecha->format('d/m/Y') }}</p>
            <p><span class="text-slate-500">Hora inicio:</span> {{ $reservation->hora_inicio }}</p>
            <p><span class="text-slate-500">Duración:</span> {{ $reservation->duracion_horas }} horas</p>
            <p><span class="text-slate-500">Estado:</span>
                @if($reservation->estado == 'confirmada')
                    <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-emerald-100 text-emerald-800">{{ ucfirst($reservation->estado) }}</span>
                @elseif($reservation->estado == 'pendiente')
                    <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-amber-100 text-amber-800">{{ ucfirst($reservation->estado) }}</span>
                @else
                    <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-800">{{ ucfirst($reservation->estado) }}</span>
                @endif
            </p>
            <p><span class="text-slate-500">Pagado:</span>
                @if($reservation->pagado_bool)
                    <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-emerald-100 text-emerald-800">Sí</span>
                @else
                    <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-amber-100 text-amber-800">No</span>
                @endif
            </p>
            <p><span class="text-slate-500">Creado:</span> {{ $reservation->created_at->format('d/m/Y H:i') }}</p>
            <p><span class="text-slate-500">Actualizado:</span> {{ $reservation->updated_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>
    <div class="bg-white border border-slate-200 rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-slate-800 mb-3">Costos</h3>
        <div class="p-4 bg-slate-50 border border-slate-200 rounded-lg space-y-2">
            <p><span class="text-slate-600">Precio por hora:</span> ${{ number_format($reservation->court->precio_por_hora, 2) }}</p>
            <p><span class="text-slate-600">Duración:</span> {{ $reservation->duracion_horas }} horas</p>
            <p><span class="text-slate-600">Total estimado:</span> <strong class="text-emerald-600">${{ number_format($reservation->total_estimado, 2) }}</strong></p>
        </div>
        @if($reservation->orders->count() > 0)
            <h4 class="text-md font-semibold text-slate-800 mt-4 mb-2">Órdenes asociadas</h4>
            <div class="space-y-2">
                @foreach($reservation->orders as $order)
                    <div class="p-2 bg-sky-50 border border-sky-200 rounded-lg text-sm">
                        <strong>Orden #{{ $order->id }}:</strong> ${{ number_format($order->total, 2) }}
                        @if($order->estado_pago == 'pagado')
                            <span class="px-1.5 py-0.5 text-xs font-medium rounded-full bg-emerald-100 text-emerald-800">{{ ucfirst($order->estado_pago) }}</span>
                        @elseif($order->estado_pago == 'pendiente')
                            <span class="px-1.5 py-0.5 text-xs font-medium rounded-full bg-amber-100 text-amber-800">{{ ucfirst($order->estado_pago) }}</span>
                        @else
                            <span class="px-1.5 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-800">{{ ucfirst($order->estado_pago) }}</span>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

@if($reservation->reservationItems->count() > 0)
<div class="bg-white border border-slate-200 rounded-lg shadow-sm p-6">
    <h3 class="text-lg font-semibold text-slate-800 mb-3">Items adicionales</h3>
    <div class="overflow-x-auto">
        <table class="w-full border border-slate-200 rounded-lg overflow-hidden">
            <thead>
                <tr class="bg-slate-50">
                    <th class="border-b border-slate-200 px-4 py-2 text-left text-sm font-medium text-slate-700">Descripción</th>
                    <th class="border-b border-slate-200 px-4 py-2 text-center text-sm font-medium text-slate-700">Cantidad</th>
                    <th class="border-b border-slate-200 px-4 py-2 text-right text-sm font-medium text-slate-700">Precio unit.</th>
                    <th class="border-b border-slate-200 px-4 py-2 text-right text-sm font-medium text-slate-700">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reservation->reservationItems as $item)
                    <tr class="hover:bg-slate-50">
                        <td class="border-b border-slate-100 px-4 py-2 text-slate-800">{{ $item->descripcion }}</td>
                        <td class="border-b border-slate-100 px-4 py-2 text-center">{{ $item->cantidad }}</td>
                        <td class="border-b border-slate-100 px-4 py-2 text-right">${{ number_format($item->precio_unitario, 2) }}</td>
                        <td class="border-b border-slate-100 px-4 py-2 text-right">${{ number_format($item->cantidad * $item->precio_unitario, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
