<?php

namespace App\Domain\Product\Services;

use App\Models\Product;

/**
 * Service de suppression de produit.
 */
class DeleteProductService
{
    /**
     * Supprime un produit (soft delete).
     *
     * Les variants associés seront également soft deleted via la cascade.
     *
     * @param  Product  $product  Le produit à supprimer
     */
    public function execute(Product $product): void
    {
        // Soft delete variants as well
        $product->variants()->delete();

        $product->delete();
    }
}
