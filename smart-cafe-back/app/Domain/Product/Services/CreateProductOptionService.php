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

        return $option->load(['product', 'values']);
    }
}
