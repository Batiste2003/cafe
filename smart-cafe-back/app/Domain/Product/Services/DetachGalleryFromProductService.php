<?php

namespace App\Domain\Product\Services;

use App\Models\Product;

/**
 * Service pour retirer une image de la galerie d'un produit.
 */
class DetachGalleryFromProductService
{
    /**
     * Retire une image de la galerie du produit.
     *
     * @param  Product  $product  Le produit
     * @param  int  $storedFileId  L'ID du fichier stocké à retirer
     * @return Product Le produit avec ses relations mises à jour
     */
    public function execute(Product $product, int $storedFileId): Product
    {
        $product->gallery()->detach($storedFileId);

        return $product->fresh()->load(['category', 'stores', 'creator', 'variants.storeStocks', 'gallery']);
    }
}
