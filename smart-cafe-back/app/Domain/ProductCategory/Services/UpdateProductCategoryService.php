<?php

namespace App\Domain\ProductCategory\Services;

use App\Domain\ProductCategory\Constant\ProductCategoryConstant;
use App\Domain\ProductCategory\DTOs\UpdateProductCategoryInputDTO;
use App\Models\ProductCategory;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * Service de mise à jour de catégorie de produit.
 */
class UpdateProductCategoryService
{
    /**
     * Met à jour une catégorie existante.
     *
     * @param  ProductCategory  $category  La catégorie à modifier
     * @param  UpdateProductCategoryInputDTO  $dto  Les données de mise à jour
     * @return ProductCategory La catégorie mise à jour avec ses relations
     *
     * @throws InvalidArgumentException Si le parent est invalide
     */
    public function execute(ProductCategory $category, UpdateProductCategoryInputDTO $dto): ProductCategory
    {
        $data = [];

        if ($dto->name !== null) {
            $data['name'] = $dto->name;
            $data['slug'] = $this->generateUniqueSlug($dto->name, $category->id);
        }

        if ($dto->description !== null) {
            $data['description'] = $dto->description;
        }

        if ($dto->parentId !== null || $dto->clearParent) {
            $this->validateParent($category, $dto->parentId);
            $data['parent_id'] = $dto->parentId;
        }

        if ($dto->isActive !== null) {
            $data['is_active'] = $dto->isActive;
        }

        if (! empty($data)) {
            $category->update($data);
        }

        return $category->fresh()->load(['parent', 'children']);
    }

    /**
     * Valide que le parent est valide (pas soi-même, pas un descendant).
     */
    private function validateParent(ProductCategory $category, ?int $parentId): void
    {
        if ($parentId === null) {
            return;
        }

        if ($parentId === $category->id) {
            throw new InvalidArgumentException(ProductCategoryConstant::CANNOT_SET_SELF_AS_PARENT);
        }

        $descendantIds = $category->descendants()->pluck('id')->toArray();
        if (in_array($parentId, $descendantIds)) {
            throw new InvalidArgumentException(ProductCategoryConstant::CANNOT_SET_DESCENDANT_AS_PARENT);
        }
    }

    /**
     * Génère un slug unique basé sur le nom.
     */
    private function generateUniqueSlug(string $name, int $excludeId): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (ProductCategory::withTrashed()->where('slug', $slug)->where('id', '!=', $excludeId)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
