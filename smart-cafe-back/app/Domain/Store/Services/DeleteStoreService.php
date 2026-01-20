<?php

namespace App\Domain\Store\Services;

use App\Models\Store;

/**
 * Service de suppression de magasin.
 */
class DeleteStoreService
{
    /**
     * Supprime un magasin (soft delete).
     *
     * @param  Store  $store  Le magasin à supprimer
     * @return bool True si la suppression a réussi
     */
    public function execute(Store $store): bool
    {
        return $store->delete();
    }
}
