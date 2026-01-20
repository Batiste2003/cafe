<?php

namespace App\Domain\Product\Services;

use App\Domain\Product\DTOs\ListProductsFilterDTO;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Service de liste des produits.
 */
class ListProductsService
{
    /**
     * Liste les produits avec filtres et pagination.
     *
     * @param  ListProductsFilterDTO  $filters  Les critères de filtrage
     * @param  User|null  $user  Si fourni, limite les résultats aux produits accessibles par l'utilisateur
     * @return LengthAwarePaginator<Product>
     */
    public function execute(ListProductsFilterDTO $filters, ?User $user = null): LengthAwarePaginator
    {
        $query = Product::query()->with(['category', 'stores', 'creator', 'variants.storeStocks', 'gallery']);

        if ($filters->withTrashed) {
            $query->withTrashed();
        }

        if ($user !== null) {
            $query->accessibleBy($user);
        }

        if ($filters->search !== null) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters->search}%")
                    ->orWhere('description', 'like', "%{$filters->search}%");
            });
        }

        if ($filters->storeId !== null) {
            $query->forStore($filters->storeId);
        }

        if ($filters->categoryId !== null) {
            $query->forCategory($filters->categoryId);
        }

        if ($filters->isActive !== null) {
            $query->where('is_active', $filters->isActive);
        }

        if ($filters->isFeatured !== null) {
            $query->where('is_featured', $filters->isFeatured);
        }

        return $query->orderBy('created_at', 'desc')->paginate($filters->perPage);
    }
}
