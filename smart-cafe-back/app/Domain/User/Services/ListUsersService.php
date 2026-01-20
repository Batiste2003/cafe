<?php

namespace App\Domain\User\Services;

use App\Domain\User\DTOs\ListUsersFilterDTO;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListUsersService
{
    /**
     * @return LengthAwarePaginator<User>
     */
    public function execute(ListUsersFilterDTO $filters): LengthAwarePaginator
    {
        $query = User::query()->with('roles');

        if ($filters->withTrashed) {
            $query->withTrashed();
        }

        if ($filters->search !== null) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters->search}%")
                    ->orWhere('email', 'like', "%{$filters->search}%");
            });
        }

        if ($filters->role !== null) {
            $query->role($filters->role->value);
        }

        return $query->orderBy('created_at', 'desc')->paginate($filters->perPage);
    }
}
