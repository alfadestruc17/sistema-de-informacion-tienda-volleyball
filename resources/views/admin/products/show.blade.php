@extends('layouts.app')

@section('title', 'Detalle producto')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">{{ $product->nombre }}</h1>
        <p class="text-slate-600 mt-1">Detalle del producto</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.products.edit', $product) }}" class="bg-sky-600 text-white px-4 py-2 rounded-lg hover:bg-sky-700 text-sm">Editar</a>
        <a href="{{ route('admin.products.index') }}" class="border border-slate-300 text-slate-700 px-4 py-2 rounded-lg hover:bg-slate-50 text-sm">Volver</a>
    </div>
</div>

<div class="bg-white border border-slate-200 rounded-lg shadow-sm p-6 max-w-2xl">
    <div class="space-y-4">
        <div>
            <span class="text-sm text-slate-500">Categoría</span>
            <p><span class="px-2 py-0.5 text-xs font-medium rounded-full bg-sky-100 text-sky-800">{{ $product->categoria }}</span></p>
        </div>
        <div>
            <span class="text-sm text-slate-500">Precio</span>
            <p class="text-xl font-semibold text-emerald-600">${{ number_format($product->precio, 0, ',', '.') }}</p>
        </div>
        <div>
            <span class="text-sm text-slate-500">Stock</span>
            <p class="text-xl font-semibold {{ $product->stock > 10 ? 'text-emerald-600' : ($product->stock > 0 ? 'text-amber-600' : 'text-red-600') }}">{{ $product->stock }}</p>
            <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $product->stock > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">{{ $product->stock > 0 ? 'Disponible' : 'Agotado' }}</span>
        </div>
        <div class="pt-4 border-t border-slate-200 text-sm text-slate-500">
            ID {{ $product->id }} · Creado {{ $product->created_at->format('d/m/Y H:i') }}
        </div>
    </div>
    <div class="mt-6 flex gap-2">
        <a href="{{ route('admin.products.edit', $product) }}" class="bg-sky-600 text-white px-4 py-2 rounded-lg hover:bg-sky-700 text-sm">Editar</a>
        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar este producto?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 text-sm">Eliminar</button>
        </form>
    </div>
</div>
@endsection
