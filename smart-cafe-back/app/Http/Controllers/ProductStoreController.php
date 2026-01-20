<?php

namespace App\Http\Controllers;

use App\Domain\ApiResponse\Services\ApiResponseErrorService;
use App\Domain\ApiResponse\Services\ApiResponseSuccessService;
use App\Domain\Product\Constant\ProductConstant;
use App\Domain\Product\DTOs\AttachProductToStoresInputDTO;
use App\Domain\Product\DTOs\UpdateVariantStockInputDTO;
use App\Domain\Product\Services\AttachProductToStoresService;
use App\Domain\Product\Services\DeleteVariantStockService;
use App\Domain\Product\Services\DetachProductFromStoreService;
use App\Domain\Product\Services\UpdateVariantStockService;
use App\Http\Requests\AttachProductToStoresRequest;
use App\Http\Requests\UpdateVariantStockRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\StoreProductVariantResource;
use App\Http\Resources\StoreResource;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

/**
 * Contrôleur pour la gestion des associations produit-store et des stocks.
 *
 * Gère l'association/dissociation des produits aux stores et la gestion
 * des stocks par variant par store.
 */
class ProductStoreController extends Controller
{
    public function __construct(
        private readonly ApiResponseSuccessService $successService,
        private readonly ApiResponseErrorService $errorService,
        private readonly AttachProductToStoresService $attachProductToStoresService,
        private readonly DetachProductFromStoreService $detachProductFromStoreService,
        private readonly UpdateVariantStockService $updateVariantStockService,
        private readonly DeleteVariantStockService $deleteVariantStockService,
    ) {}

    /**
     * Liste les stores associés à un produit.
     *
     * @param  Product  $product  Le produit concerné
     * @return JsonResponse Liste des stores du produit
     */
    public function indexStores(Product $product): JsonResponse
    {
        Gate::authorize('view', $product);

        $product->load('stores');

        return $this->successService->execute(
            message: ProductConstant::PRODUCT_STORES_RETRIEVED,
            data: StoreResource::collection($product->stores),
        );
    }

    /**
     * Associe un produit à plusieurs stores.
     *
     * @param  AttachProductToStoresRequest  $request  Les données validées
     * @param  Product  $product  Le produit à associer
     * @return JsonResponse Le produit avec ses stores mis à jour
     */
    public function attachStores(AttachProductToStoresRequest $request, Product $product): JsonResponse
    {
        Gate::authorize('update', $product);

        $dto = AttachProductToStoresInputDTO::fromArray($request->validated());
        $product = $this->attachProductToStoresService->execute($product, $dto);

        return $this->successService->execute(
            message: ProductConstant::STORES_ATTACHED,
            data: new ProductResource($product),
        );
    }

    /**
     * Retire un produit d'un store.
     *
     * Supprime également les stocks des variants de ce produit dans ce store.
     *
     * @param  Product  $product  Le produit concerné
     * @param  Store  $store  Le store duquel retirer le produit
     * @return JsonResponse Le produit avec ses stores mis à jour
     */
    public function detachStore(Product $product, Store $store): JsonResponse
    {
        Gate::authorize('update', $product);

        // Vérifier que le produit est bien dans ce store
        if (! $product->stores()->where('stores.id', $store->id)->exists()) {
            return $this->errorService->execute(
                message: ProductConstant::PRODUCT_NOT_IN_STORE,
                statusCode: 404,
            );
        }

        $product = $this->detachProductFromStoreService->execute($product, $store);

        return $this->successService->execute(
            message: ProductConstant::STORE_DETACHED,
            data: new ProductResource($product),
        );
    }

    /**
     * Liste les stocks d'un variant par store.
     *
     * @param  Product  $product  Le produit parent
     * @param  ProductVariant  $variant  Le variant concerné
     * @return JsonResponse Liste des stocks par store
     */
    public function indexVariantStocks(Product $product, ProductVariant $variant): JsonResponse
    {
        Gate::authorize('view', $product);

        // Vérifier que le variant appartient bien au produit
        if ($variant->product_id !== $product->id) {
            return $this->errorService->execute(
                message: ProductConstant::VARIANT_NOT_IN_STORE,
                statusCode: 404,
            );
        }

        $variant->load('storeStocks.store');

        return $this->successService->execute(
            message: ProductConstant::VARIANT_STOCKS_RETRIEVED,
            data: StoreProductVariantResource::collection($variant->storeStocks),
        );
    }

    /**
     * Met à jour ou crée le stock d'un variant dans un store.
     *
     * @param  UpdateVariantStockRequest  $request  Les données validées
     * @param  Product  $product  Le produit parent
     * @param  ProductVariant  $variant  Le variant concerné
     * @param  Store  $store  Le store concerné
     * @return JsonResponse L'entrée de stock mise à jour
     */
    public function updateVariantStock(
        UpdateVariantStockRequest $request,
        Product $product,
        ProductVariant $variant,
        Store $store
    ): JsonResponse {
        Gate::authorize('update', $product);

        // Vérifier que le variant appartient bien au produit
        if ($variant->product_id !== $product->id) {
            return $this->errorService->execute(
                message: ProductConstant::VARIANT_NOT_IN_STORE,
                statusCode: 404,
            );
        }

        // Vérifier que le produit est associé au store
        if (! $product->stores()->where('stores.id', $store->id)->exists()) {
            return $this->errorService->execute(
                message: ProductConstant::PRODUCT_NOT_IN_STORE,
                statusCode: 422,
            );
        }

        $dto = UpdateVariantStockInputDTO::fromArray($request->validated());
        $storeProductVariant = $this->updateVariantStockService->execute($variant, $store, $dto);
        $storeProductVariant->load('store', 'variant');

        return $this->successService->execute(
            message: ProductConstant::VARIANT_STOCK_UPDATED,
            data: new StoreProductVariantResource($storeProductVariant),
        );
    }

    /**
     * Supprime le stock d'un variant dans un store.
     *
     * @param  Product  $product  Le produit parent
     * @param  ProductVariant  $variant  Le variant concerné
     * @param  Store  $store  Le store concerné
     * @return JsonResponse Confirmation de suppression
     */
    public function deleteVariantStock(Product $product, ProductVariant $variant, Store $store): JsonResponse
    {
        Gate::authorize('update', $product);

        // Vérifier que le variant appartient bien au produit
        if ($variant->product_id !== $product->id) {
            return $this->errorService->execute(
                message: ProductConstant::VARIANT_NOT_IN_STORE,
                statusCode: 404,
            );
        }

        $deleted = $this->deleteVariantStockService->execute($variant, $store);

        if (! $deleted) {
            return $this->errorService->execute(
                message: ProductConstant::VARIANT_NOT_IN_STORE,
                statusCode: 404,
            );
        }

        return $this->successService->execute(
            message: ProductConstant::VARIANT_STOCK_DELETED,
            data: null,
        );
    }
}
