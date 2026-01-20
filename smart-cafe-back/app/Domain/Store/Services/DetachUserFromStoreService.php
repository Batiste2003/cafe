<?php

namespace App\Domain\Store\Services;

use App\Domain\Store\Constant\StoreConstant;
use App\Models\Store;
use App\Models\User;
use Illuminate\Validation\ValidationException;

/**
 * Service de dissociation d'un utilisateur d'un magasin.
 */
class DetachUserFromStoreService
{
    /**
     * Dissocie un utilisateur d'un magasin.
     *
     * @param  Store  $store  Le magasin duquel dissocier l'utilisateur
     * @param  User  $user  L'utilisateur à dissocier
     * @return Store Le magasin mis à jour
     *
     * @throws ValidationException Si l'utilisateur n'est pas associé au magasin
     */
    public function execute(Store $store, User $user): Store
    {
        if (! $store->users()->where('users.id', $user->id)->exists()) {
            throw ValidationException::withMessages([
                'user' => [StoreConstant::USER_NOT_ATTACHED],
            ]);
        }

        $store->users()->detach($user->id);

        return $store->fresh()->load(['users.roles']);
    }
}
