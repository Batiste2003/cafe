<?php

namespace App\Domain\ProductCategory\Services;

use App\Domain\ProductCategory\Constant\ProductCategoryConstant;
use App\Models\ProductCategory;
use InvalidArgumentException;

/**
 * Service de suppression de catégorie de produit.
 */
class DeleteProductCategoryService
{
    /**
     * Supprime une catégorie (soft delete).
     *
     * @param  ProductCategory  $category  La catégorie à supprimer
     * @param  bool  $force  Forcer la suppression même avec des produits
     *
     * @throws InvalidArgumentException Si la catégorie contient des produits
     */
    public function execute(ProductCategory $category, bool $force = false): void
    {
        if (! $force && $category->products()->exists()) {
            throw new InvalidArgumentException(ProductCategoryConstant::CATEGORY_HAS_PRODUCTS);
        }

        $category->delete();
    }
}
