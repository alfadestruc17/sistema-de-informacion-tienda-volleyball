<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Reservation;
use App\Repositories\Contracts\ReservationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ReservationRepository implements ReservationRepositoryInterface
{
    public function all(): Collection
    {
        return Reservation::with(['user', 'court'])->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Reservation::with(['user', 'court'])
            ->orderBy('fecha', 'desc')
            ->orderBy('hora_inicio', 'desc')
            ->paginate($perPage);
    }

    public function find(int $id): ?Reservation
    {
        return Reservation::find($id);
    }

    public function findByUser(int $userId): Collection
    {
        return Reservation::where('user_id', $userId)
            ->with('court')
            ->orderBy('fecha', 'desc')
            ->orderBy('hora_inicio', 'desc')
            ->get();
    }

    public function getByCourtAndDateRange(int $courtId, string $dateFrom, string $dateTo): Collection
    {
        return Reservation::where('court_id', $courtId)
            ->whereBetween('fecha', [$dateFrom, $dateTo])
            ->where('estado', '!=', 'cancelada')
            ->with(['court', 'user'])
            ->get();
    }

    public function getByDateRange(?int $courtId, string $dateFrom, string $dateTo): Collection
    {
        $query = Reservation::whereBetween('fecha', [$dateFrom, $dateTo])
            ->where('estado', '!=', 'cancelada')
            ->with(['court', 'user']);

        if ($courtId !== null) {
            $query->where('court_id', $courtId);
        }

        return $query->get();
    }

    public function getByCourtAndWeek(int $courtId, string $weekStart, string $weekEnd): Collection
    {
        return Reservation::where('court_id', $courtId)
            ->whereBetween('fecha', [$weekStart, $weekEnd])
            ->with('user')
            ->get();
    }

    public function isSlotAvailable(int $courtId, string $fecha, string $horaInicio, int $duracionHoras, ?int $excludeReservationId = null): bool
    {
        return Reservation::isSlotAvailable($courtId, $fecha, $horaInicio, $duracionHoras, $excludeReservationId);
    }

    public function create(array $data): Reservation
    {
        return Reservation::create($data);
    }

    public function update(Reservation $reservation, array $data): Reservation
    {
        $reservation->update($data);
        return $reservation->fresh();
    }

    public function delete(Reservation $reservation): bool
    {
        return $reservation->delete();
    }

    public function countByDateRange(string $dateFrom, string $dateTo): int
    {
        return Reservation::whereBetween('fecha', [$dateFrom, $dateTo])->count();
    }
}
