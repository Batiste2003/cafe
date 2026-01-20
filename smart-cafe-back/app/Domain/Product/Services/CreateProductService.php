<?php

namespace App\Domain\Product\Services;

use App\Domain\Product\DTOs\CreateProductInputDTO;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * Service de création de produit.
 */
class CreateProductService
{
    /**
     * Crée un nouveau produit.
     *
     * @param  CreateProductInputDTO  $dto  Les données du produit à créer
     * @param  User  $creator  L'utilisateur qui crée le produit
     * @return Product Le produit créé avec ses relations
     */
    public function execute(CreateProductInputDTO $dto, User $creator): Product
    {
        $product = Product::create([
            'name' => $dto->name,
            'slug' => $this->generateUniqueSlug($dto->name),
            'description' => $dto->description,
            'product_category_id' => $dto->productCategoryId,
            'created_by' => $creator->id,
            'is_active' => $dto->isActive,
            'is_featured' => $dto->isFeatured,
        ]);

        // Associer le produit aux stores
        if (! empty($dto->storeIds)) {
            $product->stores()->attach($dto->storeIds);
        }

        return $product->load(['category', 'stores', 'creator', 'variants', 'gallery']);
    }

    /**
     * Génère un slug unique basé sur le nom.
     */
    private function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (Product::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $originalSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
