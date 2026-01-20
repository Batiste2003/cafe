<?php

namespace App\Domain\Store\Services;

use App\Domain\Store\DTOs\ListStoresFilterDTO;
use App\Models\Store;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Service de liste des magasins.
 */
class ListStoresService
{
    /**
     * Liste les magasins avec filtres et pagination.
     *
     * @param  ListStoresFilterDTO  $filters  Les critères de filtrage
     * @param  User|null  $user  Si fourni, limite les résultats aux magasins accessibles par l'utilisateur
     * @return LengthAwarePaginator<Store>
     */
    public function execute(ListStoresFilterDTO $filters, ?User $user = null): LengthAwarePaginator
    {
        $query = Store::query()->with(['banner', 'logo', 'address', 'creator']);

        if ($filters->withTrashed) {
            $query->withTrashed();
        }

        if ($user !== null) {
            $query->accessibleBy($user);
        }

        if ($filters->search !== null) {
            $query->where('name', 'like', "%{$filters->search}%");
        }

        if ($filters->status !== null) {
            $query->where('status', $filters->status->value);
        }

        return $query->orderBy('created_at', 'desc')->paginate($filters->perPage);
    }
}
