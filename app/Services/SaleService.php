<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Sale;
use App\Repositories\Contracts\SaleRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SaleService
{
    public function __construct(
        private SaleRepositoryInterface $saleRepository
    ) {
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->saleRepository->paginate($perPage);
    }

    public function find(int $id): ?Sale
    {
        return $this->saleRepository->find($id);
    }

    public function update(Sale $sale, array $data): Sale
    {
        return $this->saleRepository->update($sale, $data);
    }

    public function canDelete(Sale $sale): bool
    {
        return $sale->estado_pago !== 'pagado';
    }

    public function delete(Sale $sale): bool
    {
        foreach ($sale->saleItems as $item) {
            $item->product->increment('stock', $item->cantidad);
        }
        return $this->saleRepository->delete($sale);
    }
}
