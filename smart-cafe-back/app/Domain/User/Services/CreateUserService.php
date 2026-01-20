<?php

namespace App\Domain\User\Services;

use App\Domain\User\DTOs\CreateUserInputDTO;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateUserService
{
    public function execute(CreateUserInputDTO $dto): User
    {
        $user = User::create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
        ]);

        $user->assignRole($dto->role->value);

        return $user->load('roles');
    }
}
