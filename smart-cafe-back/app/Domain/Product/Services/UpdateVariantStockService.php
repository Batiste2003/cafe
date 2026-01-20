<?php

namespace App\Domain\Product\Services;

use App\Domain\Product\DTOs\UpdateVariantStockInputDTO;
use App\Models\ProductVariant;
use App\Models\Store;
use App\Models\StoreProductVariant;

/**
 * Service pour mettre à jour le stock d'un variant dans un store.
 */
class UpdateVariantStockService
{
    /**
     * Met à jour ou crée le stock d'un variant dans un store.
     *
     * @param  ProductVariant  $variant  Le variant concerné
     * @param  Store  $store  Le store concerné
     * @param  UpdateVariantStockInputDTO  $dto  Les données de stock
     * @return StoreProductVariant L'entrée de stock mise à jour
     */
    public function execute(ProductVariant $variant, Store $store, UpdateVariantStockInputDTO $dto): StoreProductVariant
    {
        return StoreProductVariant::updateOrCreate(
            [
                'store_id' => $store->id,
                'product_variant_id' => $variant->id,
            ],
            [
                'stock' => $dto->stock,
            ]
        );
    }
}
