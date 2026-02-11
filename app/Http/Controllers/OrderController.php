<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Order\StoreOrderRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService
    ) {
        $this->middleware('auth:sanctum');
        $this->middleware('role:admin,cajero');
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = $this->orderService->create(
            (int) auth()->id(),
            $request->validated('reservation_id')
        );
        return response()->json($order->load('orderItems'), 201);
    }

    public function show(Order $order): JsonResponse
    {
        $order->load(['orderItems.product', 'user', 'reservation']);
        $order->total_calculado = $order->calculateTotal();
        return response()->json($order);
    }

    public function addItem(Request $request, Order $order): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'cantidad' => ['required', 'integer', 'min:1'],
        ]);

        try {
            $order = $this->orderService->addItem(
                $order,
                (int) $validated['product_id'],
                (int) $validated['cantidad']
            );
        } catch (ValidationException $e) {
            throw $e;
        }

        return response()->json($order);
    }

    public function removeItem(Request $request, Order $order, OrderItem $item): JsonResponse
    {
        if ($item->order_id !== $order->id) {
            return response()->json(['message' => 'Item no pertenece a esta orden'], 403);
        }

        $quantityToRemove = $request->input('cantidad', $item->cantidad);

        if ($quantityToRemove >= $item->cantidad) {
            $order = $this->orderService->removeItem($order, $item);
        } else {
            $item->product->addStock($quantityToRemove);
            $item->update(['cantidad' => $item->cantidad - $quantityToRemove]);
            $order->recalculateTotal();
            $order = $order->load('orderItems.product');
        }

        return response()->json($order);
    }

    public function close(Order $order): JsonResponse
    {
        if ($order->estado_pago === 'pagado' || $order->estado_pago === true) {
            return response()->json(['message' => 'La orden ya está cerrada'], 400);
        }

        DB::transaction(function () use ($order) {
            foreach ($order->orderItems as $item) {
                $item->product->reduceStock($item->cantidad);
            }
            $this->orderService->closeOrder($order);
        });

        return response()->json([
            'message' => 'Orden cerrada exitosamente',
            'order' => $order->load('orderItems.product'),
        ]);
    }
}
