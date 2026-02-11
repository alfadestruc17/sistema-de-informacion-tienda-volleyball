<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index(): View
    {
        $products = $this->productService->allOrderedByCategoryAndName();
        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        return view('admin.products.create');
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $this->productService->create($request->validated());
        return redirect()->route('admin.products.index')->with('success', 'Producto creado exitosamente');
    }

    public function show(Product $product): View
    {
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $this->productService->update($product, $request->validated());
        return redirect()->route('admin.products.index')->with('success', 'Producto actualizado exitosamente');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->productService->delete($product);
        return redirect()->route('admin.products.index')->with('success', 'Producto eliminado exitosamente');
    }
}
