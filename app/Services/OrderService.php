<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository
    ) {
    }

    public function create(int $userId, ?int $reservationId = null): Order
    {
        return $this->orderRepository->create([
            'user_id' => $userId,
            'reservation_id' => $reservationId,
            'total' => 0,
            'estado_pago' => 'pendiente',
        ]);
    }

    public function find(int $id): ?Order
    {
        return $this->orderRepository->find($id);
    }

    /**
     * @throws ValidationException
     */
    public function addItem(Order $order, int $productId, int $cantidad): Order
    {
        $product = Product::findOrFail($productId);

        if (!$product->hasStock($cantidad)) {
            throw ValidationException::withMessages([
                'cantidad' => ['Stock insuficiente para este producto.'],
            ]);
        }

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $productId,
            'cantidad' => $cantidad,
            'precio_unitario' => $product->precio,
        ]);

        $product->reduceStock($cantidad);
        $order->recalculateTotal();

        return $order->load('orderItems.product');
    }

    public function removeItem(Order $order, OrderItem $item): Order
    {
        if ($item->order_id !== $order->id) {
            throw new \InvalidArgumentException('El ítem no pertenece a esta orden.');
        }

        $item->product->addStock($item->cantidad);
        $item->delete();
        $order->recalculateTotal();

        return $order->load('orderItems.product');
    }

    public function closeOrder(Order $order): Order
    {
        $this->orderRepository->update($order, ['estado_pago' => 'pagado']);
        return $order->fresh();
    }
}
