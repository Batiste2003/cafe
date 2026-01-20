<?php

namespace App\Domain\Product\Services;

use App\Models\ProductOptionValue;

/**
 * Service de suppression de valeur d'option.
 */
class DeleteProductOptionValueService
{
    /**
     * Supprime une valeur d'option.
     *
     * Détache également la valeur des variants qui l'utilisent.
     *
     * @param  ProductOptionValue  $value  La valeur à supprimer
     * @return bool Succès de la suppression
     */
    public function execute(ProductOptionValue $value): bool
    {
        // Détacher des variants
        $value->variants()->detach();

        return $value->delete();
    }
}
