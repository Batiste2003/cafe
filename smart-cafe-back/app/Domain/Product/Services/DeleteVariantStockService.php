<?php

namespace App\Domain\Product\Services;

use App\Models\ProductVariant;
use App\Models\Store;
use App\Models\StoreProductVariant;

/**
 * Service pour supprimer le stock d'un variant dans un store.
 */
class DeleteVariantStockService
{
    /**
     * Supprime l'entrée de stock d'un variant dans un store.
     *
     * @param  ProductVariant  $variant  Le variant concerné
     * @param  Store  $store  Le store concerné
     * @return bool True si supprimé, false si non trouvé
     */
    public function execute(ProductVariant $variant, Store $store): bool
    {
        return StoreProductVariant::where('store_id', $store->id)
            ->where('product_variant_id', $variant->id)
            ->delete() > 0;
    }
}
