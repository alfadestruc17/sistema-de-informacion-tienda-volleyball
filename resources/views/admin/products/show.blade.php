@extends('layouts.app')

@section('title', 'Detalle del Producto')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white shadow-xl rounded-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800">Detalle del Producto</h1>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.products.edit', $product) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                        <i class="fas fa-edit mr-2"></i> Editar
                    </a>
                    <a href="{{ route('admin.products.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                        <i class="fas fa-arrow-left mr-2"></i> Volver
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Información Principal -->
                <div class="space-y-6">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Información del Producto</h2>
                        <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Nombre</label>
                                <p class="text-lg font-medium text-gray-900">{{ $product->nombre }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Categoría</label>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $product->categoria }}
                                </span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Precio</label>
                                <p class="text-2xl font-bold text-green-600">${{ number_format($product->precio, 0, ',', '.') }} COP</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Stock Disponible</label>
                                <div class="flex items-center space-x-2">
                                    <span class="text-2xl font-bold {{ $product->stock > 10 ? 'text-green-600' : ($product->stock > 0 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ $product->stock }}
                                    </span>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $product->stock > 10 ? 'bg-green-100 text-green-800' : ($product->stock > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $product->stock > 10 ? 'Stock Alto' : ($product->stock > 0 ? 'Stock Bajo' : 'Agotado') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Estadísticas -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Estadísticas</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600">{{ $product->id }}</div>
                                <div class="text-sm text-blue-600">ID del Producto</div>
                            </div>
                            <div class="bg-purple-50 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-purple-600">{{ $product->stock > 0 ? 'Disponible' : 'Agotado' }}</div>
                                <div class="text-sm text-purple-600">Estado</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información del Sistema -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Información del Sistema</h3>
                        <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Fecha de Creación</label>
                                <p class="text-sm text-gray-900">{{ $product->created_at->format('d/m/Y H:i:s') }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Última Actualización</label>
                                <p class="text-sm text-gray-900">{{ $product->updated_at->format('d/m/Y H:i:s') }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600">ID Único</label>
                                <p class="text-sm font-mono text-gray-900">{{ $product->id }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Acciones Rápidas -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Acciones Rápidas</h3>
                        <div class="space-y-2">
                            <a href="{{ route('admin.products.edit', $product) }}" class="block w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300 text-center">
                                <i class="fas fa-edit mr-2"></i> Editar Producto
                            </a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="block" onsubmit="return confirm('¿Estás seguro de eliminar este producto? Esta acción no se puede deshacer.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                                    <i class="fas fa-trash mr-2"></i> Eliminar Producto
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Barra de navegación inferior -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <div class="flex justify-between">
                    <a href="{{ route('admin.products.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                        <i class="fas fa-list mr-2"></i> Ver Todos los Productos
                    </a>
                    <a href="{{ route('admin.products.create') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                        <i class="fas fa-plus mr-2"></i> Crear Nuevo Producto
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection