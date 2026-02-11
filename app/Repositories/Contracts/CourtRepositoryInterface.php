<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Court;
use Illuminate\Support\Collection;

interface CourtRepositoryInterface
{
    public function all(): Collection;

    public function find(int $id): ?Court;

    public function create(array $data): Court;

    public function update(Court $court, array $data): Court;

    public function delete(Court $court): bool;
}
