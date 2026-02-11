<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\ReservationRepositoryInterface;
use App\Repositories\Contracts\SaleRepositoryInterface;
use Illuminate\Support\Facades\DB;

class PosService
{
    public function __construct(
        private SaleRepositoryInterface $saleRepository,
        private ProductRepositoryInterface $productRepository,
        private ReservationRepositoryInterface $reservationRepository
    ) {
    }

    /**
     * Procesar venta desde ítems (array de product_id + quantity).
     *
     * @param array<int, array{product_id: int, quantity: int}> $items
     * @throws \Exception
     */
    public function processSaleFromItems(int $userId, array $items): Sale
    {
        return DB::transaction(function () use ($userId, $items) {
            foreach ($items as $item) {
                $product = $this->productRepository->find($item['product_id']);
                if (!$product || !$product->hasStock($item['quantity'])) {
                    throw new \Exception("Stock insuficiente para el producto. Stock disponible: " . ($product->stock ?? 0));
                }
            }

            $sale = $this->saleRepository->create([
                'user_id' => $userId,
                'total' => 0,
                'estado_pago' => 'pagado',
            ]);

            $total = 0;
            foreach ($items as $item) {
                $product = $this->productRepository->find($item['product_id']);
                $subtotal = $item['quantity'] * $product->precio;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'cantidad' => $item['quantity'],
                    'precio_unitario' => $product->precio,
                ]);

                $product->reduceStock($item['quantity']);
                $total += $subtotal;
            }

            $this->saleRepository->update($sale, ['total' => $total]);

            return $sale->fresh();
        });
    }

    /**
     * Checkout desde carrito en sesión (array de items con product y cantidad).
     *
     * @param array<int, array{product: array, cantidad: int}> $cart
     * @throws \Exception
     */
    public function checkoutFromCart(int $userId, array $cart): Sale
    {
        return DB::transaction(function () use ($userId, $cart) {
            foreach ($cart as $item) {
                $product = Product::find($item['product']['id']);
                if (!$product || !$product->hasStock($item['cantidad'])) {
                    throw new \Exception("Stock insuficiente para {$product->nombre}. Stock disponible: {$product->stock}");
                }
            }

            $sale = $this->saleRepository->create([
                'user_id' => $userId,
                'total' => 0,
                'estado_pago' => 'pagado',
            ]);

            $total = 0;
            foreach ($cart as $item) {
                $product = Product::find($item['product']['id']);
                $subtotal = $item['cantidad'] * $product->precio;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $product->precio,
                ]);

                $product->reduceStock($item['cantidad']);
                $total += $subtotal;
            }

            $this->saleRepository->update($sale, ['total' => $total]);

            return $sale->fresh();
        });
    }

    public function findReservation(int $id)
    {
        return $this->reservationRepository->find($id);
    }
}
