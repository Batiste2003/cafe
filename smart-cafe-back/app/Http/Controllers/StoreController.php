<?php

namespace App\Http\Controllers;

use App\Domain\ApiResponse\DTOs\PagingDTO;
use App\Domain\ApiResponse\Services\ApiResponseSuccessService;
use App\Domain\ApiResponse\Services\ApiResponseSuccessWithPaginationService;
use App\Domain\Store\Constant\StoreConstant;
use App\Domain\Store\DTOs\AttachUsersToStoreDTO;
use App\Domain\Store\DTOs\CreateStoreInputDTO;
use App\Domain\Store\DTOs\ListStoresFilterDTO;
use App\Domain\Store\DTOs\UpdateStoreInputDTO;
use App\Domain\Store\Services\AttachUsersToStoreService;
use App\Domain\Store\Services\CreateStoreService;
use App\Domain\Store\Services\DeleteStoreService;
use App\Domain\Store\Services\DetachUserFromStoreService;
use App\Domain\Store\Services\GetStoreService;
use App\Domain\Store\Services\ListStoresService;
use App\Domain\Store\Services\UpdateStoreService;
use App\Domain\UploadedFile\Constant\UploadedFileConstant;
use App\Domain\UploadedFile\DTOs\ProcessUploadedFileInputDTO;
use App\Domain\UploadedFile\Services\ProcessUploadedFileService;
use App\Http\Requests\AttachUsersToStoreRequest;
use App\Http\Requests\StoreStoreRequest;
use App\Http\Requests\UpdateStoreRequest;
use App\Http\Resources\StoreResource;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Contrôleur pour la gestion des magasins.
 *
 * Gère les opérations CRUD sur les magasins ainsi que
 * l'association/dissociation des utilisateurs.
 */
class StoreController extends Controller
{
    public function __construct(
        private readonly ApiResponseSuccessService $successService,
        private readonly ApiResponseSuccessWithPaginationService $successWithPaginationService,
        private readonly ListStoresService $listStoresService,
        private readonly GetStoreService $getStoreService,
        private readonly CreateStoreService $createStoreService,
        private readonly UpdateStoreService $updateStoreService,
        private readonly DeleteStoreService $deleteStoreService,
        private readonly AttachUsersToStoreService $attachUsersService,
        private readonly DetachUserFromStoreService $detachUserService,
        private readonly ProcessUploadedFileService $processUploadedFileService,
    ) {}

    /**
     * Liste tous les magasins (Admin uniquement).
     *
     * Filtres disponibles : search, status, with_trashed, per_page.
     *
     * @param  Request  $request  La requête avec les filtres optionnels
     * @return JsonResponse Liste paginée des magasins
     */
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Store::class);

        $filters = ListStoresFilterDTO::fromArray($request->all());
        $stores = $this->listStoresService->execute($filters);

        $paging = PagingDTO::fromPaginator($stores);

        return $this->successWithPaginationService->execute(
            message: StoreConstant::STORES_RETRIEVED,
            data: StoreResource::collection($stores->items()),
            paging: $paging,
        );
    }

    /**
     * Liste les magasins accessibles à l'utilisateur authentifié.
     *
     * Admin voit tous les magasins, Manager/Employer voient uniquement leurs magasins associés.
     *
     * @param  Request  $request  La requête avec les filtres optionnels
     * @return JsonResponse Liste paginée des magasins accessibles
     */
    public function indexAccessible(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Store::class);

        $filters = ListStoresFilterDTO::fromArray($request->all());
        $stores = $this->listStoresService->execute($filters, $request->user());

        $paging = PagingDTO::fromPaginator($stores);

        return $this->successWithPaginationService->execute(
            message: StoreConstant::STORES_RETRIEVED,
            data: StoreResource::collection($stores->items()),
            paging: $paging,
        );
    }

    /**
     * Crée un nouveau magasin.
     *
     * @param  StoreStoreRequest  $request  Les données validées du magasin
     * @return JsonResponse Le magasin créé avec le code 201
     */
    public function store(StoreStoreRequest $request): JsonResponse
    {
        Gate::authorize('create', Store::class);

        $validated = $request->validated();
        $userId = $request->user()->id;

        // Process banner file if uploaded
        $bannerStoredFileId = null;
        if ($request->hasFile('banner')) {
            $bannerDto = ProcessUploadedFileInputDTO::fromArray([
                'file' => $request->file('banner'),
                'user_id' => $userId,
                'directory' => UploadedFileConstant::STORE_BANNER_DIRECTORY,
            ]);
            $bannerStoredFile = $this->processUploadedFileService->execute($bannerDto);
            $bannerStoredFileId = $bannerStoredFile->id;
        }

        // Process logo file if uploaded
        $logoStoredFileId = null;
        if ($request->hasFile('logo')) {
            $logoDto = ProcessUploadedFileInputDTO::fromArray([
                'file' => $request->file('logo'),
                'user_id' => $userId,
                'directory' => UploadedFileConstant::STORE_LOGO_DIRECTORY,
            ]);
            $logoStoredFile = $this->processUploadedFileService->execute($logoDto);
            $logoStoredFileId = $logoStoredFile->id;
        }

        $dto = CreateStoreInputDTO::fromArray([
            'name' => $validated['name'],
            'banner_stored_file_id' => $bannerStoredFileId,
            'logo_stored_file_id' => $logoStoredFileId,
            'address_id' => $validated['address_id'] ?? null,
            'status' => $validated['status'] ?? null,
        ]);

        $store = $this->createStoreService->execute($dto, $request->user());

        return $this->successService->execute(
            message: StoreConstant::STORE_CREATED,
            data: new StoreResource($store),
            statusCode: 201,
        );
    }

    /**
     * Récupère les détails d'un magasin (Admin uniquement).
     *
     * @param  Store  $store  Le magasin à afficher
     * @return JsonResponse Les détails du magasin
     */
    public function show(Store $store): JsonResponse
    {
        Gate::authorize('view', $store);

        $store = $this->getStoreService->execute($store);

        return $this->successService->execute(
            message: StoreConstant::STORE_RETRIEVED,
            data: new StoreResource($store),
        );
    }

    /**
     * Récupère un magasin si accessible par l'utilisateur authentifié.
     *
     * @param  Store  $store  Le magasin à afficher
     * @return JsonResponse Les détails du magasin
     */
    public function showAccessible(Store $store): JsonResponse
    {
        Gate::authorize('view', $store);

        $store = $this->getStoreService->execute($store);

        return $this->successService->execute(
            message: StoreConstant::STORE_RETRIEVED,
            data: new StoreResource($store),
        );
    }

    /**
     * Met à jour un magasin existant.
     *
     * @param  UpdateStoreRequest  $request  Les données validées de mise à jour
     * @param  Store  $store  Le magasin à modifier
     * @return JsonResponse Le magasin mis à jour
     */
    public function update(UpdateStoreRequest $request, Store $store): JsonResponse
    {
        Gate::authorize('update', $store);

        $validated = $request->validated();
        $userId = $request->user()->id;

        // Process banner file if uploaded
        $bannerStoredFileId = null;
        if ($request->hasFile('banner')) {
            $bannerDto = ProcessUploadedFileInputDTO::fromArray([
                'file' => $request->file('banner'),
                'user_id' => $userId,
                'directory' => UploadedFileConstant::STORE_BANNER_DIRECTORY,
            ]);
            $bannerStoredFile = $this->processUploadedFileService->execute($bannerDto);
            $bannerStoredFileId = $bannerStoredFile->id;
        }

        // Process logo file if uploaded
        $logoStoredFileId = null;
        if ($request->hasFile('logo')) {
            $logoDto = ProcessUploadedFileInputDTO::fromArray([
                'file' => $request->file('logo'),
                'user_id' => $userId,
                'directory' => UploadedFileConstant::STORE_LOGO_DIRECTORY,
            ]);
            $logoStoredFile = $this->processUploadedFileService->execute($logoDto);
            $logoStoredFileId = $logoStoredFile->id;
        }

        $dtoData = array_filter([
            'name' => $validated['name'] ?? null,
            'banner_stored_file_id' => $bannerStoredFileId,
            'logo_stored_file_id' => $logoStoredFileId,
            'address_id' => $validated['address_id'] ?? null,
            'status' => $validated['status'] ?? null,
            'remove_banner' => $validated['remove_banner'] ?? null,
            'remove_logo' => $validated['remove_logo'] ?? null,
            'remove_address' => $validated['remove_address'] ?? null,
        ], fn ($value) => $value !== null);

        $dto = UpdateStoreInputDTO::fromArray($dtoData);
        $updatedStore = $this->updateStoreService->execute($store, $dto);

        return $this->successService->execute(
            message: StoreConstant::STORE_UPDATED,
            data: new StoreResource($updatedStore),
        );
    }

    /**
     * Supprime un magasin (soft delete).
     *
     * @param  Store  $store  Le magasin à supprimer
     * @return JsonResponse Confirmation de suppression
     */
    public function destroy(Store $store): JsonResponse
    {
        Gate::authorize('delete', $store);

        $this->deleteStoreService->execute($store);

        return $this->successService->execute(
            message: StoreConstant::STORE_DELETED,
            data: null,
        );
    }

    /**
     * Associe des utilisateurs à un magasin.
     *
     * Règles métier appliquées :
     * - Les administrateurs ne peuvent pas être associés
     * - Les employés ne peuvent avoir qu'un seul magasin
     *
     * @param  AttachUsersToStoreRequest  $request  Les IDs des utilisateurs à associer
     * @param  Store  $store  Le magasin auquel associer les utilisateurs
     * @return JsonResponse Le magasin mis à jour avec ses utilisateurs
     */
    public function attachUsers(AttachUsersToStoreRequest $request, Store $store): JsonResponse
    {
        Gate::authorize('attachUser', $store);

        $dto = AttachUsersToStoreDTO::fromArray($request->validated());
        $updatedStore = $this->attachUsersService->execute($store, $dto);

        return $this->successService->execute(
            message: StoreConstant::USER_ATTACHED,
            data: new StoreResource($updatedStore),
        );
    }

    /**
     * Dissocie un utilisateur d'un magasin.
     *
     * @param  Store  $store  Le magasin duquel dissocier l'utilisateur
     * @param  User  $user  L'utilisateur à dissocier
     * @return JsonResponse Le magasin mis à jour
     */
    public function detachUser(Store $store, User $user): JsonResponse
    {
        Gate::authorize('detachUser', $store);

        $updatedStore = $this->detachUserService->execute($store, $user);

        return $this->successService->execute(
            message: StoreConstant::USER_DETACHED,
            data: new StoreResource($updatedStore),
        );
    }
}
