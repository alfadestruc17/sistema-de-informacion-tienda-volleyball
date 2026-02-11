<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Sale;
use App\Repositories\Contracts\SaleRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SaleRepository implements SaleRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Sale::with(['user', 'saleItems.product'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function find(int $id): ?Sale
    {
        return Sale::find($id);
    }

    public function getDailyTotal(Carbon $date): float
    {
        return (float) Sale::whereDate('created_at', $date)
            ->where('estado_pago', 'pagado')
            ->sum('total');
    }

    public function getWeeklyTotal(Carbon $weekStart, Carbon $weekEnd): float
    {
        return (float) Sale::whereBetween('created_at', [$weekStart, $weekEnd])
            ->where('estado_pago', 'pagado')
            ->sum('total');
    }

    public function getMonthlyTotal(Carbon $dateFrom): float
    {
        return (float) Sale::where('estado_pago', 'pagado')
            ->where('created_at', '>=', $dateFrom)
            ->sum('total');
    }

    public function getTopProductsByQuantity(int $limit, Carbon $dateFrom): Collection
    {
        return \App\Models\SaleItem::selectRaw('product_id, products.nombre, SUM(cantidad) as total_vendido, SUM(cantidad * precio_unitario) as total_ingresos')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.estado_pago', 'pagado')
            ->where('sales.created_at', '>=', $dateFrom)
            ->groupBy('product_id', 'products.nombre')
            ->orderBy('total_vendido', 'desc')
            ->limit($limit)
            ->get();
    }

    public function create(array $data): Sale
    {
        return Sale::create($data);
    }

    public function update(Sale $sale, array $data): Sale
    {
        $sale->update($data);
        return $sale->fresh();
    }

    public function delete(Sale $sale): bool
    {
        return $sale->delete();
    }
}
