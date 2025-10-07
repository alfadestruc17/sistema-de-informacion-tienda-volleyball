<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Venta #{{ $sale->id }} - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-xl font-bold text-gray-800">🏐 Arena Sport C.B - Venta #{{ $sale->id }}</span>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700">Admin: {{ Auth::user()->nombre }}</span>
                    <a href="{{ route('admin.sales.index') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        ← Volver a Ventas
                    </a>
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
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Venta #{{ $sale->id }}</h1>
                <a href="{{ route('admin.sales.edit', $sale) }}" class="bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-600">
                    ✏️ Editar
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h3 class="text-lg font-semibold mb-3">Información General</h3>
                    <div class="space-y-2">
                        <p><strong>ID:</strong> #{{ $sale->id }}</p>
                        <p><strong>Cliente:</strong> {{ $sale->user->nombre }} ({{ $sale->user->email }})</p>
                        <p><strong>Estado:</strong>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($sale->estado_pago == 'pagado') bg-green-100 text-green-800
                                @elseif($sale->estado_pago == 'pendiente') bg-yellow text-yellow
                                @else bg-red text-red @endif">
                                {{ ucfirst($sale->estado_pago) }}
                            </span>
                        </p>
                        <p><strong>Fecha de Creación:</strong> {{ $sale->created_at->format('d/m/Y H:i') }}</p>
                        <p><strong>Última Actualización:</strong> {{ $sale->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-3">Resumen Financiero</h3>
                    <div class="bg-gray-50 p-4 rounded">
                        <p class="text-2xl font-bold text-green-600">${{ number_format($sale->total, 2) }}</p>
                        <p class="text-sm text-gray-600">Total de la Venta</p>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-semibold mb-3">Productos Vendidos</h3>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="border border-gray-300 px-4 py-2 text-left">Producto</th>
                                <th class="border border-gray-300 px-4 py-2 text-center">Cantidad</th>
                                <th class="border border-gray-300 px-4 py-2 text-right">Precio Unitario</th>
                                <th class="border border-gray-300 px-4 py-2 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sale->saleItems as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="border border-gray-300 px-4 py-2">{{ $item->product->nombre }}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">{{ $item->cantidad }}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-right">${{ number_format($item->precio_unitario, 2) }}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-right">${{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                            <tr class="bg-gray-50 font-bold">
                                <td colspan="3" class="border border-gray-300 px-4 py-2 text-right">TOTAL</td>
                                <td class="border border-gray-300 px-4 py-2 text-right">${{ number_format($sale->total, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>