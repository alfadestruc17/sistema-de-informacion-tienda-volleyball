<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Producto - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-xl font-bold text-gray-800">🏐 Arena Sport C.B - Nuevo Producto</span>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700">Admin: {{ Auth::user()->nombre }}</span>
                    <a href="{{ route('admin.products.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
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
        <div class="max-w-2xl mx-auto bg-white shadow-xl rounded-lg p-6">
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-800">Crear Nuevo Producto</h1>
                <p class="text-gray-600">Agrega un nuevo producto al inventario</p>
            </div>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white shadow-xl rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Crear Nuevo Producto</h1>
            <a href="{{ route('admin.products.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                <i class="fas fa-arrow-left mr-2"></i> Volver
            </a>
        </div>

        <form action="{{ route('admin.products.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                    Nombre del Producto *
                </label>
                <input type="text" name="nombre" id="nombre" required
                       class="w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('nombre') border-red-500 @enderror"
                       value="{{ old('nombre') }}" placeholder="Ej: Coca Cola 350ml">
                @error('nombre')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="categoria" class="block text-sm font-medium text-gray-700 mb-2">
                    Categoría *
                </label>
                <select name="categoria" id="categoria" required
                        class="w-full px-3 py-2 border  rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('categoria') border-red-500 @enderror">
                    <option value="">Seleccionar categoría</option>
                    <option value="Bebidas" {{ old('categoria') == 'Bebidas' ? 'selected' : '' }}>Bebidas</option>
                    <option value="Snacks" {{ old('categoria') == 'Snacks' ? 'selected' : '' }}>Snacks</option>
                    <option value="Accesorios" {{ old('categoria') == 'Accesorios' ? 'selected' : '' }}>Accesorios</option>
                    <option value="Otros" {{ old('categoria') == 'Otros' ? 'selected' : '' }}>Otros</option>
                </select>
                @error('categoria')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="precio" class="block text-sm font-medium text-gray-700 mb-2">
                        Precio (COP) *
                    </label>
                    <input type="number" name="precio" id="precio" required min="0" step="100"
                           class="w-full px-3 py-2 border  rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('precio') border-red-500 @enderror"
                           value="{{ old('precio') }}" placeholder="5000">
                    @error('precio')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">
                        Stock Inicial *
                    </label>
                    <input type="number" name="stock" id="stock" required min="0"
                           class="w-full px-3 py-2 border  rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('stock') border-red-500 @enderror"
                           value="{{ old('stock', 0) }}" placeholder="50">
                    @error('stock')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end space-x-4 pt-6">
                <a href="{{ route('admin.products.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                    Cancelar
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                    <i class="fas fa-save mr-2"></i> Crear Producto
                </button>
            </div>
        </form>
    </div>
</body>
</html>