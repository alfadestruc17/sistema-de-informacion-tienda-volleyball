@extends('layouts.app')

@section('title', 'Punto de Venta')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Punto de Venta</h1>
    <p class="text-slate-600 mt-1">Gestiona órdenes y consumos de reservas</p>
</div>

<div class="mb-4">
    <input type="text" placeholder="Buscar producto..." class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-sky-400 focus:border-sky-400">
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <h2 class="text-lg font-semibold text-slate-800 mb-2">Productos disponibles</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 mb-4">
            @foreach ($products as $product)
                <form method="POST" action="{{ route('pos.addToCart') }}" class="inline">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="cantidad" value="1">
                    <button type="submit" class="w-full text-left bg-white border border-slate-200 p-4 rounded-lg shadow-sm hover:bg-sky-50 hover:border-sky-200 transition">
                        <div class="font-semibold text-sm text-slate-800">{{ $product->nombre }}</div>
                        <div class="text-slate-600 text-sm">${{ number_format($product->precio, 2) }}</div>
                        <div class="text-xs text-slate-500">Stock: {{ $product->stock }}</div>
                    </button>
                </form>
            @endforeach
        </div>
    </div>
    <div>
        <h2 class="text-lg font-semibold text-slate-800 mb-2">Orden actual</h2>
        <div class="bg-white border border-slate-200 rounded-lg shadow-sm p-4 mb-4">
            <h3 class="text-sm font-semibold text-slate-700 mb-2">Reserva (opcional)</h3>
            <form method="POST" action="{{ route('pos.loadReservation') }}" class="flex gap-2">
                @csrf
                <input type="number" name="reservation_id" placeholder="ID de reserva" class="border border-slate-300 rounded-lg px-3 py-2 flex-1 text-sm focus:ring-2 focus:ring-sky-400 focus:border-sky-400" required>
                <button type="submit" class="bg-sky-600 text-white px-3 py-2 rounded-lg hover:bg-sky-700 text-sm">Cargar</button>
            </form>
            @if (session('reservation'))
                <div class="mt-2 text-xs text-slate-600">
                    <div><strong>Cliente:</strong> {{ session('reservation')['user']['nombre'] }}</div>
                    <div><strong>Cancha:</strong> {{ session('reservation')['court']['nombre'] }}</div>
                    <div><strong>Fecha/Hora:</strong> {{ session('reservation')['fecha'] }} {{ session('reservation')['hora_inicio'] }}</div>
                </div>
            @endif
        </div>
        <div class="bg-white border border-slate-200 rounded-lg shadow-sm p-4 mb-4">
            <strong class="text-slate-700">Orden ID:</strong> {{ session('current_order_id', 'Nueva orden') }}
        </div>
        <div class="bg-white border border-slate-200 rounded-lg shadow-sm p-4 mb-4 min-h-32">
            @if (session('cart') && count(session('cart')) > 0)
                @foreach (session('cart') as $item)
                    <div class="flex justify-between items-center mb-2 p-2 border-b border-slate-100">
                        <span class="text-slate-800 text-sm">{{ $item['product']['nombre'] }} x{{ $item['cantidad'] }}</span>
                        <span class="text-slate-600 text-sm">${{ number_format($item['cantidad'] * $item['product']['precio'], 2) }}</span>
                        <form method="POST" action="{{ route('pos.removeFromCart') }}" class="inline">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $item['product']['id'] }}">
                            <button type="submit" class="text-red-500 hover:text-red-600">×</button>
                        </form>
                    </div>
                @endforeach
            @else
                <p class="text-slate-500 text-center py-8 text-sm">No hay items en la orden</p>
            @endif
        </div>
        <div class="bg-white border border-slate-200 rounded-lg shadow-sm p-4 mb-4">
            <div class="flex justify-between font-bold text-lg text-slate-800">
                <span>Total</span>
                <span class="text-emerald-600">${{ number_format(session('cart_total', 0), 2) }}</span>
            </div>
        </div>
        <div class="flex gap-2">
            @if (session('cart') && count(session('cart')) > 0)
                <form method="POST" action="{{ route('pos.checkout') }}" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700" onclick="return confirm('¿Confirmar cobro de ${{ number_format(session('cart_total', 0), 2) }}?')">
                        Cobrar orden
                    </button>
                </form>
            @endif
            <form method="POST" action="{{ route('pos.clearCart') }}" class="flex-1">
                @csrf
                <button type="submit" class="w-full border border-slate-300 text-slate-700 px-4 py-2 rounded-lg hover:bg-slate-50">
                    Limpiar
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
