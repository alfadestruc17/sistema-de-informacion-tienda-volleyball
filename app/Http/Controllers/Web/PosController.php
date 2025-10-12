<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PosController extends Controller
{
    public function index()
    {
        $products = Product::where('stock', '>', 0)->get();
        $users = User::where('rol_id', '!=', 1)->get(); // Excluir admins

        // Si es cajero, usar vista simple sin AJAX
        if (Auth::user()->role->nombre === 'cajero') {
            return view('pos.simple', compact('products', 'users'));
        }

        return view('admin.pos.index', compact('products', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {
            // Verificar stock disponible
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                if (!$product->hasStock($item['quantity'])) {
                    throw new \Exception("Stock insuficiente para {$product->nombre}. Stock disponible: {$product->stock}");
                }
            }

            // Crear la venta
            $sale = Sale::create([
                'user_id' => $request->user_id,
                'total' => 0, // Se calculará después
                'estado_pago' => 'pagado', // Asumir pagado por defecto
            ]);

            $total = 0;

            // Crear items de venta y reducir stock
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                $subtotal = $item['quantity'] * $product->precio;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'cantidad' => $item['quantity'],
                    'precio_unitario' => $product->precio,
                ]);

                // Reducir stock
                $product->reduceStock($item['quantity']);
                $total += $subtotal;
            }

            // Actualizar total de la venta
            $sale->update(['total' => $total]);
        });

        return redirect()->route('admin.pos.index')->with('success', 'Venta procesada exitosamente');
    }

    public function getProduct(Request $request)
    {
        $product = Product::find($request->product_id);

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

    public function createOrder(Request $request)
    {
        // Verificar que el usuario esté autenticado y sea cajero
        if (!Auth::check()) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

        $user = Auth::user();
        if (!$user->role || $user->role->nombre !== 'cajero') {
            return response()->json(['error' => 'Acceso denegado - Solo cajeros'], 403);
        }

        $request->validate([
            'reservation_id' => 'nullable|exists:reservations,id',
        ]);

        $order = \App\Models\Order::create([
            'user_id' => Auth::id(),
            'reservation_id' => $request->reservation_id,
            'total' => 0,
            'estado_pago' => 'pendiente',
        ]);

        return response()->json($order->load('order_items.product'));
    }

    public function addItem(Request $request, \App\Models\Order $order)
    {
        // Verificar que el usuario esté autenticado y sea cajero
        if (!Auth::check()) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

        $user = Auth::user();
        if (!$user->role || $user->role->nombre !== 'cajero') {
            return response()->json(['error' => 'Acceso denegado - Solo cajeros'], 403);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'cantidad' => 'required|integer|min:1',
        ]);

        $product = Product::find($request->product_id);

        if (!$product->hasStock($request->cantidad)) {
            return response()->json(['error' => 'Stock insuficiente'], 400);
        }

        $orderItem = \App\Models\OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $request->product_id,
            'cantidad' => $request->cantidad,
            'precio_unitario' => $product->precio,
        ]);

        // Reducir stock
        $product->reduceStock($request->cantidad);

        // Recalcular total
        $order->recalculateTotal();

        return response()->json($order->load('order_items.product'));
    }

    public function removeItem(Request $request, \App\Models\Order $order, \App\Models\OrderItem $item)
    {
        // Devolver stock
        $item->product->addStock($item->cantidad);

        $item->delete();

        // Recalcular total
        $order->recalculateTotal();

        return response()->json($order->load('order_items.product'));
    }

    public function closeOrder(Request $request, \App\Models\Order $order)
    {
        $order->update(['estado_pago' => 'pagado']);

        return response()->json($order);
    }

    public function getReservation(Request $request, $id)
    {
        $reservation = \App\Models\Reservation::with('user', 'court')->find($id);

        if (!$reservation) {
            return response()->json(['error' => 'Reserva no encontrada'], 404);
        }

        return response()->json($reservation);
    }

    // Métodos para POS simple con sesiones
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'cantidad' => 'required|integer|min:1',
        ]);

        $product = Product::find($request->product_id);

        if (!$product->hasStock($request->cantidad)) {
            return back()->with('error', 'Stock insuficiente para este producto');
        }

        $cart = session('cart', []);

        // Verificar si el producto ya está en el carrito
        $found = false;
        foreach ($cart as &$item) {
            if ($item['product']['id'] == $product->id) {
                $item['cantidad'] += $request->cantidad;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $cart[] = [
                'product' => $product->toArray(),
                'cantidad' => $request->cantidad,
            ];
        }

        // Calcular total
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['cantidad'] * $item['product']['precio'];
        }

        session(['cart' => $cart, 'cart_total' => $total]);

        return back()->with('success', 'Producto agregado al carrito');
    }

    public function removeFromCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $cart = session('cart', []);
        $productId = $request->product_id;

        // Remover producto del carrito
        $cart = array_filter($cart, function($item) use ($productId) {
            return $item['product']['id'] != $productId;
        });

        // Recalcular total
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['cantidad'] * $item['product']['precio'];
        }

        session(['cart' => array_values($cart), 'cart_total' => $total]);

        return back();
    }

    public function clearCart(Request $request)
    {
        session()->forget(['cart', 'cart_total', 'current_order_id', 'reservation']);
        return back();
    }

    public function loadReservation(Request $request)
    {
        $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
        ]);

        $reservation = \App\Models\Reservation::with('user', 'court')->find($request->reservation_id);

        session(['reservation' => $reservation->toArray()]);

        return back()->with('success', 'Reserva cargada exitosamente');
    }

    public function checkout(Request $request)
    {
        $cart = session('cart', []);
        $reservation = session('reservation');

        if (empty($cart)) {
            return back()->with('error', 'El carrito está vacío');
        }

        DB::transaction(function () use ($cart, $reservation) {
            // Verificar stock disponible antes de procesar
            foreach ($cart as $item) {
                $product = Product::find($item['product']['id']);
                if (!$product->hasStock($item['cantidad'])) {
                    throw new \Exception("Stock insuficiente para {$product->nombre}. Stock disponible: {$product->stock}");
                }
            }

            // Crear venta
            $sale = Sale::create([
                'user_id' => Auth::id(),
                'total' => 0, // Se calculará después
                'estado_pago' => 'pagado',
            ]);

            $total = 0;

            // Crear items de venta y reducir stock
            foreach ($cart as $item) {
                $product = Product::find($item['product']['id']);
                $subtotal = $item['cantidad'] * $product->precio;

                \App\Models\SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product']['id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $product->precio,
                ]);

                // Reducir stock
                $product->reduceStock($item['cantidad']);
                $total += $subtotal;
            }

            // Actualizar total de la venta
            $sale->update(['total' => $total]);

            // Si hay reserva asociada, vincularla
            if ($reservation) {
                // Aquí podríamos agregar lógica para vincular la venta a la reserva si es necesario
            }
        });

        // Limpiar carrito
        session()->forget(['cart', 'cart_total', 'current_order_id', 'reservation']);

        return back()->with('success', 'Venta procesada exitosamente');
    }
}
