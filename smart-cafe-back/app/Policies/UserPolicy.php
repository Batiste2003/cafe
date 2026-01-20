<?php

namespace App\Policies;

use App\Domain\User\Constant\UserConstant;
use App\Domain\User\Enumeration\UserPermissionEnum;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): Response
    {
        return $user->hasPermissionTo(UserPermissionEnum::USER_VIEW->value)
            ? Response::allow()
            : Response::deny();
    }

    public function view(User $user, User $model): Response
    {
        return $user->hasPermissionTo(UserPermissionEnum::USER_VIEW->value)
            ? Response::allow()
            : Response::deny();
    }

    public function create(User $user): Response
    {
        return $user->hasPermissionTo(UserPermissionEnum::USER_CREATE->value)
            ? Response::allow()
            : Response::deny();
    }

    public function update(User $user, User $model): Response
    {
        return $user->hasPermissionTo(UserPermissionEnum::USER_UPDATE->value)
            ? Response::allow()
            : Response::deny();
    }

    public function delete(User $user, User $model): Response
    {
        if (! $user->hasPermissionTo(UserPermissionEnum::USER_DELETE->value)) {
            return Response::deny();
        }

        if ($user->id === $model->id) {
            return Response::deny(UserConstant::CANNOT_DELETE_SELF);
        }

        if ($model->isAdmin()) {
            return Response::deny(UserConstant::CANNOT_DELETE_ADMIN);
        }

        return Response::allow();
    }

    public function restore(User $user, User $model): Response
    {
        return $user->hasPermissionTo(UserPermissionEnum::USER_UPDATE->value)
            ? Response::allow()
            : Response::deny();
    }

    public function forceDelete(User $user, User $model): Response
    {
        return Response::deny();
    }
}
