<?php

namespace App\Domain\ProductCategory\Services;

use App\Domain\ProductCategory\DTOs\CreateProductCategoryInputDTO;
use App\Models\ProductCategory;
use Illuminate\Support\Str;

/**
 * Service de création de catégorie de produit.
 */
class CreateProductCategoryService
{
    /**
     * Crée une nouvelle catégorie de produit.
     *
     * @param  CreateProductCategoryInputDTO  $dto  Les données de la catégorie à créer
     * @return ProductCategory La catégorie créée avec ses relations
     */
    public function execute(CreateProductCategoryInputDTO $dto): ProductCategory
    {
        $category = ProductCategory::create([
            'name' => $dto->name,
            'slug' => $this->generateUniqueSlug($dto->name),
            'description' => $dto->description,
            'parent_id' => $dto->parentId,
            'is_active' => $dto->isActive,
        ]);

        return $category->load(['parent', 'children']);
    }

    /**
     * Génère un slug unique basé sur le nom.
     */
    private function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (ProductCategory::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
