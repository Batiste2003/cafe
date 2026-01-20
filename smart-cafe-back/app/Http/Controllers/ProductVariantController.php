<?php

namespace App\Http\Controllers;

use App\Domain\ApiResponse\Services\ApiResponseErrorService;
use App\Domain\ApiResponse\Services\ApiResponseSuccessService;
use App\Domain\Product\Constant\ProductConstant;
use App\Domain\Product\DTOs\CreateProductVariantInputDTO;
use App\Domain\Product\DTOs\UpdateProductVariantInputDTO;
use App\Domain\Product\Services\AttachGalleryToVariantService;
use App\Domain\Product\Services\CreateProductVariantService;
use App\Domain\Product\Services\DeleteProductVariantService;
use App\Domain\Product\Services\DetachGalleryFromVariantService;
use App\Domain\Product\Services\UpdateProductVariantService;
use App\Domain\UploadedFile\Constant\UploadedFileConstant;
use App\Domain\UploadedFile\DTOs\ProcessUploadedFileInputDTO;
use App\Domain\UploadedFile\Services\ProcessUploadedFileService;
use App\Http\Requests\AttachVariantGalleryRequest;
use App\Http\Requests\StoreProductVariantRequest;
use App\Http\Requests\UpdateProductVariantRequest;
use App\Http\Resources\ProductVariantResource;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StoredFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use InvalidArgumentException;

/**
 * Contrôleur pour la gestion des variants de produits.
 *
 * Gère les opérations CRUD sur les variants ainsi que la gestion de la galerie.
 */
class ProductVariantController extends Controller
{
    public function __construct(
        private readonly ApiResponseSuccessService $successService,
        private readonly ApiResponseErrorService $errorService,
        private readonly CreateProductVariantService $createVariantService,
        private readonly UpdateProductVariantService $updateVariantService,
        private readonly DeleteProductVariantService $deleteVariantService,
        private readonly AttachGalleryToVariantService $attachGalleryService,
        private readonly DetachGalleryFromVariantService $detachGalleryService,
        private readonly ProcessUploadedFileService $processUploadedFileService,
    ) {}

    /**
     * Liste les variants d'un produit.
     *
     * @param  Product  $product  Le produit parent
     * @return JsonResponse Liste des variants
     */
    public function index(Product $product): JsonResponse
    {
        Gate::authorize('viewAny', ProductVariant::class);

        $variants = $product->variants()->with(['gallery', 'optionValues'])->get();

        return $this->successService->execute(
            message: 'Liste des variants récupérée avec succès.',
            data: ProductVariantResource::collection($variants),
        );
    }

    /**
     * Crée un nouveau variant pour un produit.
     *
     * @param  StoreProductVariantRequest  $request  Les données validées du variant
     * @param  Product  $product  Le produit parent
     * @return JsonResponse Le variant créé avec le code 201
     */
    public function store(StoreProductVariantRequest $request, Product $product): JsonResponse
    {
        Gate::authorize('create', ProductVariant::class);

        $data = array_merge($request->validated(), ['product_id' => $product->id]);
        $dto = CreateProductVariantInputDTO::fromArray($data);
        $variant = $this->createVariantService->execute($product, $dto);

        // Traiter les images si fournies
        if ($request->hasFile('images')) {
            $this->processAndAttachImages($variant, $request->file('images'), $request->user()->id);
            $variant = $variant->fresh()->load(['product', 'gallery', 'optionValues', 'storeStocks']);
        }

        return $this->successService->execute(
            message: 'Variant créé avec succès.',
            data: new ProductVariantResource($variant),
            statusCode: 201,
        );
    }

    /**
     * Récupère les détails d'un variant.
     *
     * @param  Product  $product  Le produit parent
     * @param  ProductVariant  $variant  Le variant à afficher
     * @return JsonResponse Les détails du variant
     */
    public function show(Product $product, ProductVariant $variant): JsonResponse
    {
        Gate::authorize('view', $variant);

        $variant->load(['product', 'gallery', 'optionValues']);

        return $this->successService->execute(
            message: 'Variant récupéré avec succès.',
            data: new ProductVariantResource($variant),
        );
    }

    /**
     * Met à jour un variant existant.
     *
     * @param  UpdateProductVariantRequest  $request  Les données validées de mise à jour
     * @param  Product  $product  Le produit parent
     * @param  ProductVariant  $variant  Le variant à modifier
     * @return JsonResponse Le variant mis à jour
     */
    public function update(UpdateProductVariantRequest $request, Product $product, ProductVariant $variant): JsonResponse
    {
        Gate::authorize('update', $variant);

        $dto = UpdateProductVariantInputDTO::fromArray($request->validated());
        $variant = $this->updateVariantService->execute($variant, $dto);

        // Traiter les images si fournies
        if ($request->hasFile('images')) {
            $this->processAndAttachImages($variant, $request->file('images'), $request->user()->id);
            $variant = $variant->fresh()->load(['product', 'gallery', 'optionValues', 'storeStocks']);
        }

        return $this->successService->execute(
            message: 'Variant mis à jour avec succès.',
            data: new ProductVariantResource($variant),
        );
    }

    /**
     * Supprime un variant (soft delete).
     *
     * @param  Product  $product  Le produit parent
     * @param  ProductVariant  $variant  Le variant à supprimer
     * @return JsonResponse Confirmation de suppression
     */
    public function destroy(Product $product, ProductVariant $variant): JsonResponse
    {
        Gate::authorize('delete', $variant);

        $this->deleteVariantService->execute($variant);

        return $this->successService->execute(
            message: 'Variant supprimé avec succès.',
            data: null,
        );
    }

    /**
     * Ajoute une image à la galerie du variant.
     *
     * @param  AttachVariantGalleryRequest  $request  La requête avec l'image
     * @param  Product  $product  Le produit parent
     * @param  ProductVariant  $variant  Le variant concerné
     * @return JsonResponse Le variant mis à jour
     */
    public function attachGallery(AttachVariantGalleryRequest $request, Product $product, ProductVariant $variant): JsonResponse
    {
        Gate::authorize('manageGallery', $variant);

        try {
            $validated = $request->validated();
            $userId = $request->user()->id;

            // Process uploaded file
            $imageDto = ProcessUploadedFileInputDTO::fromArray([
                'file' => $request->file('image'),
                'user_id' => $userId,
                'directory' => UploadedFileConstant::PRODUCT_VARIANT_GALLERY_DIRECTORY,
            ]);
            $storedFile = $this->processUploadedFileService->execute($imageDto);

            $variant = $this->attachGalleryService->execute(
                $variant,
                $storedFile->id,
                $validated['position'] ?? null
            );

            return $this->successService->execute(
                message: ProductConstant::GALLERY_IMAGE_ADDED,
                data: new ProductVariantResource($variant),
            );
        } catch (InvalidArgumentException $e) {
            return $this->errorService->execute(
                message: $e->getMessage(),
                statusCode: 422,
            );
        }
    }

    /**
     * Retire une image de la galerie du variant.
     *
     * @param  Product  $product  Le produit parent
     * @param  ProductVariant  $variant  Le variant concerné
     * @param  StoredFile  $storedFile  Le fichier à retirer
     * @return JsonResponse Le variant mis à jour
     */
    public function detachGallery(Product $product, ProductVariant $variant, StoredFile $storedFile): JsonResponse
    {
        Gate::authorize('manageGallery', $variant);

        $variant = $this->detachGalleryService->execute($variant, $storedFile->id);

        return $this->successService->execute(
            message: ProductConstant::GALLERY_IMAGE_REMOVED,
            data: new ProductVariantResource($variant),
        );
    }

    /**
     * Traite et attache les images uploadées à un variant.
     *
     * @param  ProductVariant  $variant  Le variant
     * @param  array<\Illuminate\Http\UploadedFile>  $images  Les fichiers images
     * @param  int  $userId  L'ID de l'utilisateur
     */
    private function processAndAttachImages(ProductVariant $variant, array $images, int $userId): void
    {
        $currentCount = $variant->gallery()->count();
        $position = $currentCount;

        foreach ($images as $image) {
            if ($position >= ProductConstant::MAX_GALLERY_IMAGES) {
                break;
            }

            $imageDto = ProcessUploadedFileInputDTO::fromArray([
                'file' => $image,
                'user_id' => $userId,
                'directory' => UploadedFileConstant::PRODUCT_VARIANT_GALLERY_DIRECTORY,
            ]);

            $storedFile = $this->processUploadedFileService->execute($imageDto);

            $this->attachGalleryService->execute($variant, $storedFile->id, $position);
            $position++;
        }
    }
}
