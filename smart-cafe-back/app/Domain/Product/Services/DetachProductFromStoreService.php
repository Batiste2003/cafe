<?php

namespace App\Domain\Product\Services;

use App\Models\Product;
use App\Models\Store;
use App\Models\StoreProductVariant;

/**
 * Service pour retirer un produit d'un store.
 */
class DetachProductFromStoreService
{
    /**
     * Retire un produit d'un store.
     *
     * Supprime également les stocks des variants de ce produit dans ce store.
     *
     * @param  Product  $product  Le produit à retirer
     * @param  Store  $store  Le store duquel retirer le produit
     * @return Product Le produit avec ses stores mis à jour
     */
    public function execute(Product $product, Store $store): Product
    {
        // Supprimer les stocks des variants de ce produit dans ce store
        $variantIds = $product->variants()->pluck('id');
        StoreProductVariant::where('store_id', $store->id)
            ->whereIn('product_variant_id', $variantIds)
            ->delete();

        // Détacher le produit du store
        $product->stores()->detach($store->id);

        return $product->load('stores');
    }
}
