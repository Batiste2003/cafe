<?php

namespace App\Domain\Store\Services;

use App\Models\Store;

/**
 * Service de récupération d'un magasin.
 */
class GetStoreService
{
    /**
     * Récupère un magasin avec toutes ses relations.
     *
     * @param  Store  $store  Le magasin à récupérer
     * @return Store Le magasin avec ses relations chargées
     */
    public function execute(Store $store): Store
    {
        return $store->load(['banner', 'logo', 'address', 'creator', 'users.roles']);
    }
}
