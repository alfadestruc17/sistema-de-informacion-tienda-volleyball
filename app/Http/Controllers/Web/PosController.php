<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Pos\AddOrderItemRequest;
use App\Http\Requests\Pos\AddToCartRequest;
use App\Http\Requests\Pos\LoadReservationRequest;
use App\Http\Requests\Pos\StorePosRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\OrderService;
use App\Services\PosService;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PosController extends Controller
{
    public function __construct(
        private ProductService $productService,
        private UserRepositoryInterface $userRepository,
        private OrderService $orderService,
        private PosService $posService
    ) {
    }

    public function index(): View
    {
        $products = $this->productService->getWithStock();
        $users = $this->userRepository->getNonAdmins();

        if (Auth::user()->role->nombre === 'cajero') {
            return view('pos.simple', compact('products', 'users'));
        }

        return view('admin.pos.index', compact('products', 'users'));
    }

    public function store(StorePosRequest $request): RedirectResponse
    {
        $this->posService->processSaleFromItems(
            (int) $request->validated('user_id'),
            $request->validated('items')
        );
        return redirect()->route('admin.pos.index')->with('success', 'Venta procesada exitosamente');
    }

    public function getProduct(Request $request): JsonResponse
    {
        $product = $this->productService->find((int) $request->product_id);
        if (!$product) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }
        return response()->json([
            'id' => $product->id,
            'nombre' => $product->nombre,
            'precio' => $product->precio,
            'stock' => $product->stock,
        ]);
    }

    public function createOrder(StoreOrderRequest $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }
        if (Auth::user()->role->nombre !== 'cajero') {
            return response()->json(['error' => 'Acceso denegado - Solo cajeros'], 403);
        }

        $order = $this->orderService->create(
            (int) Auth::id(),
            $request->validated('reservation_id') ? (int) $request->validated('reservation_id') : null
        );
        return response()->json($order->load('orderItems.product'));
    }

    public function addItem(AddOrderItemRequest $request, Order $order): JsonResponse
    {
        if (!Auth::check() || Auth::user()->role->nombre !== 'cajero') {
            return response()->json(['error' => 'Acceso denegado'], 403);
        }

        try {
            $order = $this->orderService->addItem(
                $order,
                (int) $request->validated('product_id'),
                (int) $request->validated('cantidad')
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
        return response()->json($order);
    }

    public function removeItem(Request $request, Order $order, OrderItem $item): JsonResponse
    {
        $order = $this->orderService->removeItem($order, $item);
        return response()->json($order);
    }

    public function closeOrder(Request $request, Order $order): JsonResponse
    {
        $this->orderService->closeOrder($order);
        return response()->json($order->fresh());
    }

    public function getReservation(Request $request, $id): JsonResponse
    {
        $reservation = $this->posService->findReservation((int) $id);
        if (!$reservation) {
            return response()->json(['error' => 'Reserva no encontrada'], 404);
        }
        $reservation->load('user', 'court');
        return response()->json($reservation);
    }

    public function addToCart(AddToCartRequest $request): RedirectResponse
    {
        $product = $this->productService->find((int) $request->validated('product_id'));
        if (!$product || !$product->hasStock($request->validated('cantidad'))) {
            return back()->with('error', 'Stock insuficiente para este producto');
        }

        $cart = session('cart', []);
        $cantidad = (int) $request->validated('cantidad');
        $found = false;
        foreach ($cart as &$item) {
            if ($item['product']['id'] == $product->id) {
                $item['cantidad'] += $cantidad;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $cart[] = ['product' => $product->toArray(), 'cantidad' => $cantidad];
        }
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['cantidad'] * $item['product']['precio'];
        }
        session(['cart' => $cart, 'cart_total' => $total]);
        return back()->with('success', 'Producto agregado al carrito');
    }

    public function removeFromCart(Request $request): RedirectResponse
    {
        $request->validate(['product_id' => ['required', 'exists:products,id']]);
        $cart = array_values(array_filter(session('cart', []), fn ($item) => $item['product']['id'] != $request->product_id));
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['cantidad'] * $item['product']['precio'];
        }
        session(['cart' => $cart, 'cart_total' => $total]);
        return back();
    }

    public function clearCart(Request $request): RedirectResponse
    {
        session()->forget(['cart', 'cart_total', 'current_order_id', 'reservation']);
        return back();
    }

    public function loadReservation(LoadReservationRequest $request): RedirectResponse
    {
        $reservation = $this->posService->findReservation((int) $request->validated('reservation_id'));
        session(['reservation' => $reservation->toArray()]);
        return back()->with('success', 'Reserva cargada exitosamente');
    }

    public function checkout(Request $request): RedirectResponse
    {
        $cart = session('cart', []);
        if (empty($cart)) {
            return back()->with('error', 'El carrito está vacío');
        }
        try {
            $this->posService->checkoutFromCart((int) Auth::id(), $cart);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
        session()->forget(['cart', 'cart_total', 'current_order_id', 'reservation']);
        return back()->with('success', 'Venta procesada exitosamente');
    }
}
