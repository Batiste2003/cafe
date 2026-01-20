<?php

namespace App\Domain\Product\Services;

use App\Domain\Product\DTOs\UpdateProductOptionInputDTO;
use App\Models\ProductOption;

/**
 * Service de mise à jour d'option de produit.
 */
class UpdateProductOptionService
{
    /**
     * Met à jour une option existante.
     *
     * @param  ProductOption  $option  L'option à modifier
     * @param  UpdateProductOptionInputDTO  $dto  Les données de mise à jour
     * @return ProductOption L'option mise à jour avec ses relations
     */
    public function execute(ProductOption $option, UpdateProductOptionInputDTO $dto): ProductOption
    {
        $data = [];

        if ($dto->name !== null) {
            $data['name'] = $dto->name;
        }

        if ($dto->isRequired !== null) {
            $data['is_required'] = $dto->isRequired;
        }

        if (! empty($data)) {
            $option->update($data);
        }

        return $option->fresh()->load(['product', 'values']);
    }
}
