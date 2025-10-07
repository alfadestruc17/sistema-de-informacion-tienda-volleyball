<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Punto de Venta - Sistema de Reservas de Voleibol</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .product-item {
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .product-item:hover {
            background-color: #f3f4f6;
        }
    </style>
</head>

<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-xl font-bold text-gray-800">🏐 Arena Sport C.B - POS</span>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700">Cajero: {{ Auth::user()->nombre }}</span>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                            Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto p-4">
        <div class="mb-6">
            <h1 class="text-3xl font-bold">Punto de Venta</h1>
            <p class="text-gray-600">Gestiona órdenes y consumos de reservas</p>
        </div>

        <div class="mb-4">
            <input type="text" placeholder="Buscar producto..." class="border w-full p-2 rounded">
        </div>

        <div class="grid grid-cols-3 gap-4">
            <!-- Panel de Productos -->
            <div class="col-span-2">
                <h2 class="text-lg font-semibold mb-2">Productos Disponibles</h2>
                <div class="grid grid-cols-3 gap-2 mb-4">
                    @foreach ($products as $product)
                        <form method="POST" action="{{ route('pos.addToCart') }}" class="inline">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="cantidad" value="1">
                            <button type="submit" class="product-item bg-white p-4 rounded shadow w-full text-left">
                                <div class="font-semibold text-sm">{{ $product->nombre }}</div>
                                <div class="text-gray-600 text-sm">${{ number_format($product->precio, 2) }}</div>
                                <div class="text-xs text-gray-500">Stock: {{ $product->stock }}</div>
                            </button>
                        </form>
                    @endforeach
                </div>


            </div>

            <!-- Panel de Orden -->
            <div>
                <h2 class="text-lg font-semibold mb-2">Orden Actual</h2>

                <!-- Selector de Reserva -->
                <div class="bg-white p-4 rounded shadow mb-4">
                    <h3 class="text-sm font-semibold mb-2">Reserva (Opcional)</h3>
                    <form method="POST" action="{{ route('pos.loadReservation') }}" class="flex gap-2">
                        @csrf
                        <input type="number" name="reservation_id" placeholder="ID de Reserva"
                            class="border p-2 rounded flex-1 text-sm" required>
                        <button type="submit" class="bg-blue-500 text-white px-3 py-1 rounded text-sm">Cargar</button>
                    </form>
                    @if (session('reservation'))
                        <div class="mt-2 text-xs text-gray-600">
                            <div><strong>Cliente:</strong> {{ session('reservation')['user']['nombre'] }}</div>
                            <div><strong>Cancha:</strong> {{ session('reservation')['court']['nombre'] }}</div>
                            <div><strong>Fecha/Hora:</strong> {{ session('reservation')['fecha'] }}
                                {{ session('reservation')['hora_inicio'] }}</div>
                        </div>
                    @endif
                </div>

                <div class="bg-white p-4 rounded shadow mb-4">
                    <div class="mb-2">
                        <strong>Orden ID:</strong> {{ session('current_order_id', 'Nueva Orden') }}
                    </div>
                </div>

                <div class="bg-white p-4 rounded shadow mb-4 min-h-32">
                    @if (session('cart') && count(session('cart')) > 0)
                        @foreach (session('cart') as $item)
                            <div class="flex justify-between items-center mb-2 p-2 border-b">
                                <span>{{ $item['product']['nombre'] }} x{{ $item['cantidad'] }}</span>
                                <span>${{ number_format($item['cantidad'] * $item['product']['precio'], 2) }}</span>
                                <form method="POST" action="{{ route('pos.removeFromCart') }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $item['product']['id'] }}">
                                    <button type="submit" class="text-red-500 ml-2">×</button>
                                </form>
                            </div>
                        @endforeach
                    @else
                        <p class="text-gray-500 text-center py-8">No hay items en la orden</p>
                    @endif
                </div>

                <div class="bg-white p-4 rounded shadow">
                    <div class="flex justify-between font-bold text-lg">
                        <span>Total:</span>
                        <span>${{ number_format(session('cart_total', 0), 2) }}</span>
                    </div>
                </div>

                <div class="mt-4 flex gap-2">
                    @if (session('cart') && count(session('cart')) > 0)
                        <form method="POST" action="{{ route('pos.checkout') }}" class="flex-1">
                            @csrf
                            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded w-full"
                                onclick="return confirm('¿Confirmar cobro de ${{ number_format(session('cart_total', 0), 2) }}?')">
                                💰 Cobrar Orden
                            </button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('pos.clearCart') }}" class="flex-1">
                        @csrf
                        <button type="submit" class="bg-gray-500 text-white px-4 py-2 rounded w-full">
                            Limpiar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- CSRF Token para formularios -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</body>

</html>
