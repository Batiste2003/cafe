<?php

namespace App\Domain\User\Services;

use App\Models\User;

class RestoreUserService
{
    public function execute(User $user): User
    {
        $user->restore();

        return $user->fresh()->load('roles');
    }
}
