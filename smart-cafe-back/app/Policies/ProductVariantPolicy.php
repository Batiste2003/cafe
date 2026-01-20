<?php

namespace App\Policies;

use App\Domain\User\Enumeration\UserPermissionEnum;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Policy pour la gestion des variants de produits.
 *
 * Les variants héritent des permissions des produits.
 * Seuls les admins peuvent créer/modifier/supprimer des variants.
 */
class ProductVariantPolicy
{
    /**
     * Détermine si l'utilisateur peut voir les variants d'un produit.
     *
     * @param  User  $user  L'utilisateur authentifié
     * @return Response Autorisation ou refus
     */
    public function viewAny(User $user): Response
    {
        return $user->hasPermissionTo(UserPermissionEnum::VARIANT_VIEW->value)
            ? Response::allow()
            : Response::deny();
    }

    /**
     * Détermine si l'utilisateur peut voir un variant spécifique.
     *
     * @param  User  $user  L'utilisateur authentifié
     * @param  ProductVariant  $variant  Le variant à consulter
     * @return Response Autorisation ou refus
     */
    public function view(User $user, ProductVariant $variant): Response
    {
        return $user->hasPermissionTo(UserPermissionEnum::VARIANT_VIEW->value)
            ? Response::allow()
            : Response::deny();
    }

    /**
     * Détermine si l'utilisateur peut créer un variant.
     *
     * @param  User  $user  L'utilisateur authentifié
     * @return Response Autorisation ou refus
     */
    public function create(User $user): Response
    {
        return $user->hasPermissionTo(UserPermissionEnum::VARIANT_CREATE->value)
            ? Response::allow()
            : Response::deny();
    }

    /**
     * Détermine si l'utilisateur peut modifier un variant.
     *
     * @param  User  $user  L'utilisateur authentifié
     * @param  ProductVariant  $variant  Le variant à modifier
     * @return Response Autorisation ou refus
     */
    public function update(User $user, ProductVariant $variant): Response
    {
        return $user->hasPermissionTo(UserPermissionEnum::VARIANT_UPDATE->value)
            ? Response::allow()
            : Response::deny();
    }

    /**
     * Détermine si l'utilisateur peut supprimer un variant.
     *
     * @param  User  $user  L'utilisateur authentifié
     * @param  ProductVariant  $variant  Le variant à supprimer
     * @return Response Autorisation ou refus
     */
    public function delete(User $user, ProductVariant $variant): Response
    {
        return $user->hasPermissionTo(UserPermissionEnum::VARIANT_DELETE->value)
            ? Response::allow()
            : Response::deny();
    }

    /**
     * Détermine si l'utilisateur peut gérer la galerie du variant.
     *
     * @param  User  $user  L'utilisateur authentifié
     * @param  ProductVariant  $variant  Le variant concerné
     * @return Response Autorisation ou refus
     */
    public function manageGallery(User $user, ProductVariant $variant): Response
    {
        return $user->hasPermissionTo(UserPermissionEnum::VARIANT_UPDATE->value)
            ? Response::allow()
            : Response::deny();
    }
}
