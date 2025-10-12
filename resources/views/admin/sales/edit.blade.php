<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Venta #{{ $sale->id }} - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-xl font-bold text-gray-800">🏐 Arena Sport C.B - Editar Venta #{{ $sale->id }}</span>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700">Admin: {{ Auth::user()->nombre }}</span>
                    <a href="{{ route('admin.sales.show', $sale) }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        ← Volver
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
            <h1 class="text-2xl font-bold mb-6">Editar Venta #{{ $sale->id }}</h1>

            <form method="POST" action="{{ route('admin.sales.update', $sale) }}">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">Cliente</label>
                        <select name="user_id" id="user_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ $sale->user_id == $user->id ? 'selected' : '' }}>
                                    {{ $user->nombre }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="estado_pago" class="block text-sm font-medium text-gray-700 mb-2">Estado de Pago</label>
                        <select name="estado_pago" id="estado_pago" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="pendiente" {{ $sale->estado_pago == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="pagado" {{ $sale->estado_pago == 'pagado' ? 'selected' : '' }}>Pagado</option>
                            <option value="cancelado" {{ $sale->estado_pago == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                    </div>
                </div>

                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-3">Productos en la Venta</h3>
                    <div class="bg-gray-50 p-4 rounded">
                        <div class="space-y-2">
                            @foreach($sale->saleItems as $item)
                                <div class="flex justify-between items-center p-2 bg-white rounded">
                                    <span>{{ $item->product->nombre }}</span>
                                    <span>x{{ $item->cantidad }} = ${{ number_format($item->subtotal, 2) }}</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="flex justify-between font-bold text-lg">
                                <span>Total:</span>
                                <span>${{ number_format($sale->total, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 mt-2">
                        Nota: Los productos no se pueden modificar desde aquí. Si necesitas cambiar productos, elimina esta venta y crea una nueva.
                    </p>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('admin.sales.show', $sale) }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                        Cancelar
                    </a>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        💾 Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>