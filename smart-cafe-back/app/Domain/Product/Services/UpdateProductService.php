<?php

namespace App\Domain\Product\Services;

use App\Domain\Product\DTOs\UpdateProductInputDTO;
use App\Models\Product;
use Illuminate\Support\Str;

/**
 * Service de mise à jour de produit.
 */
class UpdateProductService
{
    /**
     * Met à jour un produit existant.
     *
     * @param  Product  $product  Le produit à modifier
     * @param  UpdateProductInputDTO  $dto  Les données de mise à jour
     * @return Product Le produit mis à jour avec ses relations
     */
    public function execute(Product $product, UpdateProductInputDTO $dto): Product
    {
        $data = [];

        if ($dto->name !== null) {
            $data['name'] = $dto->name;
            $data['slug'] = $this->generateUniqueSlug($dto->name, $product->id);
        }

        if ($dto->description !== null) {
            $data['description'] = $dto->description;
        }

        if ($dto->productCategoryId !== null) {
            $data['product_category_id'] = $dto->productCategoryId;
        }

        if ($dto->isActive !== null) {
            $data['is_active'] = $dto->isActive;
        }

        if ($dto->isFeatured !== null) {
            $data['is_featured'] = $dto->isFeatured;
        }

        if (! empty($data)) {
            $product->update($data);
        }

        return $product->fresh()->load(['category', 'stores', 'creator', 'variants.storeStocks', 'gallery']);
    }

    /**
     * Génère un slug unique basé sur le nom.
     */
    private function generateUniqueSlug(string $name, int $excludeId): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (Product::withTrashed()->where('slug', $slug)->where('id', '!=', $excludeId)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
