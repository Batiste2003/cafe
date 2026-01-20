<?php

namespace App\Domain\ProductCategory\Services;

use App\Domain\ProductCategory\DTOs\ListProductCategoriesFilterDTO;
use App\Models\ProductCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Service de liste des catégories de produit.
 */
class ListProductCategoriesService
{
    /**
     * Liste les catégories avec filtres et pagination.
     *
     * @param  ListProductCategoriesFilterDTO  $filters  Les critères de filtrage
     * @return LengthAwarePaginator<ProductCategory>
     */
    public function execute(ListProductCategoriesFilterDTO $filters): LengthAwarePaginator
    {
        $query = ProductCategory::query()->with(['parent', 'children']);

        if ($filters->withTrashed) {
            $query->withTrashed();
        }

        if ($filters->search !== null) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters->search}%")
                    ->orWhere('description', 'like', "%{$filters->search}%");
            });
        }

        if ($filters->isActive !== null) {
            $query->where('is_active', $filters->isActive);
        }

        if ($filters->onlyRoot) {
            $query->root();
        } elseif ($filters->parentId !== null) {
            $query->where('parent_id', $filters->parentId);
        }

        return $query->orderBy('name', 'asc')->paginate($filters->perPage);
    }
}
