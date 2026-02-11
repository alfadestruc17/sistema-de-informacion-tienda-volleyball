<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sale\UpdateSaleRequest;
use App\Models\Sale;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\SaleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function __construct(
        private SaleService $saleService,
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function index(): View
    {
        $sales = $this->saleService->paginate(15);
        return view('admin.sales.index', compact('sales'));
    }

    public function show(Sale $sale): View
    {
        $sale->load(['user', 'saleItems.product']);
        return view('admin.sales.show', compact('sale'));
    }

    public function edit(Sale $sale): View
    {
        $sale->load(['user', 'saleItems.product']);
        $users = $this->userRepository->getNonAdmins();
        return view('admin.sales.edit', compact('sale', 'users'));
    }

    public function update(UpdateSaleRequest $request, Sale $sale): RedirectResponse
    {
        $this->saleService->update($sale, $request->validated());
        return redirect()->route('admin.sales.index')->with('success', 'Venta actualizada exitosamente');
    }

    public function destroy(Sale $sale): RedirectResponse
    {
        if (!$this->saleService->canDelete($sale)) {
            return redirect()->back()->with('error', 'No se puede eliminar una venta pagada');
        }
        $this->saleService->delete($sale);
        return redirect()->route('admin.sales.index')->with('success', 'Venta eliminada exitosamente');
    }
}
