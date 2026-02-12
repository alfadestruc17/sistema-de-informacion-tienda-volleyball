@extends('layouts.app')

@section('title', 'Editar venta #' . $sale->id)

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Editar venta #{{ $sale->id }}</h1>
        <p class="text-slate-600 mt-1">Modifica estado de pago</p>
    </div>
    <a href="{{ route('admin.sales.show', $sale) }}" class="border border-slate-300 text-slate-700 px-4 py-2 rounded-lg hover:bg-slate-50">← Volver</a>
</div>

<div class="bg-white border border-slate-200 rounded-lg shadow-sm p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.sales.update', $sale) }}">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="user_id" class="block text-sm font-medium text-slate-700 mb-2">Cliente *</label>
                <select name="user_id" id="user_id" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-sky-400 focus:border-sky-400" required>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ $sale->user_id == $user->id ? 'selected' : '' }}>
                            {{ $user->nombre }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="estado_pago" class="block text-sm font-medium text-slate-700 mb-2">Estado de pago *</label>
                <select name="estado_pago" id="estado_pago" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-sky-400 focus:border-sky-400" required>
                    <option value="pendiente" {{ $sale->estado_pago == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="pagado" {{ $sale->estado_pago == 'pagado' ? 'selected' : '' }}>Pagado</option>
                    <option value="cancelado" {{ $sale->estado_pago == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                </select>
            </div>
        </div>
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-slate-800 mb-3">Productos en la venta</h3>
            <div class="p-4 bg-slate-50 border border-slate-200 rounded-lg space-y-2">
                @foreach($sale->saleItems as $item)
                    <div class="flex justify-between items-center p-2 bg-white rounded border border-slate-100">
                        <span class="text-slate-800">{{ $item->product->nombre }}</span>
                        <span class="text-slate-600">x{{ $item->cantidad }} = ${{ number_format($item->subtotal, 2) }}</span>
                    </div>
                @endforeach
                <div class="mt-4 pt-4 border-t border-slate-200 flex justify-between font-bold text-lg text-slate-800">
                    <span>Total</span>
                    <span class="text-emerald-600">${{ number_format($sale->total, 2) }}</span>
                </div>
            </div>
            <p class="text-sm text-slate-500 mt-2">Los productos no se pueden modificar aquí. Para cambiar productos, elimina esta venta y crea una nueva.</p>
        </div>
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.sales.show', $sale) }}" class="border border-slate-300 text-slate-700 px-4 py-2 rounded-lg hover:bg-slate-50">Cancelar</a>
            <button type="submit" class="bg-sky-600 text-white px-4 py-2 rounded-lg hover:bg-sky-700">Guardar cambios</button>
        </div>
    </form>
</div>
@endsection
