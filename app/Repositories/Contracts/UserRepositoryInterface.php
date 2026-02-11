<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Support\Collection;

interface UserRepositoryInterface
{
    public function find(int $id): ?User;

    public function findByIds(array $ids): Collection;

    public function getNonAdmins(): Collection;

    public function create(array $data): User;
}
