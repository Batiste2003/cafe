<?php

namespace App\Policies;

use App\Domain\User\Enumeration\UserPermissionEnum;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Policy pour la gestion des valeurs d'options.
 *
 * Les valeurs d'options sont gérées uniquement par les admins.
 * Manager/Employer peuvent voir les valeurs des produits de leur store.
 */
class ProductOptionValuePolicy
{
    /**
     * Détermine si l'utilisateur peut voir la liste des valeurs d'une option.
     *
     * @param  User  $user  L'utilisateur authentifié
     * @param  ProductOption  $option  L'option parente
     * @return Response Autorisation ou refus
     */
    public function viewAny(User $user, ProductOption $option): Response
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
     * Détermine si l'utilisateur peut voir une valeur.
     *
     * @param  User  $user  L'utilisateur authentifié
     * @param  ProductOptionValue  $value  La valeur à consulter
     * @return Response Autorisation ou refus
     */
    public function view(User $user, ProductOptionValue $value): Response
    {
        if ($user->hasPermissionTo(UserPermissionEnum::PRODUCT_VIEW->value)) {
            // Vérifier l'accès au produit parent pour les non-admins
            if (! $user->hasPermissionTo(UserPermissionEnum::PRODUCT_CREATE->value)) {
                $storeIds = $user->stores()->pluck('stores.id')->toArray();
                if (! in_array($value->option->product->store_id, $storeIds)) {
                    return Response::deny('Vous n\'avez pas accès à cette valeur.');
                }
            }

            return Response::allow();
        }

        return Response::deny();
    }

    /**
     * Détermine si l'utilisateur peut créer une valeur.
     *
     * @param  User  $user  L'utilisateur authentifié
     * @param  ProductOption  $option  L'option parente
     * @return Response Autorisation ou refus
     */
    public function create(User $user, ProductOption $option): Response
    {
        return $user->hasPermissionTo(UserPermissionEnum::PRODUCT_CREATE->value)
            ? Response::allow()
            : Response::deny();
    }

    /**
     * Détermine si l'utilisateur peut modifier une valeur.
     *
     * @param  User  $user  L'utilisateur authentifié
     * @param  ProductOptionValue  $value  La valeur à modifier
     * @return Response Autorisation ou refus
     */
    public function update(User $user, ProductOptionValue $value): Response
    {
        return $user->hasPermissionTo(UserPermissionEnum::PRODUCT_UPDATE->value)
            ? Response::allow()
            : Response::deny();
    }

    /**
     * Détermine si l'utilisateur peut supprimer une valeur.
     *
     * @param  User  $user  L'utilisateur authentifié
     * @param  ProductOptionValue  $value  La valeur à supprimer
     * @return Response Autorisation ou refus
     */
    public function delete(User $user, ProductOptionValue $value): Response
    {
        return $user->hasPermissionTo(UserPermissionEnum::PRODUCT_DELETE->value)
            ? Response::allow()
            : Response::deny();
    }
}
