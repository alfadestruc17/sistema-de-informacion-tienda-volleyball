@extends('layouts.app')

@section('title', 'Ventas')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Ventas</h1>
        <p class="text-slate-600 mt-1">Historial de ventas</p>
    </div>
    <a href="{{ route('admin.pos.index') }}" class="bg-sky-600 text-white px-4 py-2 rounded-lg hover:bg-sky-700 text-sm font-medium transition">Nueva venta</a>
</div>

<div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-slate-600 uppercase tracking-wider">ID</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600 uppercase tracking-wider">Cliente</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600 uppercase tracking-wider">Total</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600 uppercase tracking-wider">Estado</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600 uppercase tracking-wider">Fecha</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($sales as $sale)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium text-slate-800">#{{ $sale->id }}</td>
                        <td class="px-4 py-3 text-slate-800">{{ $sale->user->nombre }}</td>
                        <td class="px-4 py-3 font-medium text-emerald-600">${{ number_format($sale->total, 2) }}</td>
                        <td class="px-4 py-3">
                            @if($sale->estado_pago == 'pagado')
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-emerald-100 text-emerald-800">Pagado</span>
                            @elseif($sale->estado_pago == 'pendiente')
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-amber-100 text-amber-800">Pendiente</span>
                            @else
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-800">Cancelado</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-800">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.sales.show', $sale) }}" class="text-sky-600 hover:text-sky-800 mr-3">Ver</a>
                            <a href="{{ route('admin.sales.edit', $sale) }}" class="text-slate-600 hover:text-sky-600 mr-3">Editar</a>
                            @if($sale->estado_pago != 'pagado')
                                <form method="POST" action="{{ route('admin.sales.destroy', $sale) }}" class="inline" onsubmit="return confirm('¿Eliminar esta venta?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">Eliminar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-slate-500">No hay ventas</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($sales->hasPages())
        <div class="px-4 py-3 border-t border-slate-200 bg-slate-50">{{ $sales->links() }}</div>
    @endif
</div>
@endsection
