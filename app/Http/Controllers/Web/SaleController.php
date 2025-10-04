<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index()
    {
        $sales = Sale::with(['user', 'saleItems.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.sales.index', compact('sales'));
    }

    public function show(Sale $sale)
    {
        $sale->load(['user', 'saleItems.product']);

        return view('admin.sales.show', compact('sale'));
    }

    public function edit(Sale $sale)
    {
        $sale->load(['user', 'saleItems.product']);
        $users = User::where('rol_id', '!=', 1)->get(); // Excluir admins

        return view('admin.sales.edit', compact('sale', 'users'));
    }

    public function update(Request $request, Sale $sale)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'estado_pago' => 'required|in:pendiente,pagado,cancelado',
        ]);

        $sale->update($request->only(['user_id', 'estado_pago']));

        return redirect()->route('admin.sales.index')->with('success', 'Venta actualizada exitosamente');
    }

    public function destroy(Sale $sale)
    {
        // Solo permitir eliminar ventas canceladas o pendientes
        if ($sale->estado_pago === 'pagado') {
            return redirect()->back()->with('error', 'No se puede eliminar una venta pagada');
        }

        // Restaurar stock de productos
        foreach ($sale->saleItems as $item) {
            $item->product->increment('stock', $item->cantidad);
        }

        $sale->delete();

        return redirect()->route('admin.sales.index')->with('success', 'Venta eliminada exitosamente');
    }
}
