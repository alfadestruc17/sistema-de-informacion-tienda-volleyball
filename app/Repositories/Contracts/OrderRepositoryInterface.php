<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Collection;

interface OrderRepositoryInterface
{
    public function find(int $id): ?Order;

    public function getDailyTotal(Carbon $date): float;

    public function getWeeklyTotal(Carbon $weekStart, Carbon $weekEnd): float;

    public function getMonthlyTotal(Carbon $dateFrom): float;

    public function create(array $data): Order;

    public function update(Order $order, array $data): Order;
}
