<?php

namespace App\Domain\Product\Services;

use App\Domain\Product\DTOs\CreateProductOptionInputDTO;
use App\Models\Product;
use App\Models\ProductOption;

/**
 * Service de création d'option de produit.
 */
class CreateProductOptionService
{
    /**
     * Crée une nouvelle option pour un produit.
     *
     * @param  Product  $product  Le produit parent
     * @param  CreateProductOptionInputDTO  $dto  Les données de l'option à créer
     * @return ProductOption L'option créée avec ses relations
     */
    public function execute(Product $product, CreateProductOptionInputDTO $dto): ProductOption
    {
        $option = ProductOption::create([
            'product_id' => $product->id,
            'name' => $dto->name,
            'is_required' => $dto->isRequired,
        ]);

        // Créer les valeurs de l'option si fournies
        if (!empty($dto->values)) {
            foreach ($dto->values as $valueData) {
                $option->values()->create([
                    'value' => $valueData['value'],
                    'price_add_cent_ht' => $valueData['price_add_cent_ht'] ?? 0,
                ]);
            }
        }

        return $option->load(['product', 'values']);
    }
}
