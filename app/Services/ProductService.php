<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Support\Collection;

class ProductService
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {
    }

    /**
     * @return Collection<int, Product>
     */
    public function all(): Collection
    {
        return $this->productRepository->all();
    }

    /**
     * @return Collection<int, Product>
     */
    public function allOrderedByCategoryAndName(): Collection
    {
        return $this->productRepository->allOrderedByCategoryAndName();
    }

    public function find(int $id): ?Product
    {
        return $this->productRepository->find($id);
    }

    /**
     * @return Collection<int, Product>
     */
    public function getWithStock(): Collection
    {
        return $this->productRepository->getWithStock();
    }

    public function create(array $data): Product
    {
        return $this->productRepository->create($data);
    }

    public function update(Product $product, array $data): Product
    {
        return $this->productRepository->update($product, $data);
    }

    public function delete(Product $product): bool
    {
        return $this->productRepository->delete($product);
    }
}
