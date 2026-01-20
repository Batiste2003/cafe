<?php

namespace App\Domain\Product\Services;

use App\Domain\Product\Constant\ProductConstant;
use App\Models\ProductVariant;
use InvalidArgumentException;

/**
 * Service pour ajouter une image à la galerie d'un variant.
 */
class AttachGalleryToVariantService
{
    /**
     * Ajoute une image à la galerie du variant.
     *
     * @param  ProductVariant  $variant  Le variant
     * @param  int  $storedFileId  L'ID du fichier stocké
     * @param  int|null  $position  La position dans la galerie (auto-calculée si null)
     * @return ProductVariant Le variant avec ses relations mises à jour
     *
     * @throws InvalidArgumentException Si le nombre max d'images est atteint
     */
    public function execute(ProductVariant $variant, int $storedFileId, ?int $position = null): ProductVariant
    {
        $currentCount = $variant->gallery()->count();

        if ($currentCount >= ProductConstant::MAX_GALLERY_IMAGES) {
            throw new InvalidArgumentException(ProductConstant::GALLERY_MAX_IMAGES_REACHED);
        }

        if ($position === null) {
            $position = $currentCount;
        }

        // Avoid duplicate
        if (! $variant->gallery()->where('stored_file_id', $storedFileId)->exists()) {
            $variant->gallery()->attach($storedFileId, ['position' => $position]);
        }

        return $variant->fresh()->load(['product', 'gallery', 'optionValues']);
    }
}
