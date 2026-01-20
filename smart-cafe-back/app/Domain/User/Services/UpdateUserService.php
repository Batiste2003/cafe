<?php

namespace App\Domain\User\Services;

use App\Domain\User\DTOs\UpdateUserInputDTO;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Service de mise à jour d'un utilisateur.
 */
class UpdateUserService
{
    /**
     * Met à jour les informations d'un utilisateur.
     *
     * Si l'email est modifié, email_verified_at est réinitialisé à null.
     *
     * @param  User  $user  L'utilisateur à modifier
     * @param  UpdateUserInputDTO  $dto  Les données de mise à jour
     * @return User L'utilisateur mis à jour avec ses rôles
     */
    public function execute(User $user, UpdateUserInputDTO $dto): User
    {
        $data = [];

        if ($dto->name !== null) {
            $data['name'] = $dto->name;
        }

        if ($dto->email !== null && $dto->email !== $user->email) {
            $data['email'] = $dto->email;
            $data['email_verified_at'] = null;
        }

        if ($dto->password !== null) {
            $data['password'] = Hash::make($dto->password);
        }

        if (! empty($data)) {
            $user->update($data);
        }

        return $user->fresh()->load('roles');
    }
}
