<?php

namespace App\Http\Controllers;

use App\Domain\ApiResponse\Services\ApiResponseSuccessService;
use App\Domain\Product\Constant\ProductConstant;
use App\Domain\Product\DTOs\CreateProductOptionValueInputDTO;
use App\Domain\Product\DTOs\UpdateProductOptionValueInputDTO;
use App\Domain\Product\Services\CreateProductOptionValueService;
use App\Domain\Product\Services\DeleteProductOptionValueService;
use App\Domain\Product\Services\UpdateProductOptionValueService;
use App\Http\Requests\StoreProductOptionValueRequest;
use App\Http\Requests\UpdateProductOptionValueRequest;
use App\Http\Resources\ProductOptionValueResource;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class ProductOptionValueController extends Controller
{
    public function __construct(
        private readonly CreateProductOptionValueService $createProductOptionValueService,
        private readonly UpdateProductOptionValueService $updateProductOptionValueService,
        private readonly DeleteProductOptionValueService $deleteProductOptionValueService,
        private readonly ApiResponseSuccessService $apiResponseSuccessService,
    ) {}

    /**
     * Liste les valeurs d'une option.
     */
    public function index(ProductOption $option): JsonResponse
    {
        Gate::authorize('viewAny', [ProductOptionValue::class, $option]);

        $values = $option->values;

        return $this->apiResponseSuccessService->create(
            ProductConstant::OPTION_VALUES_RETRIEVED,
            ProductOptionValueResource::collection($values),
        );
    }

    /**
     * Crée une nouvelle valeur pour une option.
     */
    public function store(StoreProductOptionValueRequest $request, ProductOption $option): JsonResponse
    {
        Gate::authorize('create', [ProductOptionValue::class, $option]);

        $dto = new CreateProductOptionValueInputDTO(
            value: $request->validated('value'),
            priceAddHt: $request->validated('price_add_ht'),
        );

        $value = $this->createProductOptionValueService->execute($option, $dto);

        return $this->apiResponseSuccessService->create(
            ProductConstant::OPTION_VALUE_CREATED,
            new ProductOptionValueResource($value),
            201,
        );
    }

    /**
     * Affiche une valeur d'option.
     */
    public function show(ProductOption $option, ProductOptionValue $value): JsonResponse
    {
        Gate::authorize('view', $value);

        $value->load(['option']);

        return $this->apiResponseSuccessService->create(
            ProductConstant::OPTION_VALUE_RETRIEVED,
            new ProductOptionValueResource($value),
        );
    }

    /**
     * Met à jour une valeur d'option.
     */
    public function update(UpdateProductOptionValueRequest $request, ProductOption $option, ProductOptionValue $value): JsonResponse
    {
        Gate::authorize('update', $value);

        $dto = new UpdateProductOptionValueInputDTO(
            value: $request->validated('value'),
            priceAddHt: $request->validated('price_add_ht'),
        );

        $value = $this->updateProductOptionValueService->execute($value, $dto);

        return $this->apiResponseSuccessService->create(
            ProductConstant::OPTION_VALUE_UPDATED,
            new ProductOptionValueResource($value),
        );
    }

    /**
     * Supprime une valeur d'option.
     */
    public function destroy(ProductOption $option, ProductOptionValue $value): JsonResponse
    {
        Gate::authorize('delete', $value);

        $this->deleteProductOptionValueService->execute($value);

        return $this->apiResponseSuccessService->create(
            ProductConstant::OPTION_VALUE_DELETED,
        );
    }
}
