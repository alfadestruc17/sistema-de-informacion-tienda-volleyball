@extends('layouts.app')

@section('title', 'Nuevo producto')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Nuevo producto</h1>
    <p class="text-slate-600 mt-1">Agregar producto al inventario</p>
</div>

<div class="bg-white border border-slate-200 rounded-lg shadow-sm p-6 max-w-2xl">
    <form action="{{ route('admin.products.store') }}" method="POST" class="space-y-5">
        @csrf
        <div>
            <label for="nombre" class="block text-sm font-medium text-slate-700 mb-2">Nombre *</label>
            <input type="text" name="nombre" id="nombre" required class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 @error('nombre') border-red-500 @enderror" value="{{ old('nombre') }}" placeholder="Ej: Bebida 350ml">
            @error('nombre')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="categoria" class="block text-sm font-medium text-slate-700 mb-2">Categoría *</label>
            <select name="categoria" id="categoria" required class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 @error('categoria') border-red-500 @enderror">
                <option value="">Seleccionar</option>
                <option value="Bebidas" {{ old('categoria') == 'Bebidas' ? 'selected' : '' }}>Bebidas</option>
                <option value="Snacks" {{ old('categoria') == 'Snacks' ? 'selected' : '' }}>Snacks</option>
                <option value="Accesorios" {{ old('categoria') == 'Accesorios' ? 'selected' : '' }}>Accesorios</option>
                <option value="Otros" {{ old('categoria') == 'Otros' ? 'selected' : '' }}>Otros</option>
            </select>
            @error('categoria')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label for="precio" class="block text-sm font-medium text-slate-700 mb-2">Precio *</label>
                <input type="number" name="precio" id="precio" required min="0" step="0.01" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 @error('precio') border-red-500 @enderror" value="{{ old('precio') }}" placeholder="0">
                @error('precio')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="stock" class="block text-sm font-medium text-slate-700 mb-2">Stock *</label>
                <input type="number" name="stock" id="stock" required min="0" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 @error('stock') border-red-500 @enderror" value="{{ old('stock', 0) }}" placeholder="0">
                @error('stock')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="flex gap-2 pt-2">
            <a href="{{ route('admin.products.index') }}" class="border border-slate-300 text-slate-700 px-4 py-2 rounded-lg hover:bg-slate-50">Cancelar</a>
            <button type="submit" class="bg-sky-600 text-white px-4 py-2 rounded-lg hover:bg-sky-700">Crear producto</button>
        </div>
    </form>
</div>
@endsection
