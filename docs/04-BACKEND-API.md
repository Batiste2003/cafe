# 04 - Backend API (Laravel)

> Architecture et bonnes pratiques de l'API REST Laravel

---

## Table des matières

1. [Choix Technologiques](#1-choix-technologiques)
2. [Architecture DDD](#2-architecture-ddd)
3. [Couche Domain (Services)](#3-couche-domain-services)
4. [Couche HTTP (Controllers)](#4-couche-http-controllers)
5. [DTOs et Validation](#5-dtos-et-validation)
6. [Resources (Transformation JSON)](#6-resources-transformation-json)
7. [Policies (Autorisation)](#7-policies-autorisation)
8. [Gestion des Erreurs](#8-gestion-des-erreurs)
9. [Logging et Journalisation](#9-logging-et-journalisation)
10. [Scalabilité](#10-scalabilité)

---

## 1. Choix Technologiques

### 1.1 Stack Technique

| Technologie | Version | Rôle |
|-------------|---------|------|
| **Laravel** | 12.0 | Framework PHP |
| **PHP** | 8.2+ | Langage |
| **Laravel Sanctum** | 4.0 | Authentification API |
| **Spatie Permission** | 6.24 | Gestion des rôles (RBAC) |
| **MySQL / SQLite** | - | Base de données |
| **PestPHP** | - | Tests unitaires |

### 1.2 Justification de Laravel

```
┌─────────────────────────────────────────────────────────────────┐
│                    POURQUOI LARAVEL 12 ?                        │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│   ✅ Productivité                                               │
│      • Artisan CLI puissant                                    │
│      • Migrations et seeders automatisés                       │
│      • Eloquent ORM expressif                                  │
│                                                                 │
│   ✅ Sécurité                                                   │
│      • Protection CSRF native                                  │
│      • Hashing bcrypt automatique                              │
│      • Protection SQL injection (query builder)                │
│                                                                 │
│   ✅ API Development                                            │
│      • Sanctum pour l'authentification token                   │
│      • API Resources pour la transformation                    │
│      • Rate limiting intégré                                   │
│                                                                 │
│   ✅ Écosystème                                                 │
│      • Packages matures (Spatie)                               │
│      • Documentation excellente                                │
│      • Communauté active                                       │
│                                                                 │
│   ✅ Testing                                                    │
│      • PestPHP / PHPUnit intégré                               │
│      • Factories et seeders                                    │
│      • HTTP tests fluides                                      │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 1.3 Comparaison avec Alternatives

| Critère | Laravel | Symfony | Express.js | NestJS |
|---------|---------|---------|------------|--------|
| Productivité | ⭐⭐⭐⭐⭐ | ⭐⭐⭐ | ⭐⭐⭐ | ⭐⭐⭐⭐ |
| Courbe apprentissage | Facile | Modérée | Facile | Modérée |
| ORM | Eloquent | Doctrine | Sequelize | TypeORM |
| Auth API | Sanctum | JWT | Passport | Passport |
| Écosystème | Très riche | Riche | Très riche | Riche |

---

## 2. Architecture DDD

### 2.1 Domain-Driven Design

Le backend suit les principes du **Domain-Driven Design** (DDD) avec une séparation claire des responsabilités.

```
┌─────────────────────────────────────────────────────────────────┐
│                    ARCHITECTURE DDD                             │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│   ┌─────────────────────────────────────────────────────────┐  │
│   │                    HTTP LAYER                           │  │
│   │  Controllers │ Requests │ Resources │ Middleware        │  │
│   └─────────────────────────┬───────────────────────────────┘  │
│                             │                                   │
│                             ▼                                   │
│   ┌─────────────────────────────────────────────────────────┐  │
│   │                   DOMAIN LAYER                          │  │
│   │  Services │ DTOs │ Constants │ Exceptions               │  │
│   └─────────────────────────┬───────────────────────────────┘  │
│                             │                                   │
│                             ▼                                   │
│   ┌─────────────────────────────────────────────────────────┐  │
│   │                INFRASTRUCTURE LAYER                     │  │
│   │  Models (Eloquent) │ Policies │ Repositories            │  │
│   └─────────────────────────────────────────────────────────┘  │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 2.2 Structure du Dossier Domain

```
app/Domain/
├── Address/
│   ├── Services/
│   │   ├── CreateAddressService.php
│   │   ├── DeleteAddressService.php
│   │   ├── GetAddressService.php
│   │   ├── ListAddressesService.php
│   │   └── UpdateAddressService.php
│   └── DTOs/
│       ├── CreateAddressInputDTO.php
│       └── UpdateAddressInputDTO.php
│
├── Product/
│   ├── Services/
│   │   ├── CreateProductService.php
│   │   ├── UpdateProductService.php
│   │   ├── DeleteProductService.php
│   │   ├── GetProductService.php
│   │   ├── ListProductsService.php
│   │   ├── CreateProductVariantService.php
│   │   ├── UpdateProductVariantService.php
│   │   ├── CreateProductOptionService.php
│   │   ├── AttachGalleryToProductService.php
│   │   ├── AttachProductToStoresService.php
│   │   └── ... (22 services au total)
│   ├── DTOs/
│   │   ├── CreateProductInputDTO.php
│   │   ├── UpdateProductInputDTO.php
│   │   └── ...
│   └── Constants/
│       └── ProductConstants.php
│
├── ProductCategory/
│   ├── Services/
│   │   ├── CreateProductCategoryService.php
│   │   ├── UpdateProductCategoryService.php
│   │   ├── DeleteProductCategoryService.php
│   │   ├── GetProductCategoryService.php
│   │   └── ListProductCategoriesService.php
│   └── DTOs/
│       └── ...
│
├── Store/
│   ├── Services/
│   │   ├── CreateStoreService.php
│   │   ├── UpdateStoreService.php
│   │   ├── DeleteStoreService.php
│   │   ├── GetStoreService.php
│   │   ├── ListStoresService.php
│   │   ├── AttachUsersToStoreService.php
│   │   └── DetachUserFromStoreService.php
│   └── DTOs/
│       └── ...
│
├── User/
│   ├── Services/
│   │   ├── CreateUserService.php
│   │   ├── UpdateUserService.php
│   │   ├── DeleteUserService.php
│   │   ├── GetUserService.php
│   │   ├── ListUsersService.php
│   │   └── RestoreUserService.php
│   └── DTOs/
│       └── ...
│
├── ApiResponse/
│   ├── Services/
│   │   ├── ApiResponseErrorService.php
│   │   ├── ApiResponseSuccessService.php
│   │   └── ApiResponseSuccessWithPaginationService.php
│   └── DTOs/
│       └── PagingDTO.php
│
├── Logger/
│   └── Services/
│       └── LoggerService.php
│
├── StoredFile/
│   └── Services/
│       ├── CreateStoredFileService.php
│       └── DeleteStoredFileService.php
│
└── UploadedFile/
    └── Services/
        └── ProcessUploadedFileService.php
```

### 2.3 Principe de Responsabilité Unique

Chaque service a **une seule responsabilité** :

| Service | Responsabilité |
|---------|----------------|
| `CreateProductService` | Créer un produit |
| `UpdateProductService` | Modifier un produit |
| `DeleteProductService` | Supprimer un produit |
| `GetProductService` | Récupérer un produit |
| `ListProductsService` | Lister les produits |
| `AttachGalleryToProductService` | Gérer les images |

---

## 3. Couche Domain (Services)

### 3.1 Structure d'un Service

```php
<?php

namespace App\Domain\Product\Services;

use App\Domain\Product\DTOs\CreateProductInputDTO;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * Service de création de produit.
 */
class CreateProductService
{
    /**
     * Crée un nouveau produit.
     *
     * @param CreateProductInputDTO $dto Les données du produit
     * @param User $creator L'utilisateur créateur
     * @return Product Le produit créé avec ses relations
     */
    public function execute(CreateProductInputDTO $dto, User $creator): Product
    {
        $product = Product::create([
            'name' => $dto->name,
            'slug' => $this->generateUniqueSlug($dto->name),
            'description' => $dto->description,
            'product_category_id' => $dto->productCategoryId,
            'created_by' => $creator->id,
            'is_active' => $dto->isActive,
            'is_featured' => $dto->isFeatured,
        ]);

        // Associer aux stores si spécifiés
        if (!empty($dto->storeIds)) {
            $product->stores()->attach($dto->storeIds);
        }

        return $product->load(['category', 'stores', 'creator', 'variants', 'gallery']);
    }

    /**
     * Génère un slug unique.
     */
    private function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (Product::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
```

### 3.2 Avantages des Services

```
┌─────────────────────────────────────────────────────────────────┐
│                    AVANTAGES DU PATTERN SERVICE                 │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│   ✅ Testabilité                                                │
│      • Services isolés = tests unitaires simples               │
│      • Mocking facile des dépendances                          │
│                                                                 │
│   ✅ Réutilisabilité                                            │
│      • Même service appelable depuis plusieurs controllers     │
│      • Utilisable dans Jobs, Commands, etc.                    │
│                                                                 │
│   ✅ Maintenabilité                                             │
│      • Logique métier centralisée                              │
│      • Modifications localisées                                │
│                                                                 │
│   ✅ Lisibilité                                                 │
│      • Controllers légers                                      │
│      • Intention claire du code                                │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 3.3 Liste des Services par Domain

| Domain | Services |
|--------|----------|
| **User** | Create, Update, Delete, Get, List, Restore |
| **Store** | Create, Update, Delete, Get, List, AttachUsers, DetachUser |
| **Product** | Create, Update, Delete, Get, List, AttachGallery, DetachGallery, AttachToStores, DetachFromStore |
| **ProductVariant** | Create, Update, Delete, AttachGallery, DetachGallery, UpdateStock, DeleteStock |
| **ProductOption** | Create, Update, Delete |
| **ProductOptionValue** | Create, Update, Delete |
| **ProductCategory** | Create, Update, Delete, Get, List |
| **Address** | Create, Update, Delete, Get, List |
| **StoredFile** | Create, Delete |
| **ApiResponse** | Error, Success, SuccessWithPagination |
| **Logger** | Log |

---

## 4. Couche HTTP (Controllers)

### 4.1 Structure d'un Controller

```php
<?php

namespace App\Http\Controllers;

use App\Domain\Product\Services\CreateProductService;
use App\Domain\Product\Services\ListProductsService;
use App\Domain\Product\DTOs\CreateProductInputDTO;
use App\Http\Requests\StoreProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(
        private CreateProductService $createService,
        private ListProductsService $listService,
    ) {}

    /**
     * Liste des produits.
     */
    public function index(): JsonResponse
    {
        $products = $this->listService->execute();

        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($products),
        ]);
    }

    /**
     * Création d'un produit.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $dto = CreateProductInputDTO::fromRequest($request);
        $product = $this->createService->execute($dto, $request->user());

        return response()->json([
            'success' => true,
            'data' => new ProductResource($product),
            'message' => 'Produit créé avec succès',
        ], 201);
    }

    /**
     * Affichage d'un produit.
     */
    public function show(Product $product): JsonResponse
    {
        $this->authorize('view', $product);

        return response()->json([
            'success' => true,
            'data' => new ProductResource($product->load(['category', 'variants', 'options'])),
        ]);
    }
}
```

### 4.2 Responsabilités du Controller

| Responsabilité | Description |
|----------------|-------------|
| **Validation** | Via Form Request |
| **Autorisation** | Via Policy (`authorize()`) |
| **Orchestration** | Appel du Service approprié |
| **Réponse** | Transformation via Resource |

### 4.3 Controllers Disponibles

| Controller | Endpoints |
|------------|-----------|
| `Auth\LoginController` | Login, Logout |
| `Auth\RegisterController` | Register |
| `UserController` | CRUD Users |
| `StoreController` | CRUD Stores + Attachments |
| `ProductController` | CRUD Products + Gallery |
| `ProductVariantController` | CRUD Variants + Stock |
| `ProductCategoryController` | CRUD Categories |
| `ProductOptionController` | CRUD Options |
| `ProductOptionValueController` | CRUD Option Values |
| `AddressController` | CRUD Addresses |
| `ProductStoreController` | Product-Store associations |

---

## 5. DTOs et Validation

### 5.1 Data Transfer Objects (DTOs)

Les DTOs encapsulent les données de requête de manière typée.

```php
<?php

namespace App\Domain\Product\DTOs;

use Illuminate\Http\Request;

readonly class CreateProductInputDTO
{
    public function __construct(
        public string $name,
        public ?string $description,
        public int $productCategoryId,
        public bool $isActive = true,
        public bool $isFeatured = false,
        public array $storeIds = [],
    ) {}

    /**
     * Créer un DTO depuis une Request validée.
     */
    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->validated('name'),
            description: $request->validated('description'),
            productCategoryId: $request->validated('product_category_id'),
            isActive: $request->validated('is_active', true),
            isFeatured: $request->validated('is_featured', false),
            storeIds: $request->validated('store_ids', []),
        );
    }
}
```

### 5.2 Form Requests (Validation)

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Product::class);
    }

    /**
     * Règles de validation.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'product_category_id' => ['required', 'exists:product_categories,id'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'store_ids' => ['array'],
            'store_ids.*' => ['exists:stores,id'],
        ];
    }

    /**
     * Messages d'erreur personnalisés.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Le nom du produit est obligatoire.',
            'product_category_id.exists' => 'La catégorie sélectionnée n\'existe pas.',
        ];
    }
}
```

### 5.3 Avantages des DTOs

| Avantage | Description |
|----------|-------------|
| **Typage fort** | Propriétés typées PHP 8.2+ |
| **Immutabilité** | `readonly` pour la sécurité |
| **Validation centralisée** | Via Form Request |
| **Documentation** | Auto-documentation du code |
| **IDE support** | Autocomplétion complète |

---

## 6. Resources (Transformation JSON)

### 6.1 API Resources

Les Resources transforment les modèles Eloquent en réponses JSON standardisées.

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transforme le produit en tableau.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),

            // Relations (chargées conditionnellement)
            'category' => new ProductCategoryResource($this->whenLoaded('category')),
            'variants' => ProductVariantResource::collection($this->whenLoaded('variants')),
            'options' => ProductOptionResource::collection($this->whenLoaded('options')),
            'stores' => StoreResource::collection($this->whenLoaded('stores')),
            'gallery' => StoredFileResource::collection($this->whenLoaded('gallery')),
            'creator' => new UserResource($this->whenLoaded('creator')),
        ];
    }
}
```

### 6.2 Liste des Resources

| Resource | Modèle | Usage |
|----------|--------|-------|
| `UserResource` | User | Utilisateurs |
| `StoreResource` | Store | Magasins |
| `ProductResource` | Product | Produits |
| `ProductVariantResource` | ProductVariant | Variantes |
| `ProductCategoryResource` | ProductCategory | Catégories |
| `ProductOptionResource` | ProductOption | Options |
| `ProductOptionValueResource` | ProductOptionValue | Valeurs d'options |
| `AddressResource` | Address | Adresses |
| `StoredFileResource` | StoredFile | Fichiers uploadés |
| `RoleResource` | Role (Spatie) | Rôles |
| `PermissionResource` | Permission (Spatie) | Permissions |

### 6.3 Format de Réponse Standardisé

```json
// Succès simple
{
  "success": true,
  "data": { ... },
  "message": "Opération réussie"
}

// Succès avec pagination
{
  "success": true,
  "data": [ ... ],
  "meta": {
    "current_page": 1,
    "last_page": 10,
    "per_page": 15,
    "total": 150
  }
}

// Erreur
{
  "success": false,
  "error": "Message d'erreur",
  "errors": {
    "field": ["Erreur de validation"]
  }
}
```

---

## 7. Policies (Autorisation)

### 7.1 Système de Policies

Les Policies définissent les règles d'autorisation pour chaque modèle.

```php
<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    /**
     * Vérifie si l'utilisateur peut voir la liste.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('products.view');
    }

    /**
     * Vérifie si l'utilisateur peut voir un produit.
     */
    public function view(User $user, Product $product): bool
    {
        return $user->hasPermissionTo('products.view');
    }

    /**
     * Vérifie si l'utilisateur peut créer un produit.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('products.create');
    }

    /**
     * Vérifie si l'utilisateur peut modifier un produit.
     */
    public function update(User $user, Product $product): bool
    {
        return $user->hasPermissionTo('products.update');
    }

    /**
     * Vérifie si l'utilisateur peut supprimer un produit.
     */
    public function delete(User $user, Product $product): bool
    {
        return $user->hasPermissionTo('products.delete');
    }
}
```

### 7.2 Policies Disponibles

| Policy | Modèle | Permissions |
|--------|--------|-------------|
| `UserPolicy` | User | view, create, update, delete, restore |
| `StorePolicy` | Store | view, create, update, delete, attachUsers |
| `ProductPolicy` | Product | view, create, update, delete, attachGallery |
| `ProductVariantPolicy` | ProductVariant | view, create, update, delete |
| `ProductCategoryPolicy` | ProductCategory | view, create, update, delete |
| `ProductOptionPolicy` | ProductOption | view, create, update, delete |
| `ProductOptionValuePolicy` | ProductOptionValue | view, create, update, delete |

### 7.3 Utilisation dans les Controllers

```php
// Autorisation automatique via Form Request
public function authorize(): bool
{
    return $this->user()->can('create', Product::class);
}

// Autorisation manuelle dans le controller
public function show(Product $product)
{
    $this->authorize('view', $product);
    // ...
}

// Autorisation avec Gate
if (Gate::denies('delete', $product)) {
    abort(403, 'Action non autorisée');
}
```

---

## 8. Gestion des Erreurs

### 8.1 Gestion Centralisée

```php
<?php

namespace App\Domain\ApiResponse\Services;

use Illuminate\Http\JsonResponse;

class ApiResponseErrorService
{
    /**
     * Retourne une réponse d'erreur standardisée.
     */
    public function execute(
        string $message,
        int $statusCode = 400,
        ?array $errors = null
    ): JsonResponse {
        $response = [
            'success' => false,
            'error' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }
}
```

### 8.2 Types d'Erreurs

| Code HTTP | Type | Exemple |
|-----------|------|---------|
| **400** | Bad Request | Données invalides |
| **401** | Unauthorized | Token manquant/invalide |
| **403** | Forbidden | Action non autorisée |
| **404** | Not Found | Ressource inexistante |
| **422** | Unprocessable | Erreurs de validation |
| **500** | Server Error | Erreur interne |

### 8.3 Handler d'Exceptions

```php
// bootstrap/app.php ou app/Exceptions/Handler.php
$exceptions->render(function (ValidationException $e) {
    return response()->json([
        'success' => false,
        'error' => 'Validation échouée',
        'errors' => $e->errors(),
    ], 422);
});

$exceptions->render(function (ModelNotFoundException $e) {
    return response()->json([
        'success' => false,
        'error' => 'Ressource non trouvée',
    ], 404);
});

$exceptions->render(function (AuthorizationException $e) {
    return response()->json([
        'success' => false,
        'error' => 'Action non autorisée',
    ], 403);
});
```

---

## 9. Logging et Journalisation

### 9.1 Service de Logging

```php
<?php

namespace App\Domain\Logger\Services;

use Illuminate\Support\Facades\Log;

class LoggerService
{
    /**
     * Log une erreur avec contexte.
     */
    public function error(string $message, array $context = []): void
    {
        Log::error($message, array_merge($context, [
            'timestamp' => now()->toISOString(),
            'user_id' => auth()->id(),
        ]));
    }

    /**
     * Log une information.
     */
    public function info(string $message, array $context = []): void
    {
        Log::info($message, $context);
    }

    /**
     * Log un avertissement.
     */
    public function warning(string $message, array $context = []): void
    {
        Log::warning($message, $context);
    }
}
```

### 9.2 Niveaux de Log

| Niveau | Usage |
|--------|-------|
| **debug** | Développement uniquement |
| **info** | Actions normales (login, création) |
| **warning** | Situations anormales non critiques |
| **error** | Erreurs nécessitant attention |
| **critical** | Erreurs système graves |

### 9.3 Configuration des Canaux

```php
// config/logging.php
'channels' => [
    'stack' => [
        'driver' => 'stack',
        'channels' => ['daily', 'slack'],
    ],
    'daily' => [
        'driver' => 'daily',
        'path' => storage_path('logs/laravel.log'),
        'days' => 14,
    ],
    'slack' => [
        'driver' => 'slack',
        'url' => env('LOG_SLACK_WEBHOOK_URL'),
        'level' => 'error',
    ],
],
```

---

## 10. Scalabilité

### 10.1 Optimisations Implémentées

```
┌─────────────────────────────────────────────────────────────────┐
│                    OPTIMISATIONS BACKEND                        │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│   REQUÊTES                                                      │
│   ─────────────────────────────────────────────────────────    │
│   ✅ Eager loading (with()) pour éviter N+1                    │
│   ✅ Pagination côté serveur                                   │
│   ✅ Sélection des colonnes nécessaires                        │
│   ✅ Index sur les colonnes fréquemment filtrées               │
│                                                                 │
│   CACHE                                                         │
│   ─────────────────────────────────────────────────────────    │
│   ✅ Cache de configuration (`php artisan config:cache`)       │
│   ✅ Cache de routes (`php artisan route:cache`)               │
│   ✅ Cache des vues (`php artisan view:cache`)                 │
│                                                                 │
│   API                                                           │
│   ─────────────────────────────────────────────────────────    │
│   ✅ Rate limiting par endpoint                                │
│   ✅ Compression Gzip des réponses                             │
│   ✅ Headers de cache HTTP                                     │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 10.2 Bonnes Pratiques Eloquent

```php
// ❌ N+1 Query Problem
$products = Product::all();
foreach ($products as $product) {
    echo $product->category->name;  // Requête supplémentaire par produit
}

// ✅ Eager Loading
$products = Product::with('category')->get();
foreach ($products as $product) {
    echo $product->category->name;  // Pas de requête supplémentaire
}

// ✅ Pagination
$products = Product::with(['category', 'variants'])
    ->paginate(15);

// ✅ Sélection des colonnes
$products = Product::select(['id', 'name', 'price'])
    ->where('is_active', true)
    ->get();
```

### 10.3 Architecture Scalable

```
┌─────────────────────────────────────────────────────────────────┐
│                    SCALABILITÉ HORIZONTALE                      │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│   Load Balancer                                                 │
│        │                                                        │
│   ┌────┴────┬────────────┐                                     │
│   │         │            │                                      │
│   ▼         ▼            ▼                                      │
│ Laravel   Laravel     Laravel                                   │
│ Node 1    Node 2      Node N                                    │
│   │         │            │                                      │
│   └────┬────┴────────────┘                                     │
│        │                                                        │
│        ▼                                                        │
│   ┌─────────────────┐                                          │
│   │  MySQL Primary  │───► MySQL Replicas                       │
│   └─────────────────┘                                          │
│                                                                 │
│   ┌─────────────────┐                                          │
│   │     Redis       │  (Sessions, Cache, Queues)               │
│   └─────────────────┘                                          │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

---

## Ressources

- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Sanctum](https://laravel.com/docs/sanctum)
- [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission)
- [PestPHP](https://pestphp.com/)

---
