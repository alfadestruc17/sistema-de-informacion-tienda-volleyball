<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class SalesReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $dateFrom;
    protected $dateTo;

    public function __construct($dateFrom = null, $dateTo = null)
    {
        $this->dateFrom = $dateFrom ?: Carbon::now()->startOfMonth();
        $this->dateTo = $dateTo ?: Carbon::now()->endOfMonth();
    }

    public function collection()
    {
        return Order::with(['user', 'orderItems.product', 'reservation'])
                   ->whereBetween('created_at', [$this->dateFrom, $this->dateTo])
                   ->where('estado_pago', true)
                   ->orderBy('created_at', 'desc')
                   ->get();
    }

    public function headings(): array
    {
        return [
            'ID Orden',
            'Fecha',
            'Cliente',
            'Reserva ID',
            'Productos',
            'Total',
            'Estado Pago'
        ];
    }

    public function map($order): array
    {
        $products = $order->orderItems->map(function ($item) {
            return $item->product->nombre . ' x' . $item->cantidad;
        })->join(', ');

        return [
            $order->id,
            $order->created_at->format('Y-m-d H:i'),
            $order->user->nombre,
            $order->reservation_id ?: 'N/A',
            $products,
            $order->total,
            $order->estado_pago ? 'Pagado' : 'Pendiente'
        ];
    }
}