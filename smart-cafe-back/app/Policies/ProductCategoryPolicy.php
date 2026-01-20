<?php

namespace App\Policies;

use App\Domain\User\Enumeration\UserPermissionEnum;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Policy pour la gestion des catégories de produits.
 *
 * Gère les autorisations CRUD sur les catégories.
 * Seuls les admins peuvent créer/modifier/supprimer des catégories.
 * Tous les utilisateurs authentifiés avec la permission PRODUCT_VIEW peuvent voir les catégories.
 */
class ProductCategoryPolicy
{
    /**
     * Détermine si l'utilisateur peut voir la liste des catégories.
     *
     * @param  User  $user  L'utilisateur authentifié
     * @return Response Autorisation ou refus
     */
    public function viewAny(User $user): Response
    {
        return $user->hasPermissionTo(UserPermissionEnum::PRODUCT_VIEW->value)
            ? Response::allow()
            : Response::deny();
    }

    /**
     * Détermine si l'utilisateur peut voir une catégorie spécifique.
     *
     * @param  User  $user  L'utilisateur authentifié
     * @param  ProductCategory  $category  La catégorie à consulter
     * @return Response Autorisation ou refus
     */
    public function view(User $user, ProductCategory $category): Response
    {
        return $user->hasPermissionTo(UserPermissionEnum::PRODUCT_VIEW->value)
            ? Response::allow()
            : Response::deny();
    }

    /**
     * Détermine si l'utilisateur peut créer une catégorie.
     *
     * @param  User  $user  L'utilisateur authentifié
     * @return Response Autorisation ou refus
     */
    public function create(User $user): Response
    {
        return $user->hasPermissionTo(UserPermissionEnum::PRODUCT_CREATE->value)
            ? Response::allow()
            : Response::deny();
    }

    /**
     * Détermine si l'utilisateur peut modifier une catégorie.
     *
     * @param  User  $user  L'utilisateur authentifié
     * @param  ProductCategory  $category  La catégorie à modifier
     * @return Response Autorisation ou refus
     */
    public function update(User $user, ProductCategory $category): Response
    {
        return $user->hasPermissionTo(UserPermissionEnum::PRODUCT_UPDATE->value)
            ? Response::allow()
            : Response::deny();
    }

    /**
     * Détermine si l'utilisateur peut supprimer une catégorie.
     *
     * @param  User  $user  L'utilisateur authentifié
     * @param  ProductCategory  $category  La catégorie à supprimer
     * @return Response Autorisation ou refus
     */
    public function delete(User $user, ProductCategory $category): Response
    {
        return $user->hasPermissionTo(UserPermissionEnum::PRODUCT_DELETE->value)
            ? Response::allow()
            : Response::deny();
    }
}
