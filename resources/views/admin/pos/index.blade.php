@extends('layouts.app')

@section('title', 'Punto de Venta')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Punto de Venta - Ventas directas</h1>
    <p class="text-slate-600 mt-1">Realiza ventas directas de productos</p>
</div>

<form id="sale-form" method="POST" action="{{ route('admin.pos.store') }}">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <h2 class="text-lg font-semibold text-slate-800 mb-2">Productos disponibles</h2>
            <div id="products-grid" class="grid grid-cols-2 sm:grid-cols-3 gap-2 mb-4">
                <!-- Productos se cargan por JS -->
            </div>
            <input type="text" id="search-product" placeholder="Buscar producto..." class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-sky-400 focus:border-sky-400">
        </div>
        <div>
            <h2 class="text-lg font-semibold text-slate-800 mb-2">Nueva venta</h2>
            <div class="bg-white border border-slate-200 rounded-lg shadow-sm p-4 mb-4">
                <h3 class="text-sm font-semibold text-slate-700 mb-2">Cliente *</h3>
                <select name="user_id" id="user-select" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-sky-400 focus:border-sky-400" required>
                    <option value="">Seleccionar cliente...</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->nombre }} ({{ $user->email }})</option>
                    @endforeach
                </select>
            </div>
            <div class="bg-white border border-slate-200 rounded-lg shadow-sm p-4 mb-4">
                <h3 class="text-sm font-semibold text-slate-700 mb-2">Productos seleccionados</h3>
                <div id="selected-products" class="min-h-32">
                    <p class="text-slate-500 text-center py-8 text-sm">No hay productos seleccionados</p>
                </div>
            </div>
            <div class="bg-white border border-slate-200 rounded-lg shadow-sm p-4 mb-4">
                <div class="flex justify-between font-bold text-lg text-slate-800">
                    <span>Total</span>
                    <span id="total" class="text-emerald-600">$0.00</span>
                </div>
            </div>
            <button type="submit" id="submit-btn" class="w-full bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                Procesar venta
            </button>
        </div>
    </div>
</form>

<script>
(function() {
    var products = @json($products);
    var selectedProducts = {};
    var grid = document.getElementById('products-grid');
    var searchEl = document.getElementById('search-product');
    var userSelect = document.getElementById('user-select');

    function displayProducts(productsList) {
        if (!grid) return;
        grid.innerHTML = '';
        productsList.forEach(function(product) {
            var div = document.createElement('div');
            div.className = 'product-item bg-white border border-slate-200 p-4 rounded-lg shadow-sm cursor-pointer hover:bg-sky-50 hover:border-sky-200 transition';
            div.onclick = function() { addProductToSale(product); };
            div.innerHTML = '<div class="font-semibold text-sm text-slate-800">' + product.nombre + '</div>' +
                '<div class="text-slate-600 text-sm">$' + parseFloat(product.precio).toFixed(2) + '</div>' +
                '<div class="text-xs text-slate-500">Stock: ' + product.stock + '</div>';
            grid.appendChild(div);
        });
    }

    function addProductToSale(product) {
        if (selectedProducts[product.id]) {
            selectedProducts[product.id].quantity++;
        } else {
            selectedProducts[product.id] = { product: product, quantity: 1 };
        }
        updateSaleDisplay();
    }

    window.updateQuantity = function(productId, newQuantity) {
        if (newQuantity <= 0) {
            delete selectedProducts[productId];
        } else {
            selectedProducts[productId].quantity = newQuantity;
        }
        updateSaleDisplay();
    };

    function updateSaleDisplay() {
        var container = document.getElementById('selected-products');
        var totalSpan = document.getElementById('total');
        var submitBtn = document.getElementById('submit-btn');
        if (!container || !totalSpan || !submitBtn) return;
        container.innerHTML = '';
        var total = 0;
        var hasItems = false;
        for (var productId in selectedProducts) {
            hasItems = true;
            var item = selectedProducts[productId];
            var subtotal = item.quantity * item.product.precio;
            total += subtotal;
            var itemDiv = document.createElement('div');
            itemDiv.className = 'flex justify-between items-center mb-2 p-2 border-b border-slate-100';
            itemDiv.innerHTML = '<span class="text-sm text-slate-800">' + item.product.nombre + '</span>' +
                '<div class="flex items-center gap-2">' +
                '<button type="button" onclick="updateQuantity(' + productId + ',' + (item.quantity - 1) + ')" class="text-red-500 hover:text-red-600 text-sm">-</button>' +
                '<span class="text-slate-600 text-sm">x' + item.quantity + '</span>' +
                '<button type="button" onclick="updateQuantity(' + productId + ',' + (item.quantity + 1) + ')" class="text-emerald-500 hover:text-emerald-600 text-sm">+</button>' +
                '</div>' +
                '<span class="text-slate-700 text-sm">$' + subtotal.toFixed(2) + '</span>' +
                '<button type="button" onclick="updateQuantity(' + productId + ',0)" class="text-red-500 hover:text-red-600 ml-1">×</button>';
            container.appendChild(itemDiv);
        }
        if (!hasItems) {
            container.innerHTML = '<p class="text-slate-500 text-center py-8 text-sm">No hay productos seleccionados</p>';
        }
        totalSpan.textContent = '$' + total.toFixed(2);
        totalSpan.className = 'text-emerald-600';
        submitBtn.disabled = !hasItems || !userSelect || !userSelect.value;
        updateFormData();
    }

    function updateFormData() {
        var form = document.getElementById('sale-form');
        if (!form) return;
        var existing = form.querySelectorAll('input[name^="items["]');
        existing.forEach(function(el) { el.remove(); });
        var index = 0;
        for (var productId in selectedProducts) {
            var item = selectedProducts[productId];
            var i1 = document.createElement('input');
            i1.type = 'hidden';
            i1.name = 'items[' + index + '][product_id]';
            i1.value = productId;
            var i2 = document.createElement('input');
            i2.type = 'hidden';
            i2.name = 'items[' + index + '][quantity]';
            i2.value = item.quantity;
            form.appendChild(i1);
            form.appendChild(i2);
            index++;
        }
    }

    if (searchEl) {
        searchEl.addEventListener('input', function() {
            var term = this.value.toLowerCase();
            var filtered = products.filter(function(p) { return p.nombre.toLowerCase().indexOf(term) !== -1; });
            displayProducts(filtered);
        });
    }
    if (userSelect) userSelect.addEventListener('change', updateSaleDisplay);
    displayProducts(products);
})();
</script>
@endsection
