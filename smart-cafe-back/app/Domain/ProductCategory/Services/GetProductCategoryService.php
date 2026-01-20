<?php

namespace App\Domain\ProductCategory\Services;

use App\Models\ProductCategory;

/**
 * Service de récupération d'une catégorie de produit.
 */
class GetProductCategoryService
{
    /**
     * Récupère une catégorie avec ses relations.
     *
     * @param  ProductCategory  $category  La catégorie à récupérer
     * @return ProductCategory La catégorie avec ses relations chargées
     */
    public function execute(ProductCategory $category): ProductCategory
    {
        return $category->load(['parent', 'children']);
    }
}
