<?php

namespace App\Domain\Product\Services;

use App\Domain\Product\DTOs\CreateProductOptionValueInputDTO;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;

/**
 * Service de création de valeur d'option.
 */
class CreateProductOptionValueService
{
    /**
     * Crée une nouvelle valeur pour une option.
     *
     * @param  ProductOption  $option  L'option parente
     * @param  CreateProductOptionValueInputDTO  $dto  Les données de la valeur à créer
     * @return ProductOptionValue La valeur créée avec ses relations
     */
    public function execute(ProductOption $option, CreateProductOptionValueInputDTO $dto): ProductOptionValue
    {
        $value = ProductOptionValue::create([
            'product_option_id' => $option->id,
            'value' => $dto->value,
            'price_add_cent_ht' => $dto->priceAddCentHt,
        ]);

        return $value->load(['option']);
    }
}
