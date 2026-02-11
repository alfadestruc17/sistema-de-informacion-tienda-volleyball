<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Reservation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ReservationRepositoryInterface
{
    public function all(): Collection;

    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function find(int $id): ?Reservation;

    public function findByUser(int $userId): Collection;

    public function getByCourtAndDateRange(int $courtId, string $dateFrom, string $dateTo): Collection;

    /** @param int|null $courtId Si es null, devuelve reservas de todas las canchas en el rango */
    public function getByDateRange(?int $courtId, string $dateFrom, string $dateTo): Collection;

    public function getByCourtAndWeek(int $courtId, string $weekStart, string $weekEnd): Collection;

    public function isSlotAvailable(int $courtId, string $fecha, string $horaInicio, int $duracionHoras, ?int $excludeReservationId = null): bool;

    public function create(array $data): Reservation;

    public function update(Reservation $reservation, array $data): Reservation;

    public function delete(Reservation $reservation): bool;

    public function countByDateRange(string $dateFrom, string $dateTo): int;
}
