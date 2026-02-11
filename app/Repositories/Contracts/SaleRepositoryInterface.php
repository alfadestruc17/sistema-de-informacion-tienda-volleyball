<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface SaleRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function find(int $id): ?Sale;

    public function getDailyTotal(Carbon $date): float;

    public function getWeeklyTotal(Carbon $weekStart, Carbon $weekEnd): float;

    public function getMonthlyTotal(Carbon $dateFrom): float;

    public function getTopProductsByQuantity(int $limit, Carbon $dateFrom): Collection;

    public function create(array $data): Sale;

    public function update(Sale $sale, array $data): Sale;

    public function delete(Sale $sale): bool;
}
