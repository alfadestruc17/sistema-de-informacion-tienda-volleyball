<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Court;
use App\Repositories\Contracts\CourtRepositoryInterface;
use App\Repositories\Contracts\ReservationRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class CourtService
{
    public function __construct(
        private CourtRepositoryInterface $courtRepository,
        private ReservationRepositoryInterface $reservationRepository
    ) {
    }

    /**
     * @return Collection<int, Court>
     */
    public function getAll(): Collection
    {
        return $this->courtRepository->all();
    }

    public function create(array $data): Court
    {
        return $this->courtRepository->create($data);
    }

    /**
     * Disponibilidad por rango de fechas con slots por día.
     *
     * @return array<int, array{date: string, court_id: int|null, available_slots: array, occupied_slots: array}>
     */
    public function getAvailability(?int $courtId, string $dateFrom, string $dateTo): array
    {
        $dateFromCarbon = Carbon::parse($dateFrom);
        $dateToCarbon = Carbon::parse($dateTo);

        $reservations = $this->reservationRepository->getByDateRange($courtId, $dateFrom, $dateTo);

        $availability = [];
        for ($date = $dateFromCarbon->copy(); $date->lte($dateToCarbon); $date->addDay()) {
            $dayReservations = $reservations->where('fecha', $date->toDateString());
            $occupiedSlots = [];
            $availableSlots = [];

            for ($hour = 8; $hour < 22; $hour++) {
                $slotStart = Carbon::createFromTime($hour, 0);
                $slotEnd = Carbon::createFromTime($hour + 1, 0);

                $isOccupied = false;
                foreach ($dayReservations as $reservation) {
                    $resStart = Carbon::parse($reservation->hora_inicio);
                    $resEnd = $resStart->copy()->addHours((int) $reservation->duracion_horas);
                    if ($slotStart->lt($resEnd) && $slotEnd->gt($resStart)) {
                        $isOccupied = true;
                        break;
                    }
                }

                if ($isOccupied) {
                    $occupiedSlots[] = [
                        'start_time' => $slotStart->format('H:i'),
                        'end_time' => $slotEnd->format('H:i'),
                        'reservation' => $dayReservations->first(),
                    ];
                } else {
                    $availableSlots[] = [
                        'start_time' => $slotStart->format('H:i'),
                        'end_time' => $slotEnd->format('H:i'),
                    ];
                }
            }

            $availability[] = [
                'date' => $date->toDateString(),
                'court_id' => $courtId,
                'available_slots' => $availableSlots,
                'occupied_slots' => $occupiedSlots,
            ];
        }

        return $availability;
    }

    public function getCourtsForAvailability(?int $courtId): Court|Collection
    {
        if ($courtId) {
            $court = $this->courtRepository->find($courtId);
            return $court ?? collect();
        }
        return $this->courtRepository->all();
    }
}
