<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {
        $this->middleware('auth:sanctum');
        $this->middleware('role:admin,cajero')->except(['index', 'show']);
    }

    public function index(): JsonResponse
    {
        return response()->json($this->productService->all());
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->productService->create($request->validated());
        return response()->json($product, 201);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json($product);
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $product = $this->productService->update($product, $request->validated());
        return response()->json($product);
    }

    public function destroy(Product $product): JsonResponse
    {
        $this->productService->delete($product);
        return response()->json(['message' => 'Product deleted successfully']);
    }
}
