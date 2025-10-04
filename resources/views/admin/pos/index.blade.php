<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Punto de Venta - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .product-item {
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .product-item:hover {
            background-color: #f3f4f6;
        }
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-xl font-bold text-gray-800">🏐 Volleyball Booking - POS Admin</span>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700">Admin: {{ Auth::user()->nombre }}</span>
                    <a href="{{ route('dashboard') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        ← Volver al Dashboard
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
        <div class="mb-6">
            <h1 class="text-3xl font-bold">Punto de Venta - Ventas Directas</h1>
            <p class="text-gray-600">Realiza ventas directas de productos</p>
        </div>

        <form id="sale-form" method="POST" action="{{ route('admin.pos.store') }}">
            @csrf
            <div class="grid grid-cols-3 gap-4">
                <!-- Panel de Productos -->
                <div class="col-span-2">
                    <h2 class="text-lg font-semibold mb-2">Productos Disponibles</h2>
                    <div id="products-grid" class="grid grid-cols-3 gap-2 mb-4">
                        <!-- Productos se cargarán dinámicamente -->
                    </div>

                    <div class="mb-4">
                        <input type="text" id="search-product" placeholder="Buscar producto..." class="border w-full p-2 rounded">
                    </div>
                </div>

                <!-- Panel de Venta -->
                <div>
                    <h2 class="text-lg font-semibold mb-2">Nueva Venta</h2>

                    <!-- Selector de Cliente -->
                    <div class="bg-white p-4 rounded shadow mb-4">
                        <h3 class="text-sm font-semibold mb-2">Cliente *</h3>
                        <select name="user_id" id="user-select" class="border w-full p-2 rounded" required>
                            <option value="">Seleccionar cliente...</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->nombre }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="bg-white p-4 rounded shadow mb-4">
                        <h3 class="text-sm font-semibold mb-2">Productos Seleccionados</h3>
                        <div id="selected-products" class="min-h-32">
                            <p class="text-gray-500 text-center py-8">No hay productos seleccionados</p>
                        </div>
                    </div>

                    <div class="bg-white p-4 rounded shadow">
                        <div class="flex justify-between font-bold text-lg">
                            <span>Total:</span>
                            <span id="total">$0.00</span>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" id="submit-btn" class="bg-green-500 text-white px-4 py-2 rounded w-full" disabled>
                            💰 Procesar Venta
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        let products = @json($products);
        let selectedProducts = {};

        // Cargar productos (ya están disponibles desde el controlador)
        function loadProducts() {
            displayProducts(products);
        }

        // Mostrar productos
        function displayProducts(productsList) {
            const grid = document.getElementById('products-grid');
            grid.innerHTML = '';

            productsList.forEach(product => {
                const productDiv = document.createElement('div');
                productDiv.className = 'product-item bg-white p-4 rounded shadow';
                productDiv.onclick = () => addProductToSale(product);
                productDiv.innerHTML = `
                    <div class="font-semibold text-sm">${product.nombre}</div>
                    <div class="text-gray-600 text-sm">$${parseFloat(product.precio).toFixed(2)}</div>
                    <div class="text-xs text-gray-500">Stock: ${product.stock}</div>
                `;
                grid.appendChild(productDiv);
            });
        }

        // Agregar producto a la venta
        function addProductToSale(product) {
            if (selectedProducts[product.id]) {
                selectedProducts[product.id].quantity++;
            } else {
                selectedProducts[product.id] = {
                    product: product,
                    quantity: 1
                };
            }
            updateSaleDisplay();
        }

        // Actualizar cantidad de producto
        function updateQuantity(productId, newQuantity) {
            if (newQuantity <= 0) {
                delete selectedProducts[productId];
            } else {
                selectedProducts[productId].quantity = newQuantity;
            }
            updateSaleDisplay();
        }

        // Actualizar display de la venta
        function updateSaleDisplay() {
            const container = document.getElementById('selected-products');
            const totalSpan = document.getElementById('total');
            const submitBtn = document.getElementById('submit-btn');

            container.innerHTML = '';
            let total = 0;
            let hasItems = false;

            for (const productId in selectedProducts) {
                hasItems = true;
                const item = selectedProducts[productId];
                const subtotal = item.quantity * item.product.precio;
                total += subtotal;

                const itemDiv = document.createElement('div');
                itemDiv.className = 'flex justify-between items-center mb-2 p-2 border-b';
                itemDiv.innerHTML = `
                    <span>${item.product.nombre}</span>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="updateQuantity(${productId}, ${item.quantity - 1})" class="text-red-500">-</button>
                        <span>x${item.quantity}</span>
                        <button type="button" onclick="updateQuantity(${productId}, ${item.quantity + 1})" class="text-green-500">+</button>
                    </div>
                    <span>$${subtotal.toFixed(2)}</span>
                    <button type="button" onclick="updateQuantity(${productId}, 0)" class="text-red-500 ml-2">×</button>
                `;
                container.appendChild(itemDiv);
            }

            if (!hasItems) {
                container.innerHTML = '<p class="text-gray-500 text-center py-8">No hay productos seleccionados</p>';
            }

            totalSpan.textContent = `$${total.toFixed(2)}`;
            submitBtn.disabled = !hasItems || !document.getElementById('user-select').value;

            // Actualizar form con items
            updateFormData();
        }

        // Actualizar datos del formulario
        function updateFormData() {
            // Limpiar items anteriores
            const form = document.getElementById('sale-form');
            const existingItems = form.querySelectorAll('input[name^="items["]');
            existingItems.forEach(item => item.remove());

            // Agregar items actuales
            let index = 0;
            for (const productId in selectedProducts) {
                const item = selectedProducts[productId];

                const productIdInput = document.createElement('input');
                productIdInput.type = 'hidden';
                productIdInput.name = `items[${index}][product_id]`;
                productIdInput.value = productId;

                const quantityInput = document.createElement('input');
                quantityInput.type = 'hidden';
                quantityInput.name = `items[${index}][quantity]`;
                quantityInput.value = item.quantity;

                form.appendChild(productIdInput);
                form.appendChild(quantityInput);
                index++;
            }
        }

        // Búsqueda de productos
        document.getElementById('search-product').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const filteredProducts = products.filter(product =>
                product.nombre.toLowerCase().includes(searchTerm)
            );
            displayProducts(filteredProducts);
        });

        // Validar formulario antes de enviar
        document.getElementById('user-select').addEventListener('change', function() {
            updateSaleDisplay();
        });

        // Inicializar
        document.addEventListener('DOMContentLoaded', function() {
            loadProducts();
        });
    </script>

    <!-- CSRF Token para AJAX -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</body>
</html>