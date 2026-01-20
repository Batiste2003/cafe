<?php

namespace App\Domain\Product\Services;

use App\Domain\Product\DTOs\UpdateProductOptionValueInputDTO;
use App\Models\ProductOptionValue;

/**
 * Service de mise à jour de valeur d'option.
 */
class UpdateProductOptionValueService
{
    /**
     * Met à jour une valeur d'option existante.
     *
     * @param  ProductOptionValue  $value  La valeur à modifier
     * @param  UpdateProductOptionValueInputDTO  $dto  Les nouvelles données
     * @return ProductOptionValue La valeur mise à jour avec ses relations
     */
    public function execute(ProductOptionValue $value, UpdateProductOptionValueInputDTO $dto): ProductOptionValue
    {
        $data = [];

        if ($dto->value !== null) {
            $data['value'] = $dto->value;
        }

        if ($dto->priceAddCentHt !== null) {
            $data['price_add_cent_ht'] = $dto->priceAddCentHt;
        }

        if (! empty($data)) {
            $value->update($data);
        }

        return $value->load(['option']);
    }
}
