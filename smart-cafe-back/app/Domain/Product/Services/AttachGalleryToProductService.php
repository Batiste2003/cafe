<?php

namespace App\Domain\Product\Services;

use App\Domain\Product\Constant\ProductConstant;
use App\Models\Product;
use InvalidArgumentException;

/**
 * Service pour ajouter une image à la galerie d'un produit.
 */
class AttachGalleryToProductService
{
    /**
     * Ajoute une image à la galerie du produit.
     *
     * @param  Product  $product  Le produit
     * @param  int  $storedFileId  L'ID du fichier stocké
     * @param  int|null  $position  La position dans la galerie (auto-calculée si null)
     * @return Product Le produit avec ses relations mises à jour
     *
     * @throws InvalidArgumentException Si le nombre max d'images est atteint
     */
    public function execute(Product $product, int $storedFileId, ?int $position = null): Product
    {
        $currentCount = $product->gallery()->count();

        if ($currentCount >= ProductConstant::MAX_GALLERY_IMAGES) {
            throw new InvalidArgumentException(ProductConstant::GALLERY_MAX_IMAGES_REACHED);
        }

        if ($position === null) {
            $position = $currentCount;
        }

        // Avoid duplicate
        if (! $product->gallery()->where('stored_file_id', $storedFileId)->exists()) {
            $product->gallery()->attach($storedFileId, ['position' => $position]);
        }

        return $product->fresh()->load(['category', 'stores', 'creator', 'variants.storeStocks', 'gallery']);
    }
}
