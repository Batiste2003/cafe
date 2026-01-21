# Architecture MVC - API Café

## Vue d'ensemble

L'API suit une architecture **MVC (Model-View-Controller)** enrichie d'une **couche Domain** pour organiser le code métier par entité.

```
┌─────────────────────────────────────────────────────────────────┐
│                         Routes                                   │
│                    (routes/api.php)                             │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                       Controllers                                │
│                  (app/Http/Controllers)                         │
│         Réceptionne les requêtes, appelle les services          │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                         Domain                                   │
│                      (app/Domain)                               │
│   Services, Enumerations, Constants, DTOs organisés par Model   │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                         Models                                   │
│                      (app/Models)                               │
│            Représentation des données (Eloquent)                │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                        Database                                  │
│                   (database/migrations)                         │
└─────────────────────────────────────────────────────────────────┘
```

---

## Structure des dossiers

```
API/
├── app/
│   ├── Domain/                       # Logique métier par entité
│   │   ├── User/
│   │   │   ├── Services/
│   │   │   ├── Enumeration/
│   │   │   ├── Constant/
│   │   │   └── DTOs/
│   │   ├── Product/
│   │   │   ├── Services/
│   │   │   ├── Enumeration/
│   │   │   ├── Constant/
│   │   │   └── DTOs/
│   │   ├── ApiResponse/              # Réponses API standardisées
│   │   │   ├── Services/
│   │   │   └── DTOs/
│   │   └── Logger/                   # Service de logging
│   │       ├── Services/
│   │       └── Enumeration/
│   ├── Http/
│   │   ├── Controllers/              # Contrôleurs
│   │   ├── Middleware/               # Middlewares
│   │   ├── Requests/                 # Form Requests (validation)
│   │   └── Resources/                # API Resources (transformation)
│   ├── Models/                       # Modèles Eloquent
│   └── Providers/                    # Service Providers
├── config/                           # Configuration
├── database/
│   ├── migrations/                   # Migrations
│   ├── factories/                    # Factories (tests)
│   └── seeders/                      # Seeders
├── routes/
│   ├── api.php                       # Routes API
│   ├── web.php                       # Routes Web
│   └── auth.php                      # Routes d'authentification
└── tests/                            # Tests
```

---

## Domain

Le dossier `Domain` centralise toute la logique métier, organisée par **entité/modèle**.

### Structure par entité

Chaque entité possède ses propres sous-dossiers :

```
app/Domain/
├── User/
│   ├── Services/                 # Services métier
│   │   ├── CreateUserService.php
│   │   ├── UpdateUserService.php
│   │   ├── DeleteUserService.php
│   │   └── GetUserService.php
│   ├── Enumeration/              # Enums PHP
│   │   ├── UserRoleEnum.php
│   │   └── UserStatusEnum.php
│   ├── Constant/                 # Constantes
│   │   └── UserConstant.php
│   └── DTOs/                     # Data Transfer Objects
│       ├── CreateUserInputDTO.php
│       └── UserOutputDTO.php
├── Product/
│   ├── Services/
│   ├── Enumeration/
│   ├── Constant/
│   └── DTOs/
├── ApiResponse/                  # Domaine transversal
│   ├── Services/
│   │   ├── ApiResponseSuccessService.php
│   │   ├── ApiResponseSuccessWithPaginationService.php
│   │   └── ApiResponseErrorService.php
│   └── DTOs/
│       ├── ApiSuccessResponseDTO.php
│       ├── ApiErrorResponseDTO.php
│       ├── ApiSuccessWithPagingResponseDTO.php
│       └── PagingDTO.php
└── Logger/                       # Domaine transversal
    ├── Services/
    │   └── LoggerService.php
    └── Enumeration/
        ├── LogLevelEnum.php
        └── ErrorCodeEnum.php
```

---

## Services

### Responsabilités

Les services contiennent la **logique métier**. Un service = une action spécifique.

### Convention de nommage

- Emplacement : `app/Domain/{Model}/Services/`
- Nommage : `{Action}{Model}Service.php`
- Namespace : `App\Domain\{Model}\Services`

### Exemple de Service

```php
<?php

namespace App\Domain\User\Services;

use App\Models\User;
use App\Domain\User\Enumeration\UserRoleEnum;

class CreateUserService
{
    public function execute(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'role' => $data['role'] ?? UserRoleEnum::CUSTOMER->value,
        ]);
    }
}
```

---

## DTOs (Data Transfer Objects)

### Responsabilités

Les DTOs structurent les données en entrée et en sortie des services.

### Convention de nommage

- Emplacement : `app/Domain/{Model}/DTOs/`
- Nommage Input : `{Action}{Model}InputDTO.php`
- Nommage Output : `{Model}OutputDTO.php`
- Namespace : `App\Domain\{Model}\DTOs`

### Exemple de DTO Input

```php
<?php

namespace App\Domain\User\DTOs;

readonly class CreateUserInputDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public ?string $role = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            password: $data['password'],
            role: $data['role'] ?? null
        );
    }
}
```

### Exemple de DTO Output

```php
<?php

namespace App\Domain\User\DTOs;

use App\Models\User;

readonly class UserOutputDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public string $role,
        public ?string $createdAt
    ) {}

    public static function fromModel(User $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            role: $user->role->value,
            createdAt: $user->created_at?->format('Y-m-d H:i:s')
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'created_at' => $this->createdAt,
        ];
    }
}
```

---

## API Response

### Responsabilités

Les services ApiResponse standardisent toutes les réponses JSON de l'API.

### Structure

```
app/Domain/ApiResponse/
├── Services/
│   ├── ApiResponseSuccessService.php
│   ├── ApiResponseSuccessWithPaginationService.php
│   └── ApiResponseErrorService.php
└── DTOs/
    ├── ApiSuccessResponseDTO.php
    ├── ApiErrorResponseDTO.php
    ├── ApiSuccessWithPagingResponseDTO.php
    └── PagingDTO.php
```

### Format des réponses

**Réponse succès :**
```json
{
    "success": true,
    "message": "Utilisateur créé avec succès",
    "data": { ... }
}
```

**Réponse succès avec pagination :**
```json
{
    "success": true,
    "message": "Liste des produits",
    "data": [ ... ],
    "paging": {
        "current_page": 1,
        "per_page": 20,
        "total": 150,
        "last_page": 8
    }
}
```

**Réponse erreur :**
```json
{
    "success": false,
    "message": "Identifiants invalides",
    "error_code": "AUTH_INVALID_CREDENTIALS"
}
```

### Utilisation dans un Controller

```php
<?php

namespace App\Http\Controllers;

use App\Domain\ApiResponse\Services\ApiResponseSuccessService;
use App\Domain\ApiResponse\Services\ApiResponseSuccessWithPaginationService;
use App\Domain\ApiResponse\Services\ApiResponseErrorService;
use App\Domain\ApiResponse\DTOs\PagingDTO;
use App\Domain\User\Services\CreateUserService;
use App\Domain\User\Services\ListUsersService;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function __construct(
        private readonly ApiResponseSuccessService $successResponse,
        private readonly ApiResponseSuccessWithPaginationService $paginatedResponse,
        private readonly ApiResponseErrorService $errorResponse
    ) {}

    public function index(ListUsersService $service): JsonResponse
    {
        $paginator = $service->execute();

        return $this->paginatedResponse->execute(
            message: 'Liste des utilisateurs',
            data: UserResource::collection($paginator->items()),
            paging: PagingDTO::fromPaginator($paginator)
        );
    }

    public function store(
        StoreUserRequest $request,
        CreateUserService $service
    ): JsonResponse {
        $user = $service->execute($request->validated());

        return $this->successResponse->execute(
            message: 'Utilisateur créé avec succès',
            data: new UserResource($user),
            statusCode: 201
        );
    }
}
```

---

## Logger

### Responsabilités

Le service Logger centralise la gestion des logs avec des niveaux et codes d'erreur standardisés.

### Structure

```
app/Domain/Logger/
├── Services/
│   └── LoggerService.php
└── Enumeration/
    ├── LogLevelEnum.php
    └── ErrorCodeEnum.php
```

### LogLevelEnum

```php
<?php

namespace App\Domain\Logger\Enumeration;

enum LogLevelEnum: string
{
    case DEBUG = 'debug';
    case INFO = 'info';
    case NOTICE = 'notice';
    case WARNING = 'warning';
    case ERROR = 'error';
    case CRITICAL = 'critical';
    case ALERT = 'alert';
    case EMERGENCY = 'emergency';

    public function label(): string
    {
        return match ($this) {
            self::DEBUG => 'Debug',
            self::INFO => 'Information',
            self::NOTICE => 'Notice',
            self::WARNING => 'Avertissement',
            self::ERROR => 'Erreur',
            self::CRITICAL => 'Critique',
            self::ALERT => 'Alerte',
            self::EMERGENCY => 'Urgence',
        };
    }

    public function isHighPriority(): bool
    {
        return in_array($this, [
            self::ERROR,
            self::CRITICAL,
            self::ALERT,
            self::EMERGENCY,
        ]);
    }
}
```

### ErrorCodeEnum

```php
<?php

namespace App\Domain\Logger\Enumeration;

enum ErrorCodeEnum: string
{
    // Erreurs génériques
    case GENERIC_ERROR = 'GENERIC_ERROR';
    case VALIDATION_ERROR = 'VALIDATION_ERROR';
    case UNAUTHORIZED = 'UNAUTHORIZED';
    case FORBIDDEN = 'FORBIDDEN';
    case NOT_FOUND = 'NOT_FOUND';

    // Erreurs d'authentification
    case AUTH_INVALID_CREDENTIALS = 'AUTH_INVALID_CREDENTIALS';
    case AUTH_TOKEN_EXPIRED = 'AUTH_TOKEN_EXPIRED';
    case AUTH_TOKEN_INVALID = 'AUTH_TOKEN_INVALID';
    case AUTH_EMAIL_NOT_VERIFIED = 'AUTH_EMAIL_NOT_VERIFIED';

    // Erreurs de base de données
    case DATABASE_ERROR = 'DATABASE_ERROR';
    case DATABASE_CONNECTION_ERROR = 'DATABASE_CONNECTION_ERROR';
    case DATABASE_QUERY_ERROR = 'DATABASE_QUERY_ERROR';

    // Erreurs métier
    case BUSINESS_RULE_VIOLATION = 'BUSINESS_RULE_VIOLATION';
    case RESOURCE_ALREADY_EXISTS = 'RESOURCE_ALREADY_EXISTS';
    case RESOURCE_NOT_AVAILABLE = 'RESOURCE_NOT_AVAILABLE';

    // Erreurs externes
    case EXTERNAL_SERVICE_ERROR = 'EXTERNAL_SERVICE_ERROR';
    case EXTERNAL_SERVICE_TIMEOUT = 'EXTERNAL_SERVICE_TIMEOUT';

    public function message(): string
    {
        return match ($this) {
            self::GENERIC_ERROR => 'Une erreur est survenue.',
            self::VALIDATION_ERROR => 'Les données fournies sont invalides.',
            // ...
        };
    }

    public function httpStatusCode(): int
    {
        return match ($this) {
            self::GENERIC_ERROR => 500,
            self::VALIDATION_ERROR => 422,
            self::UNAUTHORIZED => 401,
            self::FORBIDDEN => 403,
            self::NOT_FOUND => 404,
            // ...
        };
    }
}
```

### Utilisation du Logger

```php
<?php

use App\Domain\Logger\Services\LoggerService;
use App\Domain\Logger\Enumeration\ErrorCodeEnum;

class SomeService
{
    public function __construct(
        private readonly LoggerService $logger
    ) {}

    public function execute(): void
    {
        $this->logger->info('Opération démarrée');

        try {
            // Logique métier
        } catch (\Exception $e) {
            $this->logger->error(
                message: $e->getMessage(),
                errorCode: ErrorCodeEnum::DATABASE_ERROR->value,
                context: ['exception' => $e]
            );
        }
    }
}
```

---

## API Resources

### Responsabilités

Les Resources transforment les modèles Eloquent en réponses JSON structurées.

### Emplacement

```
app/Http/Resources/
├── UserResource.php
├── ProductResource.php
├── OrderResource.php
└── RoleResource.php
```

### Convention de nommage

- Nommage : `{Model}Resource.php`
- Namespace : `App\Http\Resources`

### Exemple de Resource

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role?->value,
            'email_verified_at' => $this->email_verified_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
```

### Resource avec relations

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'status' => $this->status?->value,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
```

### Resource avec collection imbriquée

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'guard_name' => $this->guard_name,
            'permissions' => PermissionResource::collection($this->whenLoaded('permissions')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
```

### Utilisation dans un Controller

```php
<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;

class UserController extends Controller
{
    public function show(User $user): JsonResponse
    {
        return $this->successResponse->execute(
            message: 'Détail de l\'utilisateur',
            data: new UserResource($user)
        );
    }

    public function index(): JsonResponse
    {
        $users = User::with('roles')->paginate(20);

        return $this->paginatedResponse->execute(
            message: 'Liste des utilisateurs',
            data: UserResource::collection($users->items()),
            paging: PagingDTO::fromPaginator($users)
        );
    }
}
```

---

## Enumeration

### Responsabilités

Les enums PHP 8.1+ définissent des **valeurs fixes et typées** pour un domaine.

### Convention de nommage

- Emplacement : `app/Domain/{Model}/Enumeration/`
- Nommage : `{Model}{Type}Enum.php`
- Namespace : `App\Domain\{Model}\Enumeration`

### Exemple d'Enum

```php
<?php

namespace App\Domain\User\Enumeration;

enum UserRoleEnum: string
{
    case ADMIN = 'admin';
    case MANAGER = 'manager';
    case CUSTOMER = 'customer';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrateur',
            self::MANAGER => 'Manager',
            self::CUSTOMER => 'Client',
        };
    }

    public function isAdmin(): bool
    {
        return $this === self::ADMIN;
    }
}
```

### Utilisation dans un Model

```php
<?php

namespace App\Models;

use App\Domain\User\Enumeration\UserRoleEnum;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $casts = [
        'role' => UserRoleEnum::class,
    ];
}
```

---

## Constant

### Responsabilités

Les constantes définissent des **valeurs fixes réutilisables** (limites, clés de configuration, messages...).

### Convention de nommage

- Emplacement : `app/Domain/{Model}/Constant/`
- Nommage : `{Model}Constant.php`
- Namespace : `App\Domain\{Model}\Constant`

### Exemple de Constant

```php
<?php

namespace App\Domain\User\Constant;

final class UserConstant
{
    public const MAX_LOGIN_ATTEMPTS = 5;
    public const LOCKOUT_DURATION = 15;
    public const PASSWORD_MIN_LENGTH = 8;
    public const PASSWORD_RESET_TOKEN_EXPIRY = 60;
}
```

---

## Controllers

### Responsabilités

Les contrôleurs doivent rester **légers** et se limiter à :

1. Recevoir la requête HTTP
2. Valider les données (via Form Request)
3. Appeler le(s) service(s) approprié(s)
4. Retourner la réponse via ApiResponse + Resource

### Convention de nommage

| Action | Méthode HTTP | Méthode Controller |
|--------|--------------|-------------------|
| Liste  | GET          | `index()`         |
| Détail | GET          | `show()`          |
| Créer  | POST         | `store()`         |
| Modifier | PUT/PATCH  | `update()`        |
| Supprimer | DELETE    | `destroy()`       |

### Exemple complet de Controller

```php
<?php

namespace App\Http\Controllers;

use App\Domain\ApiResponse\Services\ApiResponseSuccessService;
use App\Domain\ApiResponse\Services\ApiResponseSuccessWithPaginationService;
use App\Domain\ApiResponse\DTOs\PagingDTO;
use App\Domain\Product\Services\CreateProductService;
use App\Domain\Product\Services\UpdateProductService;
use App\Domain\Product\Services\DeleteProductService;
use App\Domain\Product\Services\ListProductsService;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(
        private readonly ApiResponseSuccessService $successResponse,
        private readonly ApiResponseSuccessWithPaginationService $paginatedResponse
    ) {}

    public function index(ListProductsService $service): JsonResponse
    {
        $paginator = $service->execute();

        return $this->paginatedResponse->execute(
            message: 'Liste des produits',
            data: ProductResource::collection($paginator->items()),
            paging: PagingDTO::fromPaginator($paginator)
        );
    }

    public function store(
        StoreProductRequest $request,
        CreateProductService $service
    ): JsonResponse {
        $product = $service->execute($request->validated());

        return $this->successResponse->execute(
            message: 'Produit créé avec succès',
            data: new ProductResource($product),
            statusCode: 201
        );
    }

    public function show(Product $product): JsonResponse
    {
        return $this->successResponse->execute(
            message: 'Détail du produit',
            data: new ProductResource($product->load('category'))
        );
    }

    public function update(
        UpdateProductRequest $request,
        Product $product,
        UpdateProductService $service
    ): JsonResponse {
        $product = $service->execute($product, $request->validated());

        return $this->successResponse->execute(
            message: 'Produit mis à jour',
            data: new ProductResource($product)
        );
    }

    public function destroy(
        Product $product,
        DeleteProductService $service
    ): JsonResponse {
        $service->execute($product);

        return $this->successResponse->execute(
            message: 'Produit supprimé',
            statusCode: 200
        );
    }
}
```

---

## Models

### Responsabilités

Les modèles Eloquent gèrent :

- La définition des attributs (`$fillable`, `$hidden`, `$casts`)
- Les relations entre modèles
- Les scopes de requêtes
- Les accessors/mutators

### Exemple de Model

```php
<?php

namespace App\Models;

use App\Domain\Product\Enumeration\ProductStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'status',
        'category_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'status' => ProductStatusEnum::class,
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
```

---

## Form Requests

Les Form Requests centralisent la **validation** des données entrantes.

### Emplacement

```
app/Http/Requests/
├── Auth/
│   └── LoginRequest.php
├── StoreUserRequest.php
├── UpdateUserRequest.php
├── StoreProductRequest.php
└── UpdateProductRequest.php
```

### Exemple

```php
<?php

namespace App\Http\Requests;

use App\Domain\Product\Constant\ProductConstant;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:' . ProductConstant::NAME_MAX_LENGTH],
            'description' => ['nullable', 'string'],
            'price' => [
                'required',
                'numeric',
                'min:' . ProductConstant::MIN_PRICE,
                'max:' . ProductConstant::MAX_PRICE,
            ],
            'category_id' => ['required', 'exists:categories,id'],
        ];
    }
}
```

---

## Routes API

### Structure

```php
// routes/api.php

use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('orders', OrderController::class);
});
```

### Conventions d'URL

| Route | Méthode | Action |
|-------|---------|--------|
| `/api/products` | GET | Liste des produits |
| `/api/products` | POST | Créer un produit |
| `/api/products/{id}` | GET | Détail d'un produit |
| `/api/products/{id}` | PUT/PATCH | Modifier un produit |
| `/api/products/{id}` | DELETE | Supprimer un produit |

---

## Flux de données

```
Requête HTTP
     │
     ▼
┌─────────────┐
│   Route     │  → Définit le controller et la méthode
└─────────────┘
     │
     ▼
┌─────────────┐
│ Middleware  │  → Auth, validation headers, etc.
└─────────────┘
     │
     ▼
┌─────────────┐
│FormRequest  │  → Validation des données
└─────────────┘
     │
     ▼
┌─────────────┐
│ Controller  │  → Orchestre l'appel aux services
└─────────────┘
     │
     ▼
┌─────────────────────────────────────┐
│              Domain                  │
│  ┌───────────┐ ┌────────────────┐   │
│  │  Service  │ │  Enum/Constant │   │
│  │           │ │      DTO       │   │
│  └───────────┘ └────────────────┘   │
└─────────────────────────────────────┘
     │
     ▼
┌─────────────┐
│   Model     │  → Interagit avec la base de données
└─────────────┘
     │
     ▼
┌─────────────┐
│  Database   │
└─────────────┘
     │
     ▼
┌─────────────┐
│  Resource   │  → Transforme le Model en JSON
└─────────────┘
     │
     ▼
┌─────────────┐
│ ApiResponse │  → Standardise la réponse
└─────────────┘
     │
     ▼
Réponse JSON
```

---

## Récapitulatif des namespaces

| Type | Emplacement | Namespace |
|------|-------------|-----------|
| Controller | `app/Http/Controllers/` | `App\Http\Controllers` |
| Service | `app/Domain/{Model}/Services/` | `App\Domain\{Model}\Services` |
| Enumeration | `app/Domain/{Model}/Enumeration/` | `App\Domain\{Model}\Enumeration` |
| Constant | `app/Domain/{Model}/Constant/` | `App\Domain\{Model}\Constant` |
| DTO | `app/Domain/{Model}/DTOs/` | `App\Domain\{Model}\DTOs` |
| Model | `app/Models/` | `App\Models` |
| Form Request | `app/Http/Requests/` | `App\Http\Requests` |
| Resource | `app/Http/Resources/` | `App\Http\Resources` |
| ApiResponse | `app/Domain/ApiResponse/` | `App\Domain\ApiResponse` |
| Logger | `app/Domain/Logger/` | `App\Domain\Logger` |

---

## Bonnes pratiques

### Services

- Un service = une action spécifique
- Méthode principale : `execute()`
- Injection de dépendances via le constructeur
- Retourner des types explicites

### DTOs

- Classes `readonly` pour l'immutabilité
- Méthodes `fromArray()` et `fromModel()` pour la création
- Méthode `toArray()` pour la sérialisation

### Enumerations

- Utiliser des enums PHP 8.1+ avec valeur (`: string` ou `: int`)
- Ajouter des méthodes utilitaires (`label()`, `isX()`, etc.)
- Caster dans les models pour un typage automatique

### Constants

- Classe `final` avec uniquement des `const`
- Nommer en SCREAMING_SNAKE_CASE
- Grouper par thématique logique

### Controllers

- Rester léger (pas de logique métier)
- Utiliser les Form Requests pour la validation
- Injecter ApiResponse via le constructeur
- Utiliser les Resources pour transformer les données
- Retourner des réponses JSON via ApiResponse
- **Documenter chaque méthode avec PHPDoc** (voir section PHPDoc)

### Resources

- Une Resource par Model
- Utiliser `whenLoaded()` pour les relations optionnelles
- Formater les dates en `Y-m-d H:i:s`
- Utiliser `->value` pour les enums

### Models

- Définir explicitement `$fillable` (jamais `$guarded = []`)
- Typer les relations
- Utiliser les casts pour les types complexes et les enums

### Général

- Respecter PSR-12 pour le code style
- Nommer en anglais
- Documenter les méthodes avec PHPDoc

### Montants et prix

**Convention obligatoire** : Tous les montants (prix, coûts, suppléments) doivent être exprimés en **centimes HT** (integer).

| Ce qu'on veut | Ce qu'on envoie | Type |
|---------------|-----------------|------|
| 3,50 € HT | `350` | integer |
| 12,99 € HT | `1299` | integer |
| 0,50 € HT | `50` | integer |

**Pourquoi ?**
- Évite les erreurs d'arrondi avec les nombres décimaux
- Stockage en base de données en `unsigned integer`
- Cohérence sur toute l'API

**Champs concernés** :
- `price_cent_ht` : Prix de vente HT (en centimes)
- `cost_price_cent_ht` : Prix de revient HT (en centimes)
- `price_add_cent_ht` : Supplément de prix HT (options, en centimes)

**Exemple de requête** :
```json
{
  "sku": "CAP-L",
  "price_cent_ht": 450,
  "cost_price_cent_ht": 150
}
```

**Conversion dans les modèles** :
Les modèles fournissent des accesseurs pour obtenir le prix en euros :
```php
$variant->price_euros;      // "4.50"
$variant->cost_price_euros; // "1.50"
```

---

## PHPDoc

### Règles obligatoires

Toutes les méthodes publiques des Controllers et Services **doivent** être documentées avec PHPDoc.

### Format standard

```php
/**
 * Description courte de ce que fait la méthode.
 *
 * Description détaillée optionnelle si nécessaire.
 *
 * @param  Type  $paramName  Description du paramètre
 * @return Type Description de ce qui est retourné
 */
```

### Exemple pour un Controller

```php
/**
 * Contrôleur pour la gestion des utilisateurs.
 *
 * Gère les opérations CRUD sur les utilisateurs ainsi que
 * la restauration des utilisateurs supprimés (soft delete).
 */
class UserController extends Controller
{
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
        // ...
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
        // ...
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
        // ...
    }
}
```

### Exemple pour un Service

```php
/**
 * Service de création d'utilisateur.
 */
class CreateUserService
{
    /**
     * Crée un nouvel utilisateur avec le rôle spécifié.
     *
     * @param  CreateUserInputDTO  $dto  Les données de l'utilisateur à créer
     * @return User L'utilisateur créé
     *
     * @throws \InvalidArgumentException Si le rôle n'est pas autorisé
     */
    public function execute(CreateUserInputDTO $dto): User
    {
        // ...
    }
}
```

### Bonnes pratiques PHPDoc

- **Toujours documenter** les méthodes publiques des Controllers et Services
- Description courte sur la première ligne
- Ligne vide avant les `@param` et `@return`
- Utiliser `@throws` pour documenter les exceptions
- Mentionner les règles métier importantes dans la description
- Documenter les classes avec une description de leur responsabilité
