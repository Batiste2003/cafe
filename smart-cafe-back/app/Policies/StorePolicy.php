<?php

namespace App\Policies;

use App\Domain\Store\Constant\StoreConstant;
use App\Domain\Store\Enumeration\StoreStatusEnum;
use App\Domain\User\Enumeration\UserPermissionEnum;
use App\Models\Store;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Policy pour la gestion des magasins.
 *
 * Gère les autorisations CRUD sur les magasins ainsi que
 * l'association/dissociation des utilisateurs.
 */
class StorePolicy
{
    /**
     * Détermine si l'utilisateur peut voir la liste des magasins.
     *
     * @param  User  $user  L'utilisateur authentifié
     * @return Response Autorisation ou refus
     */
    public function viewAny(User $user): Response
    {
        return $user->hasPermissionTo(UserPermissionEnum::STORE_VIEW->value)
            ? Response::allow()
            : Response::deny();
    }

    /**
     * Détermine si l'utilisateur peut voir un magasin spécifique.
     *
     * Les admins peuvent voir tous les magasins.
     * Les autres utilisateurs ne peuvent voir que les magasins actifs auxquels ils sont associés.
     *
     * @param  User  $user  L'utilisateur authentifié
     * @param  Store  $store  Le magasin à consulter
     * @return Response Autorisation ou refus
     */
    public function view(User $user, Store $store): Response
    {
        if (! $user->hasPermissionTo(UserPermissionEnum::STORE_VIEW->value)) {
            return Response::deny();
        }

        if ($user->isAdmin()) {
            return Response::allow();
        }

        // Les non-admins ne peuvent voir que les magasins actifs auxquels ils sont associés
        if ($store->status !== StoreStatusEnum::ACTIVE) {
            return Response::deny(StoreConstant::STORE_NOT_ACCESSIBLE);
        }

        if ($store->users()->where('users.id', $user->id)->exists()) {
            return Response::allow();
        }

        return Response::deny(StoreConstant::STORE_NOT_ACCESSIBLE);
    }

    /**
     * Détermine si l'utilisateur peut créer un magasin.
     *
     * @param  User  $user  L'utilisateur authentifié
     * @return Response Autorisation ou refus
     */
    public function create(User $user): Response
    {
        return $user->hasPermissionTo(UserPermissionEnum::STORE_CREATE->value)
            ? Response::allow()
            : Response::deny();
    }

    /**
     * Détermine si l'utilisateur peut modifier un magasin.
     *
     * @param  User  $user  L'utilisateur authentifié
     * @param  Store  $store  Le magasin à modifier
     * @return Response Autorisation ou refus
     */
    public function update(User $user, Store $store): Response
    {
        return $user->hasPermissionTo(UserPermissionEnum::STORE_UPDATE->value)
            ? Response::allow()
            : Response::deny();
    }

    /**
     * Détermine si l'utilisateur peut supprimer un magasin.
     *
     * @param  User  $user  L'utilisateur authentifié
     * @param  Store  $store  Le magasin à supprimer
     * @return Response Autorisation ou refus
     */
    public function delete(User $user, Store $store): Response
    {
        return $user->hasPermissionTo(UserPermissionEnum::STORE_DELETE->value)
            ? Response::allow()
            : Response::deny();
    }

    /**
     * Détermine si l'utilisateur peut associer des utilisateurs au magasin.
     *
     * @param  User  $user  L'utilisateur authentifié
     * @param  Store  $store  Le magasin concerné
     * @return Response Autorisation ou refus
     */
    public function attachUser(User $user, Store $store): Response
    {
        return $user->hasPermissionTo(UserPermissionEnum::STORE_CREATE->value)
            ? Response::allow()
            : Response::deny();
    }

    /**
     * Détermine si l'utilisateur peut dissocier des utilisateurs du magasin.
     *
     * @param  User  $user  L'utilisateur authentifié
     * @param  Store  $store  Le magasin concerné
     * @return Response Autorisation ou refus
     */
    public function detachUser(User $user, Store $store): Response
    {
        return $user->hasPermissionTo(UserPermissionEnum::STORE_UPDATE->value)
            ? Response::allow()
            : Response::deny();
    }
}
