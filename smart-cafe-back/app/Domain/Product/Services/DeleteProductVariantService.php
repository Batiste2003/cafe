<?php

namespace App\Domain\Product\Services;

use App\Models\ProductVariant;

/**
 * Service de suppression de variant de produit.
 */
class DeleteProductVariantService
{
    /**
     * Supprime un variant (soft delete).
     *
     * @param  ProductVariant  $variant  Le variant à supprimer
     */
    public function execute(ProductVariant $variant): void
    {
        $variant->delete();
    }
}
