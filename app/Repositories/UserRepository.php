<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Collection;

class UserRepository implements UserRepositoryInterface
{
    public function find(int $id): ?User
    {
        return User::find($id);
    }

    public function findByIds(array $ids): Collection
    {
        if (empty($ids)) {
            return collect();
        }
        return User::whereIn('id', $ids)->get();
    }

    public function getNonAdmins(): Collection
    {
        return User::where('rol_id', '!=', 1)->get();
    }

    public function create(array $data): User
    {
        return User::create($data);
    }
}
