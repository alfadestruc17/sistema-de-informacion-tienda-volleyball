@extends('layouts.app')

@section('title', 'Venta #' . $sale->id)

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Venta #{{ $sale->id }}</h1>
        <p class="text-slate-600 mt-1">Detalle de la venta</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.sales.edit', $sale) }}" class="bg-sky-600 text-white px-4 py-2 rounded-lg hover:bg-sky-700 text-sm">Editar</a>
        <a href="{{ route('admin.sales.index') }}" class="border border-slate-300 text-slate-700 px-4 py-2 rounded-lg hover:bg-slate-50 text-sm">← Volver a ventas</a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div class="bg-white border border-slate-200 rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-slate-800 mb-3">Información general</h3>
        <div class="space-y-2 text-sm">
            <p><span class="text-slate-500">ID:</span> #{{ $sale->id }}</p>
            <p><span class="text-slate-500">Cliente:</span> {{ $sale->user->nombre }} ({{ $sale->user->email }})</p>
            <p><span class="text-slate-500">Estado:</span>
                @if($sale->estado_pago == 'pagado')
                    <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-emerald-100 text-emerald-800">{{ ucfirst($sale->estado_pago) }}</span>
                @elseif($sale->estado_pago == 'pendiente')
                    <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-amber-100 text-amber-800">{{ ucfirst($sale->estado_pago) }}</span>
                @else
                    <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-800">{{ ucfirst($sale->estado_pago) }}</span>
                @endif
            </p>
            <p><span class="text-slate-500">Creado:</span> {{ $sale->created_at->format('d/m/Y H:i') }}</p>
            <p><span class="text-slate-500">Actualizado:</span> {{ $sale->updated_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>
    <div class="bg-white border border-slate-200 rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-slate-800 mb-3">Resumen</h3>
        <div class="p-4 bg-slate-50 border border-slate-200 rounded-lg">
            <p class="text-2xl font-bold text-emerald-600">${{ number_format($sale->total, 2) }}</p>
            <p class="text-sm text-slate-600">Total de la venta</p>
        </div>
    </div>
</div>

<div class="bg-white border border-slate-200 rounded-lg shadow-sm p-6">
    <h3 class="text-lg font-semibold text-slate-800 mb-3">Productos vendidos</h3>
    <div class="overflow-x-auto">
        <table class="w-full border border-slate-200 rounded-lg overflow-hidden">
            <thead>
                <tr class="bg-slate-50">
                    <th class="border-b border-slate-200 px-4 py-2 text-left text-sm font-medium text-slate-700">Producto</th>
                    <th class="border-b border-slate-200 px-4 py-2 text-center text-sm font-medium text-slate-700">Cantidad</th>
                    <th class="border-b border-slate-200 px-4 py-2 text-right text-sm font-medium text-slate-700">Precio unit.</th>
                    <th class="border-b border-slate-200 px-4 py-2 text-right text-sm font-medium text-slate-700">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->saleItems as $item)
                    <tr class="hover:bg-slate-50">
                        <td class="border-b border-slate-100 px-4 py-2 text-slate-800">{{ $item->product->nombre }}</td>
                        <td class="border-b border-slate-100 px-4 py-2 text-center">{{ $item->cantidad }}</td>
                        <td class="border-b border-slate-100 px-4 py-2 text-right">${{ number_format($item->precio_unitario, 2) }}</td>
                        <td class="border-b border-slate-100 px-4 py-2 text-right">${{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
                <tr class="bg-slate-50 font-bold">
                    <td colspan="3" class="border-t border-slate-200 px-4 py-2 text-right text-slate-700">TOTAL</td>
                    <td class="border-t border-slate-200 px-4 py-2 text-right text-emerald-600">${{ number_format($sale->total, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
