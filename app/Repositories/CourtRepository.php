<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Court;
use App\Repositories\Contracts\CourtRepositoryInterface;
use Illuminate\Support\Collection;

class CourtRepository implements CourtRepositoryInterface
{
    public function all(): Collection
    {
        return Court::all();
    }

    public function find(int $id): ?Court
    {
        return Court::find($id);
    }

    public function create(array $data): Court
    {
        return Court::create($data);
    }

    public function update(Court $court, array $data): Court
    {
        $court->update($data);
        return $court->fresh();
    }

    public function delete(Court $court): bool
    {
        return $court->delete();
    }
}
