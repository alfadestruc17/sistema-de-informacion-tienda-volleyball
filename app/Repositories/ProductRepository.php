<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Support\Collection;

class ProductRepository implements ProductRepositoryInterface
{
    public function all(): Collection
    {
        return Product::all();
    }

    public function allOrderedByCategoryAndName(): Collection
    {
        return Product::orderBy('categoria')->orderBy('nombre')->get();
    }

    public function find(int $id): ?Product
    {
        return Product::find($id);
    }

    public function getWithStock(): Collection
    {
        return Product::where('stock', '>', 0)->get();
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);
        return $product->fresh();
    }

    public function delete(Product $product): bool
    {
        return $product->delete();
    }
}
