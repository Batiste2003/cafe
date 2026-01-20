<?php

namespace App\Domain\Store\Services;

use App\Domain\Store\Constant\StoreConstant;
use App\Domain\Store\DTOs\AttachUsersToStoreDTO;
use App\Models\Store;
use App\Models\User;
use Illuminate\Validation\ValidationException;

/**
 * Service d'association d'utilisateurs à un magasin.
 */
class AttachUsersToStoreService
{
    /**
     * Associe des utilisateurs à un magasin.
     *
     * Règles métier :
     * - Les administrateurs ne peuvent pas être associés à un magasin
     * - Les employés ne peuvent être associés qu'à un seul magasin
     * - Les managers peuvent être associés à plusieurs magasins
     *
     * @param  Store  $store  Le magasin auquel associer les utilisateurs
     * @param  AttachUsersToStoreDTO  $dto  Les IDs des utilisateurs à associer
     * @return Store Le magasin mis à jour avec ses utilisateurs
     *
     * @throws ValidationException Si les règles métier sont violées
     */
    public function execute(Store $store, AttachUsersToStoreDTO $dto): Store
    {
        $users = User::whereIn('id', $dto->userIds)->with('roles')->get();

        foreach ($users as $user) {
            // Un administrateur ne peut pas être associé à un magasin
            if ($user->isAdmin()) {
                throw ValidationException::withMessages([
                    'user_ids' => [StoreConstant::CANNOT_ATTACH_ADMIN],
                ]);
            }

            // Un employé ne peut être associé qu'à un seul magasin
            if ($user->isEmployer() && $user->stores()->exists()) {
                $existingStore = $user->stores()->first();
                if ($existingStore->id !== $store->id) {
                    throw ValidationException::withMessages([
                        'user_ids' => [StoreConstant::EMPLOYER_ALREADY_HAS_STORE],
                    ]);
                }
            }
        }

        // Associer les utilisateurs (syncWithoutDetaching évite les doublons)
        $store->users()->syncWithoutDetaching($dto->userIds);

        return $store->fresh()->load(['users.roles']);
    }
}
