<?php

namespace App\Domain\User\Services;

use App\Models\User;

class DeleteUserService
{
    public function execute(User $user): bool
    {
        return $user->delete();
    }
}
