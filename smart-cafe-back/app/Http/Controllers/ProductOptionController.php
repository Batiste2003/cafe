<?php

namespace App\Http\Controllers;

use App\Domain\ApiResponse\Services\ApiResponseSuccessService;
use App\Domain\Product\Constant\ProductConstant;
use App\Domain\Product\DTOs\CreateProductOptionInputDTO;
use App\Domain\Product\DTOs\UpdateProductOptionInputDTO;
use App\Domain\Product\Services\CreateProductOptionService;
use App\Domain\Product\Services\DeleteProductOptionService;
use App\Domain\Product\Services\UpdateProductOptionService;
use App\Http\Requests\StoreProductOptionRequest;
use App\Http\Requests\UpdateProductOptionRequest;
use App\Http\Resources\ProductOptionResource;
use App\Models\Product;
use App\Models\ProductOption;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class ProductOptionController extends Controller
{
    public function __construct(
        private readonly CreateProductOptionService $createProductOptionService,
        private readonly UpdateProductOptionService $updateProductOptionService,
        private readonly DeleteProductOptionService $deleteProductOptionService,
        private readonly ApiResponseSuccessService $apiResponseSuccessService,
    ) {}

    /**
     * Liste les options d'un produit.
     */
    public function index(Product $product): JsonResponse
    {
        Gate::authorize('viewAny', [ProductOption::class, $product]);

        $options = $product->options()->with('values')->get();

        return $this->apiResponseSuccessService->execute(
            ProductConstant::OPTIONS_RETRIEVED,
            ProductOptionResource::collection($options),
        );
    }

    /**
     * Crée une nouvelle option pour un produit.
     */
    public function store(StoreProductOptionRequest $request, Product $product): JsonResponse
    {
        Gate::authorize('create', [ProductOption::class, $product]);

        $dto = new CreateProductOptionInputDTO(
            name: $request->validated('name'),
            isRequired: $request->validated('is_required', false),
        );

        $option = $this->createProductOptionService->execute($product, $dto);

        return $this->apiResponseSuccessService->execute(
            ProductConstant::OPTION_CREATED,
            new ProductOptionResource($option),
            201,
        );
    }

    /**
     * Affiche une option.
     */
    public function show(Product $product, ProductOption $option): JsonResponse
    {
        Gate::authorize('view', $option);

        $option->load(['values', 'product']);

        return $this->apiResponseSuccessService->execute(
            ProductConstant::OPTION_RETRIEVED,
            new ProductOptionResource($option),
        );
    }

    /**
     * Met à jour une option.
     */
    public function update(UpdateProductOptionRequest $request, Product $product, ProductOption $option): JsonResponse
    {
        Gate::authorize('update', $option);

        $dto = new UpdateProductOptionInputDTO(
            name: $request->validated('name'),
            isRequired: $request->validated('is_required'),
        );

        $option = $this->updateProductOptionService->execute($option, $dto);

        return $this->apiResponseSuccessService->execute(
            ProductConstant::OPTION_UPDATED,
            new ProductOptionResource($option),
        );
    }

    /**
     * Supprime une option.
     */
    public function destroy(Product $product, ProductOption $option): JsonResponse
    {
        Gate::authorize('delete', $option);

        $this->deleteProductOptionService->execute($option);

        return $this->apiResponseSuccessService->execute(
            ProductConstant::OPTION_DELETED,
        );
    }
}
