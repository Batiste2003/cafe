<?php

namespace App\Http\Controllers;

use App\Domain\ApiResponse\DTOs\PagingDTO;
use App\Domain\ApiResponse\Services\ApiResponseErrorService;
use App\Domain\ApiResponse\Services\ApiResponseSuccessService;
use App\Domain\ApiResponse\Services\ApiResponseSuccessWithPaginationService;
use App\Domain\Product\Constant\ProductConstant;
use App\Domain\Product\DTOs\CreateProductInputDTO;
use App\Domain\Product\DTOs\ListProductsFilterDTO;
use App\Domain\Product\DTOs\UpdateProductInputDTO;
use App\Domain\Product\Services\AttachGalleryToProductService;
use App\Domain\Product\Services\CreateProductService;
use App\Domain\Product\Services\DeleteProductService;
use App\Domain\Product\Services\DetachGalleryFromProductService;
use App\Domain\Product\Services\GetProductService;
use App\Domain\Product\Services\ListProductsService;
use App\Domain\Product\Services\UpdateProductService;
use App\Domain\UploadedFile\Constant\UploadedFileConstant;
use App\Domain\UploadedFile\DTOs\ProcessUploadedFileInputDTO;
use App\Domain\UploadedFile\Services\ProcessUploadedFileService;
use App\Http\Requests\AttachProductGalleryRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\StoredFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use InvalidArgumentException;

/**
 * Contrôleur pour la gestion des produits.
 *
 * Gère les opérations CRUD sur les produits ainsi que la gestion de la galerie.
 */
class ProductController extends Controller
{
    public function __construct(
        private readonly ApiResponseSuccessService $successService,
        private readonly ApiResponseSuccessWithPaginationService $successWithPaginationService,
        private readonly ApiResponseErrorService $errorService,
        private readonly ListProductsService $listProductsService,
        private readonly GetProductService $getProductService,
        private readonly CreateProductService $createProductService,
        private readonly UpdateProductService $updateProductService,
        private readonly DeleteProductService $deleteProductService,
        private readonly AttachGalleryToProductService $attachGalleryService,
        private readonly DetachGalleryFromProductService $detachGalleryService,
        private readonly ProcessUploadedFileService $processUploadedFileService,
    ) {}

    /**
     * Liste tous les produits (Admin uniquement).
     *
     * Filtres disponibles : search, store_id, category_id, is_active, is_featured, with_trashed, per_page.
     *
     * @param  Request  $request  La requête avec les filtres optionnels
     * @return JsonResponse Liste paginée des produits
     */
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Product::class);

        $filters = ListProductsFilterDTO::fromArray($request->all());
        $products = $this->listProductsService->execute($filters);

        $paging = PagingDTO::fromPaginator($products);

        return $this->successWithPaginationService->execute(
            message: ProductConstant::PRODUCTS_RETRIEVED,
            data: ProductResource::collection($products->items()),
            paging: $paging,
        );
    }

    /**
     * Liste les produits accessibles à l'utilisateur authentifié.
     *
     * Admin voit tous les produits, Manager/Employer voient uniquement les produits de leurs magasins.
     *
     * @param  Request  $request  La requête avec les filtres optionnels
     * @return JsonResponse Liste paginée des produits accessibles
     */
    public function indexAccessible(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Product::class);

        $filters = ListProductsFilterDTO::fromArray($request->all());
        $products = $this->listProductsService->execute($filters, $request->user());

        $paging = PagingDTO::fromPaginator($products);

        return $this->successWithPaginationService->execute(
            message: ProductConstant::PRODUCTS_RETRIEVED,
            data: ProductResource::collection($products->items()),
            paging: $paging,
        );
    }

    /**
     * Crée un nouveau produit.
     *
     * @param  StoreProductRequest  $request  Les données validées du produit
     * @return JsonResponse Le produit créé avec le code 201
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        Gate::authorize('create', Product::class);

        $dto = CreateProductInputDTO::fromArray($request->validated());
        $product = $this->createProductService->execute($dto, $request->user());

        // Traiter les images si fournies
        if ($request->hasFile('images')) {
            $this->processAndAttachImages($product, $request->file('images'), $request->user()->id);
            $product = $product->fresh()->load(['category', 'stores', 'creator', 'variants.storeStocks', 'gallery']);
        }

        return $this->successService->execute(
            message: ProductConstant::PRODUCT_CREATED,
            data: new ProductResource($product),
            statusCode: 201,
        );
    }

    /**
     * Récupère les détails d'un produit (Admin uniquement).
     *
     * @param  Product  $product  Le produit à afficher
     * @return JsonResponse Les détails du produit
     */
    public function show(Product $product): JsonResponse
    {
        Gate::authorize('view', $product);

        $product = $this->getProductService->execute($product);

        return $this->successService->execute(
            message: ProductConstant::PRODUCT_RETRIEVED,
            data: new ProductResource($product),
        );
    }

    /**
     * Récupère un produit si accessible par l'utilisateur authentifié.
     *
     * @param  Product  $product  Le produit à afficher
     * @return JsonResponse Les détails du produit
     */
    public function showAccessible(Product $product): JsonResponse
    {
        Gate::authorize('view', $product);

        $product = $this->getProductService->execute($product);

        return $this->successService->execute(
            message: ProductConstant::PRODUCT_RETRIEVED,
            data: new ProductResource($product),
        );
    }

    /**
     * Met à jour un produit existant.
     *
     * @param  UpdateProductRequest  $request  Les données validées de mise à jour
     * @param  Product  $product  Le produit à modifier
     * @return JsonResponse Le produit mis à jour
     */
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        Gate::authorize('update', $product);

        $dto = UpdateProductInputDTO::fromArray($request->validated());
        $product = $this->updateProductService->execute($product, $dto);

        // Traiter les images si fournies
        if ($request->hasFile('images')) {
            $this->processAndAttachImages($product, $request->file('images'), $request->user()->id);
            $product = $product->fresh()->load(['category', 'stores', 'creator', 'variants.storeStocks', 'gallery']);
        }

        return $this->successService->execute(
            message: ProductConstant::PRODUCT_UPDATED,
            data: new ProductResource($product),
        );
    }

    /**
     * Supprime un produit (soft delete).
     *
     * @param  Product  $product  Le produit à supprimer
     * @return JsonResponse Confirmation de suppression
     */
    public function destroy(Product $product): JsonResponse
    {
        Gate::authorize('delete', $product);

        $this->deleteProductService->execute($product);

        return $this->successService->execute(
            message: ProductConstant::PRODUCT_DELETED,
            data: null,
        );
    }

    /**
     * Ajoute une image à la galerie du produit.
     *
     * @param  AttachProductGalleryRequest  $request  La requête avec l'image
     * @param  Product  $product  Le produit concerné
     * @return JsonResponse Le produit mis à jour
     */
    public function attachGallery(AttachProductGalleryRequest $request, Product $product): JsonResponse
    {
        Gate::authorize('manageGallery', $product);

        try {
            $validated = $request->validated();
            $userId = $request->user()->id;

            // Process uploaded file
            $imageDto = ProcessUploadedFileInputDTO::fromArray([
                'file' => $request->file('image'),
                'user_id' => $userId,
                'directory' => UploadedFileConstant::PRODUCT_GALLERY_DIRECTORY,
            ]);
            $storedFile = $this->processUploadedFileService->execute($imageDto);

            $product = $this->attachGalleryService->execute(
                $product,
                $storedFile->id,
                $validated['position'] ?? null
            );

            return $this->successService->execute(
                message: ProductConstant::GALLERY_IMAGE_ADDED,
                data: new ProductResource($product),
            );
        } catch (InvalidArgumentException $e) {
            return $this->errorService->execute(
                message: $e->getMessage(),
                statusCode: 422,
            );
        }
    }

    /**
     * Retire une image de la galerie du produit.
     *
     * @param  Product  $product  Le produit concerné
     * @param  StoredFile  $storedFile  Le fichier à retirer
     * @return JsonResponse Le produit mis à jour
     */
    public function detachGallery(Product $product, StoredFile $storedFile): JsonResponse
    {
        Gate::authorize('manageGallery', $product);

        $product = $this->detachGalleryService->execute($product, $storedFile->id);

        return $this->successService->execute(
            message: ProductConstant::GALLERY_IMAGE_REMOVED,
            data: new ProductResource($product),
        );
    }

    /**
     * Traite et attache les images uploadées à un produit.
     *
     * @param  Product  $product  Le produit
     * @param  array<\Illuminate\Http\UploadedFile>  $images  Les fichiers images
     * @param  int  $userId  L'ID de l'utilisateur
     */
    private function processAndAttachImages(Product $product, array $images, int $userId): void
    {
        $currentCount = $product->gallery()->count();
        $position = $currentCount;

        foreach ($images as $image) {
            if ($position >= ProductConstant::MAX_GALLERY_IMAGES) {
                break;
            }

            $imageDto = ProcessUploadedFileInputDTO::fromArray([
                'file' => $image,
                'user_id' => $userId,
                'directory' => UploadedFileConstant::PRODUCT_GALLERY_DIRECTORY,
            ]);

            $storedFile = $this->processUploadedFileService->execute($imageDto);

            $this->attachGalleryService->execute($product, $storedFile->id, $position);
            $position++;
        }
    }
}
