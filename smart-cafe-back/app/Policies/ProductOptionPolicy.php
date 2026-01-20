<?php

namespace App\Policies;

use App\Domain\User\Enumeration\UserPermissionEnum;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Policy pour la gestion des options de produit.
 *
 * Les options sont gérées uniquement par les admins.
 * Manager/Employer peuvent voir les options des produits de leur store.
 */
class ProductOptionPolicy
{
    /**
     * Détermine si l'utilisateur peut voir la liste des options d'un produit.
     *
     * @param  User  $user  L'utilisateur authentifié
     * @param  Product  $product  Le produit parent
     * @return Response Autorisation ou refus
     */
    public function viewAny(User $user, Product $product): Response
    {
        // Admin peut tout voir
        if ($user->hasPermissionTo(UserPermissionEnum::PRODUCT_VIEW->value)) {
            // Vérifier l'accès au produit parent pour les non-admins
            if (! $user->hasPermissionTo(UserPermissionEnum::PRODUCT_CREATE->value)) {
                // Manager/Employer : vérifier l'accès au store
                $storeIds = $user->stores()->pluck('stores.id')->toArray();
                if (! in_array($product->store_id, $storeIds)) {
                    return Response::deny('Vous n\'avez pas accès à ce produit.');
                }
            }

            return Response::allow();
        }

        return Response::deny();
    }

    /**
     * Détermine si l'utilisateur peut voir une option.
     *
     * @param  User  $user  L'utilisateur authentifié
     * @param  ProductOption  $option  L'option à consulter
     * @return Response Autorisation ou refus
     */
    public function view(User $user, ProductOption $option): Response
    {
        if ($user->hasPermissionTo(UserPermissionEnum::PRODUCT_VIEW->value)) {
            // Vérifier l'accès au produit parent pour les non-admins
            if (! $user->hasPermissionTo(UserPermissionEnum::PRODUCT_CREATE->value)) {
                $storeIds = $user->stores()->pluck('stores.id')->toArray();
                if (! in_array($option->product->store_id, $storeIds)) {
                    return Response::deny('Vous n\'avez pas accès à cette option.');
                }
            }

            return Response::allow();
        }

        return Response::deny();
    }

    /**
     * Détermine si l'utilisateur peut créer une option.
     *
     * @param  User  $user  L'utilisateur authentifié
     * @param  Product  $product  Le produit parent
     * @return Response Autorisation ou refus
     */
    public function create(User $user, Product $product): Response
    {
        return $user->hasPermissionTo(UserPermissionEnum::PRODUCT_CREATE->value)
            ? Response::allow()
            : Response::deny();
    }

    /**
     * Détermine si l'utilisateur peut modifier une option.
     *
     * @param  User  $user  L'utilisateur authentifié
     * @param  ProductOption  $option  L'option à modifier
     * @return Response Autorisation ou refus
     */
    public function update(User $user, ProductOption $option): Response
    {
        return $user->hasPermissionTo(UserPermissionEnum::PRODUCT_UPDATE->value)
            ? Response::allow()
            : Response::deny();
    }

    /**
     * Détermine si l'utilisateur peut supprimer une option.
     *
     * @param  User  $user  L'utilisateur authentifié
     * @param  ProductOption  $option  L'option à supprimer
     * @return Response Autorisation ou refus
     */
    public function delete(User $user, ProductOption $option): Response
    {
        return $user->hasPermissionTo(UserPermissionEnum::PRODUCT_DELETE->value)
            ? Response::allow()
            : Response::deny();
    }
}
