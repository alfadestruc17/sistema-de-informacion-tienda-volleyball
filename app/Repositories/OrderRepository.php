<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class OrderRepository implements OrderRepositoryInterface
{
    public function find(int $id): ?Order
    {
        return Order::find($id);
    }

    public function getDailyTotal(Carbon $date): float
    {
        return (float) Order::whereDate('created_at', $date)
            ->where('estado_pago', 'pagado')
            ->sum('total');
    }

    public function getWeeklyTotal(Carbon $weekStart, Carbon $weekEnd): float
    {
        return (float) Order::whereBetween('created_at', [$weekStart, $weekEnd])
            ->where('estado_pago', 'pagado')
            ->sum('total');
    }

    public function getMonthlyTotal(Carbon $dateFrom): float
    {
        return (float) Order::where('estado_pago', 'pagado')
            ->where('created_at', '>=', $dateFrom)
            ->sum('total');
    }

    public function create(array $data): Order
    {
        return Order::create($data);
    }

    public function update(Order $order, array $data): Order
    {
        $order->update($data);
        return $order->fresh();
    }
}
