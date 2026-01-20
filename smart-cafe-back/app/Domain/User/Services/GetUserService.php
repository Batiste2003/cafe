<?php

namespace App\Domain\User\Services;

use App\Models\User;

class GetUserService
{
    public function execute(int $id, bool $withTrashed = false): ?User
    {
        $query = User::query()->with('roles');

        if ($withTrashed) {
            $query->withTrashed();
        }

        return $query->find($id);
    }
}
