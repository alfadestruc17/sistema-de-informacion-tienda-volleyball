<?php

namespace App\Exports;

use App\Models\Reservation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class ReservationsReportExport implements FromCollection, WithHeadings, WithMapping
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
        return Reservation::with(['user', 'court'])
                        ->whereBetween('fecha', [$this->dateFrom, $this->dateTo])
                        ->orderBy('fecha', 'desc')
                        ->orderBy('hora_inicio', 'desc')
                        ->get();
    }

    public function headings(): array
    {
        return [
            'ID Reserva',
            'Fecha',
            'Hora Inicio',
            'Duración (horas)',
            'Cancha',
            'Cliente',
            'Estado',
            'Total Estimado'
        ];
    }

    public function map($reservation): array
    {
        return [
            $reservation->id,
            $reservation->fecha,
            $reservation->hora_inicio,
            $reservation->duracion_horas,
            $reservation->court->nombre,
            $reservation->user->nombre,
            $reservation->estado,
            $reservation->total_estimado
        ];
    }
}