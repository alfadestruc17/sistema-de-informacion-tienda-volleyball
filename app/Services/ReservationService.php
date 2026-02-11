<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Court;
use App\Models\Reservation;
use App\Repositories\Contracts\CourtRepositoryInterface;
use App\Repositories\Contracts\ReservationRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ReservationService
{
    public function __construct(
        private ReservationRepositoryInterface $reservationRepository,
        private CourtRepositoryInterface $courtRepository
    ) {
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getAll(): Collection
    {
        return $this->reservationRepository->all();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->reservationRepository->paginate($perPage);
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getByUser(int $userId): Collection
    {
        return $this->reservationRepository->findByUser($userId);
    }

    public function find(int $id): ?Reservation
    {
        return $this->reservationRepository->find($id);
    }

    public function isSlotAvailable(int $courtId, string $fecha, string $horaInicio, int $duracionHoras, ?int $excludeReservationId = null): bool
    {
        return $this->reservationRepository->isSlotAvailable($courtId, $fecha, $horaInicio, $duracionHoras, $excludeReservationId);
    }

    public function createReservation(array $data): Reservation
    {
        $court = $this->courtRepository->find((int) $data['court_id']);
        if (!$court instanceof Court) {
            throw new \InvalidArgumentException('Cancha no encontrada.');
        }

        $totalEstimado = $court->precio_por_hora * (int) $data['duracion_horas'];
        $data['total_estimado'] = $totalEstimado;
        $data['pagado_bool'] = $data['pagado_bool'] ?? false;

        return $this->reservationRepository->create($data);
    }

    public function updateReservation(Reservation $reservation, array $data): Reservation
    {
        $court = $this->courtRepository->find((int) $data['court_id']);
        if (!$court instanceof Court) {
            throw new \InvalidArgumentException('Cancha no encontrada.');
        }

        $data['total_estimado'] = $court->precio_por_hora * (int) $data['duracion_horas'];
        $data['pagado_bool'] = $data['pagado_bool'] ?? false;

        return $this->reservationRepository->update($reservation, $data);
    }

    public function cancelReservation(Reservation $reservation): Reservation
    {
        return $this->reservationRepository->update($reservation, ['estado' => 'cancelada']);
    }

    public function markAsPaid(Reservation $reservation): Reservation
    {
        return $this->reservationRepository->update($reservation, ['pagado_bool' => true]);
    }

    public function canDelete(Reservation $reservation): bool
    {
        return !($reservation->estado === 'confirmada' && $reservation->pagado_bool);
    }

    public function delete(Reservation $reservation): bool
    {
        return $this->reservationRepository->delete($reservation);
    }

    /**
     * Calendario semanal por cancha (para dashboard).
     *
     * @return array{week_start: string, week_end: string, courts: array}
     */
    public function getWeeklyCalendarData(string $weekStart, string $weekEnd): array
    {
        $courts = $this->courtRepository->all();
        $calendarData = [];

        foreach ($courts as $court) {
            $reservations = $this->reservationRepository->getByCourtAndWeek($court->id, $weekStart, $weekEnd);
            $calendarData[] = [
                'court' => $court,
                'reservations' => $reservations->map(fn ($r) => [
                    'id' => $r->id,
                    'fecha' => $r->fecha->format('Y-m-d'),
                    'hora_inicio' => $r->hora_inicio,
                    'duracion_horas' => $r->duracion_horas,
                    'estado' => $r->estado,
                    'cliente' => $r->user->nombre ?? '',
                    'total' => $r->total_estimado,
                ])->toArray(),
            ];
        }

        return [
            'week_start' => $weekStart,
            'week_end' => $weekEnd,
            'courts' => $calendarData,
        ];
    }

    /**
     * Reservas confirmadas para una fecha (conteo).
     */
    public function countActiveByDate(Carbon $date): int
    {
        return \App\Models\Reservation::whereDate('fecha', $date)
            ->where('estado', 'confirmada')
            ->count();
    }

    /**
     * Conteo de reservas en rango de fechas.
     */
    public function countByDateRange(Carbon $start, Carbon $end): int
    {
        return \App\Models\Reservation::whereBetween('fecha', [$start, $end])->count();
    }

    /**
     * Conteo por día para una semana (para dashboard).
     *
     * @return array<int, array{date: string, reservations: int}>
     */
    public function getDailyCountsForWeek(Carbon $weekStart): array
    {
        $data = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $weekStart->copy()->addDays($i);
            $data[] = [
                'date' => $date->toDateString(),
                'reservations' => \App\Models\Reservation::whereDate('fecha', $date)->count(),
            ];
        }
        return $data;
    }
}
