<?php

namespace App\Policies;

use App\Domain\Product\Constant\ProductConstant;
use App\Domain\User\Enumeration\UserPermissionEnum;
use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Policy pour la gestion des produits.
 *
 * Gère les autorisations CRUD sur les produits.
 * Admin: CRUD complet sur tous les produits.
 * Manager/Employer: Lecture seule sur les produits de leurs magasins.
 */
class ProductPolicy
{
    /**
     * Détermine si l'utilisateur peut voir la liste des produits.
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
     * Détermine si l'utilisateur peut voir un produit spécifique.
     *
     * Les admins peuvent voir tous les produits.
     * Les autres utilisateurs ne peuvent voir que les produits actifs de leurs magasins associés.
     *
     * @param  User  $user  L'utilisateur authentifié
     * @param  Product  $product  Le produit à consulter
     * @return Response Autorisation ou refus
     */
    public function view(User $user, Product $product): Response
    {
        if (! $user->hasPermissionTo(UserPermissionEnum::PRODUCT_VIEW->value)) {
            return Response::deny();
        }

        if ($user->isAdmin()) {
            return Response::allow();
        }

        // Les non-admins ne peuvent voir que les produits actifs de leurs magasins
        if (! $product->is_active) {
            return Response::deny(ProductConstant::PRODUCT_NOT_ACCESSIBLE);
        }

        if ($user->stores()->where('stores.id', $product->store_id)->exists()) {
            return Response::allow();
        }

        return Response::deny(ProductConstant::PRODUCT_NOT_ACCESSIBLE);
    }

    /**
     * Détermine si l'utilisateur peut créer un produit.
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
     * Détermine si l'utilisateur peut modifier un produit.
     *
     * @param  User  $user  L'utilisateur authentifié
     * @param  Product  $product  Le produit à modifier
     * @return Response Autorisation ou refus
     */
    public function update(User $user, Product $product): Response
    {
        return $user->hasPermissionTo(UserPermissionEnum::PRODUCT_UPDATE->value)
            ? Response::allow()
            : Response::deny();
    }

    /**
     * Détermine si l'utilisateur peut supprimer un produit.
     *
     * @param  User  $user  L'utilisateur authentifié
     * @param  Product  $product  Le produit à supprimer
     * @return Response Autorisation ou refus
     */
    public function delete(User $user, Product $product): Response
    {
        return $user->hasPermissionTo(UserPermissionEnum::PRODUCT_DELETE->value)
            ? Response::allow()
            : Response::deny();
    }

    /**
     * Détermine si l'utilisateur peut gérer la galerie du produit.
     *
     * @param  User  $user  L'utilisateur authentifié
     * @param  Product  $product  Le produit concerné
     * @return Response Autorisation ou refus
     */
    public function manageGallery(User $user, Product $product): Response
    {
        return $user->hasPermissionTo(UserPermissionEnum::PRODUCT_UPDATE->value)
            ? Response::allow()
            : Response::deny();
    }
}
