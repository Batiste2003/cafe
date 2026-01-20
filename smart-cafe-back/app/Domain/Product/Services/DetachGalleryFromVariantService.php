<?php

namespace App\Domain\Product\Services;

use App\Models\ProductVariant;

/**
 * Service pour retirer une image de la galerie d'un variant.
 */
class DetachGalleryFromVariantService
{
    /**
     * Retire une image de la galerie du variant.
     *
     * @param  ProductVariant  $variant  Le variant
     * @param  int  $storedFileId  L'ID du fichier stocké à retirer
     * @return ProductVariant Le variant avec ses relations mises à jour
     */
    public function execute(ProductVariant $variant, int $storedFileId): ProductVariant
    {
        $variant->gallery()->detach($storedFileId);

        return $variant->fresh()->load(['product', 'gallery', 'optionValues']);
    }
}
