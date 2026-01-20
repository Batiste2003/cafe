<?php

namespace App\Domain\Product\Services;

use App\Domain\Product\DTOs\UpdateProductVariantInputDTO;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;

/**
 * Service de mise à jour de variant de produit.
 */
class UpdateProductVariantService
{
    /**
     * Met à jour un variant existant.
     *
     * @param  ProductVariant  $variant  Le variant à modifier
     * @param  UpdateProductVariantInputDTO  $dto  Les données de mise à jour
     * @return ProductVariant Le variant mis à jour avec ses relations
     */
    public function execute(ProductVariant $variant, UpdateProductVariantInputDTO $dto): ProductVariant
    {
        return DB::transaction(function () use ($variant, $dto) {
            $data = [];

            if ($dto->sku !== null) {
                $data['sku'] = $dto->sku;
            }

            if ($dto->priceCentHt !== null) {
                $data['price_cent_ht'] = $dto->priceCentHt;
            }

            if ($dto->costPriceCentHt !== null || $dto->clearCostPrice) {
                $data['cost_price_cent_ht'] = $dto->costPriceCentHt;
            }

            if ($dto->stock !== null) {
                $data['stock'] = $dto->stock;
            }

            if ($dto->isDefault !== null) {
                // Si ce variant devient défaut, désactiver les autres
                if ($dto->isDefault) {
                    $variant->product->variants()
                        ->where('id', '!=', $variant->id)
                        ->update(['is_default' => false]);
                }
                $data['is_default'] = $dto->isDefault;
            }

            if (! empty($data)) {
                $variant->update($data);
            }

            return $variant->fresh()->load(['product', 'gallery', 'optionValues']);
        });
    }
}
