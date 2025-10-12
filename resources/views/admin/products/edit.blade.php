@extends('layouts.app')

@section('title', 'Editar Producto')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white shadow-xl rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Editar Producto</h1>
            <a href="{{ route('admin.products.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                <i class="fas fa-arrow-left mr-2"></i> Volver
            </a>
        </div>

        <form action="{{ route('admin.products.update', $product) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                    Nombre del Producto *
                </label>
                <input type="text" name="nombre" id="nombre" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('nombre') border-red-500 @enderror"
                       value="{{ old('nombre', $product->nombre) }}" placeholder="Ej: Coca Cola 350ml">
                @error('nombre')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="categoria" class="block text-sm font-medium text-gray-700 mb-2">
                    Categoría *
                </label>
                <select name="categoria" id="categoria" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('categoria') border-red-500 @enderror">
                    <option value="">Seleccionar categoría</option>
                    <option value="Bebidas" {{ old('categoria', $product->categoria) == 'Bebidas' ? 'selected' : '' }}>Bebidas</option>
                    <option value="Snacks" {{ old('categoria', $product->categoria) == 'Snacks' ? 'selected' : '' }}>Snacks</option>
                    <option value="Accesorios" {{ old('categoria', $product->categoria) == 'Accesorios' ? 'selected' : '' }}>Accesorios</option>
                    <option value="Otros" {{ old('categoria', $product->categoria) == 'Otros' ? 'selected' : '' }}>Otros</option>
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
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('precio') border-red-500 @enderror"
                           value="{{ old('precio', $product->precio) }}" placeholder="5000">
                    @error('precio')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">
                        Stock Actual *
                    </label>
                    <input type="number" name="stock" id="stock" required min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('stock') border-red-500 @enderror"
                           value="{{ old('stock', $product->stock) }}" placeholder="50">
                    @error('stock')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Información del Producto</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="font-medium">ID:</span> {{ $product->id }}
                    </div>
                    <div>
                        <span class="font-medium">Creado:</span> {{ $product->created_at->format('d/m/Y H:i') }}
                    </div>
                    <div>
                        <span class="font-medium">Última actualización:</span> {{ $product->updated_at->format('d/m/Y H:i') }}
                    </div>
                    <div>
                        <span class="font-medium">Estado:</span>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $product->stock > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $product->stock > 0 ? 'Disponible' : 'Agotado' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-4 pt-6">
                <a href="{{ route('admin.products.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                    Cancelar
                </a>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                    <i class="fas fa-save mr-2"></i> Actualizar Producto
                </button>
            </div>
        </form>
    </div>
</div>
@endsection