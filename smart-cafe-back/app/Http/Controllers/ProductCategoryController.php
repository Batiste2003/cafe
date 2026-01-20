<?php

namespace App\Http\Controllers;

use App\Domain\ApiResponse\DTOs\PagingDTO;
use App\Domain\ApiResponse\Services\ApiResponseErrorService;
use App\Domain\ApiResponse\Services\ApiResponseSuccessService;
use App\Domain\ApiResponse\Services\ApiResponseSuccessWithPaginationService;
use App\Domain\ProductCategory\Constant\ProductCategoryConstant;
use App\Domain\ProductCategory\DTOs\CreateProductCategoryInputDTO;
use App\Domain\ProductCategory\DTOs\ListProductCategoriesFilterDTO;
use App\Domain\ProductCategory\DTOs\UpdateProductCategoryInputDTO;
use App\Domain\ProductCategory\Services\CreateProductCategoryService;
use App\Domain\ProductCategory\Services\DeleteProductCategoryService;
use App\Domain\ProductCategory\Services\GetProductCategoryService;
use App\Domain\ProductCategory\Services\ListProductCategoriesService;
use App\Domain\ProductCategory\Services\UpdateProductCategoryService;
use App\Http\Requests\StoreProductCategoryRequest;
use App\Http\Requests\UpdateProductCategoryRequest;
use App\Http\Resources\ProductCategoryResource;
use App\Models\ProductCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use InvalidArgumentException;

/**
 * Contrôleur pour la gestion des catégories de produits.
 *
 * Gère les opérations CRUD sur les catégories.
 */
class ProductCategoryController extends Controller
{
    public function __construct(
        private readonly ApiResponseSuccessService $successService,
        private readonly ApiResponseSuccessWithPaginationService $successWithPaginationService,
        private readonly ApiResponseErrorService $errorService,
        private readonly ListProductCategoriesService $listCategoriesService,
        private readonly GetProductCategoryService $getCategoryService,
        private readonly CreateProductCategoryService $createCategoryService,
        private readonly UpdateProductCategoryService $updateCategoryService,
        private readonly DeleteProductCategoryService $deleteCategoryService,
    ) {}

    /**
     * Liste toutes les catégories.
     *
     * Filtres disponibles : search, is_active, parent_id, only_root, with_trashed, per_page.
     *
     * @param  Request  $request  La requête avec les filtres optionnels
     * @return JsonResponse Liste paginée des catégories
     */
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', ProductCategory::class);

        $filters = ListProductCategoriesFilterDTO::fromArray($request->all());
        $categories = $this->listCategoriesService->execute($filters);

        $paging = PagingDTO::fromPaginator($categories);

        return $this->successWithPaginationService->execute(
            message: ProductCategoryConstant::CATEGORIES_RETRIEVED,
            data: ProductCategoryResource::collection($categories->items()),
            paging: $paging,
        );
    }

    /**
     * Crée une nouvelle catégorie.
     *
     * @param  StoreProductCategoryRequest  $request  Les données validées de la catégorie
     * @return JsonResponse La catégorie créée avec le code 201
     */
    public function store(StoreProductCategoryRequest $request): JsonResponse
    {
        Gate::authorize('create', ProductCategory::class);

        $dto = CreateProductCategoryInputDTO::fromArray($request->validated());
        $category = $this->createCategoryService->execute($dto);

        return $this->successService->execute(
            message: ProductCategoryConstant::CATEGORY_CREATED,
            data: new ProductCategoryResource($category),
            statusCode: 201,
        );
    }

    /**
     * Récupère les détails d'une catégorie.
     *
     * @param  ProductCategory  $productCategory  La catégorie à afficher
     * @return JsonResponse Les détails de la catégorie
     */
    public function show(ProductCategory $productCategory): JsonResponse
    {
        Gate::authorize('view', $productCategory);

        $category = $this->getCategoryService->execute($productCategory);

        return $this->successService->execute(
            message: ProductCategoryConstant::CATEGORY_RETRIEVED,
            data: new ProductCategoryResource($category),
        );
    }

    /**
     * Met à jour une catégorie existante.
     *
     * @param  UpdateProductCategoryRequest  $request  Les données validées de mise à jour
     * @param  ProductCategory  $productCategory  La catégorie à modifier
     * @return JsonResponse La catégorie mise à jour
     */
    public function update(UpdateProductCategoryRequest $request, ProductCategory $productCategory): JsonResponse
    {
        Gate::authorize('update', $productCategory);

        try {
            $dto = UpdateProductCategoryInputDTO::fromArray($request->validated());
            $category = $this->updateCategoryService->execute($productCategory, $dto);

            return $this->successService->execute(
                message: ProductCategoryConstant::CATEGORY_UPDATED,
                data: new ProductCategoryResource($category),
            );
        } catch (InvalidArgumentException $e) {
            return $this->errorService->execute(
                message: $e->getMessage(),
                statusCode: 422,
            );
        }
    }

    /**
     * Supprime une catégorie (soft delete).
     *
     * @param  ProductCategory  $productCategory  La catégorie à supprimer
     * @return JsonResponse Confirmation de suppression
     */
    public function destroy(ProductCategory $productCategory): JsonResponse
    {
        Gate::authorize('delete', $productCategory);

        try {
            $this->deleteCategoryService->execute($productCategory);

            return $this->successService->execute(
                message: ProductCategoryConstant::CATEGORY_DELETED,
                data: null,
            );
        } catch (InvalidArgumentException $e) {
            return $this->errorService->execute(
                message: $e->getMessage(),
                statusCode: 422,
            );
        }
    }
}
