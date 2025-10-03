<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:admin,cajero');
    }

    /**
     * Crear una nueva orden
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'reservation_id' => 'nullable|exists:reservations,id',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['total'] = 0;
        $validated['estado_pago'] = false;

        $order = Order::create($validated);

        return response()->json($order->load('orderItems'), 201);
    }

    /**
     * Mostrar orden con items
     */
    public function show(Order $order): JsonResponse
    {
        $order->load(['orderItems.product', 'user', 'reservation']);
        $order->total_calculado = $order->calculateTotal();

        return response()->json($order);
    }

    /**
     * Agregar item a la orden
     */
    public function addItem(Request $request, Order $order): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'cantidad' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        // Verificar stock
        if (!$product->hasStock($validated['cantidad'])) {
            throw ValidationException::withMessages([
                'cantidad' => "Stock insuficiente. Disponible: {$product->stock}"
            ]);
        }

        DB::transaction(function () use ($order, $product, $validated) {
            // Verificar si el producto ya existe en la orden
            $existingItem = $order->orderItems()->where('product_id', $product->id)->first();

            if ($existingItem) {
                // Actualizar cantidad existente
                $newQuantity = $existingItem->cantidad + $validated['cantidad'];
                if (!$product->hasStock($newQuantity)) {
                    throw ValidationException::withMessages([
                        'cantidad' => "Stock insuficiente para cantidad total. Disponible: {$product->stock}"
                    ]);
                }
                $existingItem->update(['cantidad' => $newQuantity]);
            } else {
                // Crear nuevo item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'cantidad' => $validated['cantidad'],
                    'precio_unitario' => $product->precio,
                ]);
            }

            // Actualizar total de la orden
            $order->update(['total' => $order->calculateTotal()]);
        });

        return response()->json($order->load('orderItems.product'));
    }

    /**
     * Quitar item de la orden
     */
    public function removeItem(Request $request, Order $order, OrderItem $item): JsonResponse
    {
        // Verificar que el item pertenece a la orden
        if ($item->order_id !== $order->id) {
            return response()->json(['message' => 'Item no pertenece a esta orden'], 403);
        }

        $validated = $request->validate([
            'cantidad' => 'nullable|integer|min:1',
        ]);

        $quantityToRemove = $validated['cantidad'] ?? $item->cantidad;

        if ($quantityToRemove >= $item->cantidad) {
            // Eliminar item completo
            $item->delete();
        } else {
            // Reducir cantidad
            $item->update(['cantidad' => $item->cantidad - $quantityToRemove]);
        }

        // Actualizar total
        $order->update(['total' => $order->calculateTotal()]);

        return response()->json($order->load('orderItems.product'));
    }

    /**
     * Cerrar orden (marcar como pagada y reducir stock)
     */
    public function close(Order $order): JsonResponse
    {
        if ($order->estado_pago) {
            return response()->json(['message' => 'La orden ya está cerrada'], 400);
        }

        DB::transaction(function () use ($order) {
            // Reducir stock de cada producto
            foreach ($order->orderItems as $item) {
                $item->product->reduceStock($item->cantidad);
            }

            // Marcar como pagada
            $order->update(['estado_pago' => true]);
        });

        return response()->json([
            'message' => 'Orden cerrada exitosamente',
            'order' => $order->load('orderItems.product')
        ]);
    }
}
