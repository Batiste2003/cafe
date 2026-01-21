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
     * Profondeur maximale pour éviter les boucles infinies.
     */
    private const MAX_DEPTH = 10;

    /**
     * Liste les catégories avec filtres et pagination.
     *
     * @param  ListProductCategoriesFilterDTO  $filters  Les critères de filtrage
     * @return LengthAwarePaginator<ProductCategory>
     */
    public function execute(ListProductCategoriesFilterDTO $filters): LengthAwarePaginator
    {
        $query = ProductCategory::query()->with(['parent']);

        // Load children recursively with depth limit
        $query->with($this->getChildrenRelations());

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

    /**
     * Génère les relations imbriquées pour charger les enfants récursivement.
     * Limite à MAX_DEPTH niveaux pour éviter les boucles infinies.
     *
     * @return array<string>
     */
    private function getChildrenRelations(): array
    {
        $relations = [];
        $relation = 'children';

        for ($i = 0; $i < self::MAX_DEPTH; $i++) {
            $relations[] = $relation;
            $relation .= '.children';
        }

        return $relations;
    }
}
