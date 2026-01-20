<?php

namespace App\Domain\Product\Services;

use App\Models\Product;

/**
 * Service de récupération d'un produit.
 */
class GetProductService
{
    /**
     * Récupère un produit avec ses relations.
     *
     * @param  Product  $product  Le produit à récupérer
     * @return Product Le produit avec ses relations chargées
     */
    public function execute(Product $product): Product
    {
        return $product->load(['category', 'stores', 'creator', 'variants.gallery', 'variants.storeStocks', 'options.values', 'gallery']);
    }
}
