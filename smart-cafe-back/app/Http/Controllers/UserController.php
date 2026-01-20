<?php

namespace App\Http\Controllers;

use App\Domain\ApiResponse\DTOs\PagingDTO;
use App\Domain\ApiResponse\Services\ApiResponseErrorService;
use App\Domain\ApiResponse\Services\ApiResponseSuccessService;
use App\Domain\ApiResponse\Services\ApiResponseSuccessWithPaginationService;
use App\Domain\User\Constant\UserConstant;
use App\Domain\User\DTOs\CreateUserInputDTO;
use App\Domain\User\DTOs\ListUsersFilterDTO;
use App\Domain\User\DTOs\UpdateUserInputDTO;
use App\Domain\User\Services\CreateUserService;
use App\Domain\User\Services\DeleteUserService;
use App\Domain\User\Services\GetUserService;
use App\Domain\User\Services\ListUsersService;
use App\Domain\User\Services\RestoreUserService;
use App\Domain\User\Services\UpdateUserService;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Contrôleur pour la gestion des utilisateurs.
 *
 * Gère les opérations CRUD sur les utilisateurs ainsi que
 * la restauration des utilisateurs supprimés (soft delete).
 */
class UserController extends Controller
{
    public function __construct(
        private readonly ApiResponseSuccessService $successService,
        private readonly ApiResponseSuccessWithPaginationService $successWithPaginationService,
        private readonly ApiResponseErrorService $errorService,
        private readonly ListUsersService $listUsersService,
        private readonly GetUserService $getUserService,
        private readonly CreateUserService $createUserService,
        private readonly UpdateUserService $updateUserService,
        private readonly DeleteUserService $deleteUserService,
        private readonly RestoreUserService $restoreUserService,
    ) {}

    /**
     * Récupère la liste paginée des utilisateurs.
     *
     * Filtres disponibles : search, role, with_trashed, per_page.
     *
     * @param  Request  $request  La requête contenant les filtres optionnels
     * @return JsonResponse Liste paginée des utilisateurs
     */
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', User::class);

        $filters = ListUsersFilterDTO::fromArray($request->all());
        $users = $this->listUsersService->execute($filters);

        $paging = PagingDTO::fromPaginator($users);

        return $this->successWithPaginationService->execute(
            message: UserConstant::USERS_RETRIEVED,
            data: UserResource::collection($users->items()),
            paging: $paging,
        );
    }

    /**
     * Crée un nouvel utilisateur.
     *
     * Seuls les utilisateurs avec le rôle Employer peuvent être créés par un Admin.
     *
     * @param  StoreUserRequest  $request  Les données validées de l'utilisateur
     * @return JsonResponse L'utilisateur créé avec le code 201
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        Gate::authorize('create', User::class);

        $dto = CreateUserInputDTO::fromArray($request->validated());
        $user = $this->createUserService->execute($dto);

        return $this->successService->execute(
            message: UserConstant::USER_CREATED,
            data: new UserResource($user),
            statusCode: 201,
        );
    }

    /**
     * Récupère les détails d'un utilisateur.
     *
     * @param  User  $user  L'utilisateur à afficher
     * @return JsonResponse Les détails de l'utilisateur avec ses rôles
     */
    public function show(User $user): JsonResponse
    {
        Gate::authorize('view', $user);

        $user->load('roles');

        return $this->successService->execute(
            message: UserConstant::USER_RETRIEVED,
            data: new UserResource($user),
        );
    }

    /**
     * Met à jour un utilisateur existant.
     *
     * @param  UpdateUserRequest  $request  Les données validées de mise à jour
     * @param  User  $user  L'utilisateur à modifier
     * @return JsonResponse L'utilisateur mis à jour
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        Gate::authorize('update', $user);

        $dto = UpdateUserInputDTO::fromArray($request->validated());
        $updatedUser = $this->updateUserService->execute($user, $dto);

        return $this->successService->execute(
            message: UserConstant::USER_UPDATED,
            data: new UserResource($updatedUser),
        );
    }

    /**
     * Supprime un utilisateur (soft delete).
     *
     * Un utilisateur ne peut pas se supprimer lui-même ni supprimer un Admin.
     *
     * @param  User  $user  L'utilisateur à supprimer
     * @return JsonResponse Confirmation de suppression
     */
    public function destroy(User $user): JsonResponse
    {
        Gate::authorize('delete', $user);

        $this->deleteUserService->execute($user);

        return $this->successService->execute(
            message: UserConstant::USER_DELETED,
            data: null,
        );
    }

    /**
     * Restaure un utilisateur supprimé.
     *
     * @param  User  $user  L'utilisateur à restaurer
     * @return JsonResponse L'utilisateur restauré
     */
    public function restore(User $user): JsonResponse
    {
        Gate::authorize('restore', $user);

        $restoredUser = $this->restoreUserService->execute($user);

        return $this->successService->execute(
            message: UserConstant::USER_RESTORED,
            data: new UserResource($restoredUser),
        );
    }
}
