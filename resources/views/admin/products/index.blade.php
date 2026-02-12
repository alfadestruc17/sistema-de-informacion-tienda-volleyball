@extends('layouts.app')

@section('title', 'Productos')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Productos</h1>
        <p class="text-slate-600 mt-1">Inventario y precios</p>
    </div>
    <a href="{{ route('admin.products.create') }}" class="bg-sky-600 text-white px-4 py-2 rounded-lg hover:bg-sky-700 text-sm font-medium transition">Nuevo producto</a>
</div>

<div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-slate-600 uppercase tracking-wider">Nombre</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600 uppercase tracking-wider">Categoría</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600 uppercase tracking-wider">Precio</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600 uppercase tracking-wider">Stock</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600 uppercase tracking-wider">Estado</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($products as $product)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium text-slate-800">{{ $product->nombre }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-sky-100 text-sky-800">{{ $product->categoria }}</span>
                        </td>
                        <td class="px-4 py-3 text-slate-800">${{ number_format($product->precio, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">
                            <span class="{{ $product->stock > 10 ? 'text-emerald-600 font-medium' : ($product->stock > 0 ? 'text-amber-600 font-medium' : 'text-red-600 font-medium') }}">
                                {{ $product->stock }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if($product->stock > 0)
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-emerald-100 text-emerald-800">Disponible</span>
                            @else
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-800">Agotado</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.products.show', $product) }}" class="text-sky-600 hover:text-sky-800 mr-3">Ver</a>
                            <a href="{{ route('admin.products.edit', $product) }}" class="text-slate-600 hover:text-sky-600 mr-3">Editar</a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar este producto?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-slate-500">No hay productos</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
