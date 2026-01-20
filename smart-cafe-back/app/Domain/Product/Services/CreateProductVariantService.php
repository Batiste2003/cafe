<?php

namespace App\Domain\Product\Services;

use App\Domain\Product\DTOs\CreateProductVariantInputDTO;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;

/**
 * Service de création de variant de produit.
 */
class CreateProductVariantService
{
    /**
     * Crée un nouveau variant pour un produit.
     *
     * Le stock n'est plus géré ici mais via UpdateVariantStockService
     * pour chaque store individuellement.
     *
     * @param  Product  $product  Le produit parent
     * @param  CreateProductVariantInputDTO  $dto  Les données du variant à créer
     * @return ProductVariant Le variant créé avec ses relations
     */
    public function execute(Product $product, CreateProductVariantInputDTO $dto): ProductVariant
    {
        return DB::transaction(function () use ($product, $dto) {
            // Si ce variant est marqué comme défaut, désactiver les autres
            if ($dto->isDefault) {
                $product->variants()->update(['is_default' => false]);
            }

            $variant = ProductVariant::create([
                'product_id' => $product->id,
                'sku' => $dto->sku,
                'price_cent_ht' => $dto->priceCentHt,
                'cost_price_cent_ht' => $dto->costPriceCentHt,
                'is_default' => $dto->isDefault,
            ]);

            return $variant->load(['product', 'gallery', 'optionValues', 'storeStocks']);
        });
    }
}
