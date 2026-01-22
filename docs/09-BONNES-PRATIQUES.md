# 09 - Bonnes Pratiques

> Principes SOLID, patterns de conception et qualité de code dans Smart Cafe

---

## Table des matières

1. [Principes SOLID](#1-principes-solid)
2. [DRY - Don't Repeat Yourself](#2-dry---dont-repeat-yourself)
3. [KISS - Keep It Simple, Stupid](#3-kiss---keep-it-simple-stupid)
4. [Design Patterns Utilisés](#4-design-patterns-utilisés)
5. [Clean Code](#5-clean-code)
6. [Conventions de Nommage](#6-conventions-de-nommage)
7. [Stratégie de Tests](#7-stratégie-de-tests)
8. [Documentation du Code](#8-documentation-du-code)

---

## 1. Principes SOLID

### 1.1 Vue d'Ensemble

```
┌─────────────────────────────────────────────────────────────────┐
│                    PRINCIPES SOLID                              │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│   S - Single Responsibility                                     │
│       Une classe = une responsabilité                          │
│                                                                 │
│   O - Open/Closed                                               │
│       Ouvert à l'extension, fermé à la modification            │
│                                                                 │
│   L - Liskov Substitution                                       │
│       Sous-types substituables à leurs types parents           │
│                                                                 │
│   I - Interface Segregation                                     │
│       Interfaces spécifiques plutôt que génériques             │
│                                                                 │
│   D - Dependency Inversion                                      │
│       Dépendre des abstractions, pas des concrétions           │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 1.2 Single Responsibility Principle (SRP)

Chaque classe a **une seule raison de changer**.

```php
// ❌ MAUVAIS - Controller fait tout
class ProductController extends Controller
{
    public function store(Request $request)
    {
        // Validation
        $validated = $request->validate([...]);

        // Création du slug
        $slug = Str::slug($validated['name']);

        // Vérification d'unicité du slug
        while (Product::where('slug', $slug)->exists()) {
            $slug .= '-' . random_int(1, 100);
        }

        // Création du produit
        $product = Product::create([...]);

        // Upload des images
        foreach ($request->file('images') as $image) {
            $path = $image->store('products');
            // ...
        }

        // Envoi d'email
        Mail::to($admin)->send(new NewProductNotification($product));

        return response()->json($product);
    }
}

// ✅ BON - Responsabilités séparées
class ProductController extends Controller
{
    public function __construct(
        private CreateProductService $createService,
    ) {}

    public function store(StoreProductRequest $request): JsonResponse
    {
        $dto = CreateProductInputDTO::fromRequest($request);
        $product = $this->createService->execute($dto, $request->user());

        return response()->json([
            'success' => true,
            'data' => new ProductResource($product),
        ], 201);
    }
}

// Services dédiés
class CreateProductService { /* création uniquement */ }
class ProcessUploadedFileService { /* upload uniquement */ }
class NotificationService { /* notifications uniquement */ }
```

**Application dans Smart Cafe :**

| Classe | Responsabilité Unique |
|--------|----------------------|
| `CreateProductService` | Créer un produit |
| `UpdateProductService` | Mettre à jour un produit |
| `AttachGalleryToProductService` | Gérer les images |
| `ProductController` | Orchestrer les requêtes HTTP |
| `ProductResource` | Transformer en JSON |
| `ProductPolicy` | Vérifier les autorisations |

### 1.3 Open/Closed Principle (OCP)

Les classes sont **ouvertes à l'extension, fermées à la modification**.

```php
// ❌ MAUVAIS - Modification nécessaire pour chaque nouveau type
class PaymentProcessor
{
    public function process(string $type, float $amount)
    {
        if ($type === 'stripe') {
            // Code Stripe
        } elseif ($type === 'paypal') {
            // Code PayPal
        } elseif ($type === 'bitcoin') {
            // Nouveau type = modification
        }
    }
}

// ✅ BON - Extension sans modification
interface PaymentGateway
{
    public function process(float $amount): PaymentResult;
}

class StripeGateway implements PaymentGateway
{
    public function process(float $amount): PaymentResult { /* ... */ }
}

class PayPalGateway implements PaymentGateway
{
    public function process(float $amount): PaymentResult { /* ... */ }
}

// Nouveau gateway = nouvelle classe, pas de modification
class BitcoinGateway implements PaymentGateway
{
    public function process(float $amount): PaymentResult { /* ... */ }
}
```

### 1.4 Liskov Substitution Principle (LSP)

Les sous-types doivent être **substituables** à leurs types parents.

```php
// ✅ BON - Resources substituables
abstract class JsonResource
{
    abstract public function toArray(Request $request): array;
}

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            // ...
        ];
    }
}

class DetailedProductResource extends ProductResource
{
    public function toArray(Request $request): array
    {
        return array_merge(parent::toArray($request), [
            'variants' => ProductVariantResource::collection($this->variants),
            'options' => ProductOptionResource::collection($this->options),
        ]);
    }
}

// Les deux peuvent être utilisés de manière interchangeable
return new ProductResource($product);
return new DetailedProductResource($product);
```

### 1.5 Interface Segregation Principle (ISP)

Préférer des **interfaces spécifiques** plutôt que des interfaces génériques.

```php
// ❌ MAUVAIS - Interface trop large
interface CrudServiceInterface
{
    public function create(array $data): Model;
    public function update(Model $model, array $data): Model;
    public function delete(Model $model): bool;
    public function find(int $id): ?Model;
    public function list(): Collection;
    public function search(string $query): Collection;
    public function export(): string;
    public function import(string $data): void;
}

// ✅ BON - Interfaces spécifiques
interface CreatableServiceInterface
{
    public function create(array $data): Model;
}

interface UpdatableServiceInterface
{
    public function update(Model $model, array $data): Model;
}

interface DeletableServiceInterface
{
    public function delete(Model $model): bool;
}

// Les services implémentent uniquement ce dont ils ont besoin
class CreateProductService implements CreatableServiceInterface
{
    public function create(array $data): Model { /* ... */ }
}
```

### 1.6 Dependency Inversion Principle (DIP)

Dépendre des **abstractions**, pas des implémentations concrètes.

```php
// ❌ MAUVAIS - Dépendance concrète
class ProductController
{
    public function store(Request $request)
    {
        $service = new CreateProductService();  // Couplage fort
        $product = $service->execute($request->all());
    }
}

// ✅ BON - Injection de dépendance
class ProductController
{
    public function __construct(
        private CreateProductService $createService,  // Injecté par le container
    ) {}

    public function store(StoreProductRequest $request)
    {
        $dto = CreateProductInputDTO::fromRequest($request);
        $product = $this->createService->execute($dto, $request->user());
    }
}

// Laravel Service Container gère l'injection
// app/Providers/AppServiceProvider.php
$this->app->bind(CreateProductService::class, function ($app) {
    return new CreateProductService();
});
```

---

## 2. DRY - Don't Repeat Yourself

### 2.1 Principe

Chaque connaissance doit avoir une représentation **unique et non ambiguë** dans le système.

### 2.2 Application Backend

```php
// ❌ MAUVAIS - Duplication de la génération de slug
class CreateProductService
{
    public function execute($dto)
    {
        $slug = Str::slug($dto->name);
        while (Product::where('slug', $slug)->exists()) {
            $slug .= '-' . random_int(1, 100);
        }
        // ...
    }
}

class CreateCategoryService
{
    public function execute($dto)
    {
        $slug = Str::slug($dto->name);
        while (ProductCategory::where('slug', $slug)->exists()) {
            $slug .= '-' . random_int(1, 100);
        }
        // ...
    }
}

// ✅ BON - Trait réutilisable
trait GeneratesSlug
{
    protected function generateUniqueSlug(string $name, string $modelClass): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while ($modelClass::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}

class CreateProductService
{
    use GeneratesSlug;

    public function execute($dto)
    {
        $slug = $this->generateUniqueSlug($dto->name, Product::class);
        // ...
    }
}
```

### 2.3 Application Frontend (Vue.js)

```typescript
// ❌ MAUVAIS - Logique API dupliquée
// Dans Component1.vue
const fetchProducts = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/products', {
      headers: { Authorization: `Bearer ${token}` }
    });
    products.value = response.data.data;
  } catch (error) {
    error.value = 'Erreur';
  } finally {
    loading.value = false;
  }
};

// Dans Component2.vue (même code copié)

// ✅ BON - Composable réutilisable
// composable/API/Admin/Products/useGetIndexProduct.ts
export function useGetIndexProduct() {
  const data = ref<Product[] | null>(null);
  const loading = ref(false);
  const error = ref<string | null>(null);

  async function execute(params?: PaginationParams) {
    loading.value = true;
    error.value = null;
    try {
      const response = await api.get('/api/products', { params });
      data.value = response.data.data;
      return { success: true, data: data.value };
    } catch (e) {
      error.value = 'Erreur de chargement';
      return { success: false, error: error.value };
    } finally {
      loading.value = false;
    }
  }

  return { data, loading, error, execute };
}

// Utilisation partout
const { data: products, loading, execute } = useGetIndexProduct();
```

---

## 3. KISS - Keep It Simple, Stupid

### 3.1 Principe

La simplicité est la sophistication ultime. Préférer les solutions **simples et directes**.

### 3.2 Exemples

```php
// ❌ OVER-ENGINEERED
class ProductRepositoryInterface { }
class ProductRepository implements ProductRepositoryInterface { }
class ProductRepositoryFactory { }
class ProductRepositoryFactoryInterface { }
class AbstractProductService { }
class ProductServiceDecorator { }
// ... pour une simple liste de produits

// ✅ KISS - Direct et simple
class ListProductsService
{
    public function execute(?PagingDTO $paging = null): LengthAwarePaginator
    {
        return Product::with(['category', 'variants'])
            ->where('is_active', true)
            ->paginate($paging?->perPage ?? 15);
    }
}
```

```typescript
// ❌ OVER-ENGINEERED
const formatPrice = (price: number): string => {
  const formatter = new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'EUR',
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  });
  return formatter.format(price);
};

// ✅ KISS - Suffisant pour la plupart des cas
const formatPrice = (price: number): string => `${price.toFixed(2)} €`;
```

---

## 4. Design Patterns Utilisés

### 4.1 Service Layer Pattern

Encapsule la logique métier dans des services dédiés.

```
┌─────────────────────────────────────────────────────────────────┐
│                    SERVICE LAYER PATTERN                        │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│   Controller ──► Service ──► Model                             │
│       │             │           │                               │
│   HTTP concern  Business    Database                           │
│   - Request     logic       operations                          │
│   - Response    - Rules                                        │
│   - Auth        - Workflow                                     │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 4.2 Data Transfer Object (DTO)

Encapsule les données entre les couches.

```php
readonly class CreateProductInputDTO
{
    public function __construct(
        public string $name,
        public ?string $description,
        public int $productCategoryId,
        public bool $isActive = true,
        public array $storeIds = [],
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->validated('name'),
            description: $request->validated('description'),
            productCategoryId: $request->validated('product_category_id'),
            isActive: $request->validated('is_active', true),
            storeIds: $request->validated('store_ids', []),
        );
    }
}
```

### 4.3 Repository Pattern (via Eloquent)

Laravel Eloquent agit comme repository.

```php
// Eloquent model = Repository
$products = Product::where('is_active', true)
    ->with(['category', 'variants'])
    ->orderBy('created_at', 'desc')
    ->paginate(15);
```

### 4.4 Factory Pattern

Création d'objets complexes pour les tests.

```php
// database/factories/ProductFactory.php
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'slug' => fake()->slug(),
            'description' => fake()->paragraph(),
            'product_category_id' => ProductCategory::factory(),
            'created_by' => User::factory(),
            'is_active' => true,
            'is_featured' => fake()->boolean(20),
        ];
    }

    public function featured(): self
    {
        return $this->state(['is_featured' => true]);
    }

    public function inactive(): self
    {
        return $this->state(['is_active' => false]);
    }
}

// Utilisation
$product = Product::factory()->featured()->create();
```

### 4.5 Observer Pattern (via Events)

Laravel Events pour découpler les actions.

```php
// Événements automatiques Eloquent
class Product extends Model
{
    protected static function booted()
    {
        static::created(function ($product) {
            // Notification, indexation, etc.
        });

        static::deleted(function ($product) {
            // Nettoyage, archivage, etc.
        });
    }
}
```

### 4.6 Composition (Vue.js Composables)

Réutilisation de logique via la composition.

```typescript
// Composable pour la logique de chargement
function useLoadingState() {
  const loading = ref(false);
  const error = ref<string | null>(null);

  function withLoading<T>(fn: () => Promise<T>): Promise<T> {
    loading.value = true;
    error.value = null;
    return fn()
      .catch(e => { error.value = e.message; throw e; })
      .finally(() => { loading.value = false; });
  }

  return { loading, error, withLoading };
}

// Utilisation dans d'autres composables
function useProducts() {
  const { loading, error, withLoading } = useLoadingState();
  const products = ref<Product[]>([]);

  async function fetch() {
    products.value = await withLoading(() => api.getProducts());
  }

  return { products, loading, error, fetch };
}
```

---

## 5. Clean Code

### 5.1 Fonctions

```php
// ❌ MAUVAIS - Fonction trop longue, trop de responsabilités
function processOrder($order, $user, $payment, $shipping, $coupon = null) {
    // 200 lignes de code...
}

// ✅ BON - Fonctions courtes et focalisées
function validateOrder(Order $order): void { /* ... */ }
function applyDiscount(Order $order, ?Coupon $coupon): void { /* ... */ }
function processPayment(Order $order, Payment $payment): PaymentResult { /* ... */ }
function createShipment(Order $order, ShippingMethod $method): Shipment { /* ... */ }
function sendConfirmation(Order $order, User $user): void { /* ... */ }
```

### 5.2 Noms Significatifs

```php
// ❌ MAUVAIS
$d = 86400;
$arr = $query->get();
function calc($x, $y) { return $x * $y * 0.1; }

// ✅ BON
$secondsPerDay = 86400;
$activeProducts = $query->where('is_active', true)->get();
function calculateTax(float $price, float $quantity): float
{
    $taxRate = 0.1;
    return $price * $quantity * $taxRate;
}
```

### 5.3 Éviter les Magic Numbers

```php
// ❌ MAUVAIS
if ($user->role_id === 1) { /* admin */ }
$items = $query->take(15);

// ✅ BON
class UserRole
{
    const ADMIN = 1;
    const MANAGER = 2;
    const EMPLOYEE = 3;
}

if ($user->hasRole(UserRole::ADMIN)) { /* ... */ }

class PaginationDefaults
{
    const PER_PAGE = 15;
    const MAX_PER_PAGE = 100;
}

$items = $query->take(PaginationDefaults::PER_PAGE);
```

---

## 6. Conventions de Nommage

### 6.1 Backend (PHP/Laravel)

| Type | Convention | Exemple |
|------|------------|---------|
| **Classes** | PascalCase | `CreateProductService` |
| **Méthodes** | camelCase | `getActiveProducts()` |
| **Variables** | camelCase | `$productList` |
| **Constantes** | SCREAMING_SNAKE | `MAX_UPLOAD_SIZE` |
| **Tables BDD** | snake_case, pluriel | `product_categories` |
| **Colonnes BDD** | snake_case | `created_at` |
| **Routes** | kebab-case | `/product-categories` |

### 6.2 Frontend (Vue.js/TypeScript)

| Type | Convention | Exemple |
|------|------------|---------|
| **Composants** | PascalCase | `ProductCard.vue` |
| **Composables** | camelCase, use prefix | `useGetProducts.ts` |
| **Variables** | camelCase | `productList` |
| **Types/Interfaces** | PascalCase | `interface Product` |
| **Constantes** | SCREAMING_SNAKE | `API_BASE_URL` |
| **Props** | camelCase | `:productId` |
| **Events** | camelCase, on prefix | `@onProductSelected` |

### 6.3 Mobile (React Native/TypeScript)

| Type | Convention | Exemple |
|------|------------|---------|
| **Composants** | PascalCase | `ProductCard.tsx` |
| **Hooks** | camelCase, use prefix | `useAuth.ts` |
| **Contexts** | PascalCase | `AuthContext.tsx` |
| **Services** | PascalCase | `ApiService.ts` |

---

## 7. Stratégie de Tests

### 7.1 Pyramide de Tests

```
┌─────────────────────────────────────────────────────────────────┐
│                    PYRAMIDE DE TESTS                            │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│                        ▲                                        │
│                       ╱ ╲                                       │
│                      ╱ E2E╲      Peu nombreux                   │
│                     ╱─────╲     Lents                          │
│                    ╱       ╲    UI tests                       │
│                   ╱─────────╲                                   │
│                  ╱Integration╲  Moyennement nombreux            │
│                 ╱─────────────╲ API tests                       │
│                ╱               ╲Feature tests                   │
│               ╱─────────────────╲                               │
│              ╱   Unit Tests      ╲ Très nombreux                │
│             ╱─────────────────────╲Rapides                      │
│            ╱  Services, DTOs, etc. ╲                           │
│           ▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔                          │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 7.2 Tests Unitaires (PestPHP)

```php
// tests/Unit/Domain/Product/Services/CreateProductServiceTest.php
use App\Domain\Product\Services\CreateProductService;
use App\Domain\Product\DTOs\CreateProductInputDTO;

describe('CreateProductService', function () {
    it('creates a product with valid data', function () {
        $service = new CreateProductService();
        $user = User::factory()->create();
        $category = ProductCategory::factory()->create();

        $dto = new CreateProductInputDTO(
            name: 'Test Product',
            description: 'Description',
            productCategoryId: $category->id,
        );

        $product = $service->execute($dto, $user);

        expect($product)
            ->toBeInstanceOf(Product::class)
            ->name->toBe('Test Product')
            ->created_by->toBe($user->id);
    });

    it('generates unique slug for duplicate names', function () {
        $service = new CreateProductService();
        $user = User::factory()->create();
        $category = ProductCategory::factory()->create();

        $dto = new CreateProductInputDTO(
            name: 'Same Name',
            productCategoryId: $category->id,
        );

        $product1 = $service->execute($dto, $user);
        $product2 = $service->execute($dto, $user);

        expect($product1->slug)->not->toBe($product2->slug);
    });
});
```

### 7.3 Tests d'Intégration (Feature)

```php
// tests/Feature/Api/ProductControllerTest.php
describe('ProductController', function () {
    it('returns paginated products for authenticated user', function () {
        $user = User::factory()->create();
        Product::factory()->count(20)->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/products?page=1&per_page=10');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'name', 'slug'],
                ],
                'meta' => ['current_page', 'total'],
            ])
            ->assertJsonCount(10, 'data');
    });

    it('requires authentication', function () {
        $this->getJson('/api/products')
            ->assertUnauthorized();
    });
});
```

### 7.4 Coverage Cible

| Type | Couverture Cible |
|------|------------------|
| Services | 90%+ |
| Controllers | 80%+ |
| Models | 70%+ |
| Global | 75%+ |

---

## 8. Documentation du Code

### 8.1 PHPDoc

```php
/**
 * Service de création de produit.
 *
 * Ce service gère la création d'un nouveau produit dans le système,
 * incluant la génération automatique du slug et l'association aux magasins.
 */
class CreateProductService
{
    /**
     * Crée un nouveau produit.
     *
     * @param CreateProductInputDTO $dto Les données du produit à créer
     * @param User $creator L'utilisateur qui crée le produit
     * @return Product Le produit créé avec ses relations chargées
     * @throws ValidationException Si les données sont invalides
     */
    public function execute(CreateProductInputDTO $dto, User $creator): Product
    {
        // ...
    }
}
```

### 8.2 TypeScript (TSDoc)

```typescript
/**
 * Composable pour récupérer la liste des produits.
 *
 * @example
 * ```typescript
 * const { data, loading, execute } = useGetIndexProduct();
 * await execute({ page: 1, per_page: 15 });
 * ```
 *
 * @returns Object contenant data, loading, error et la fonction execute
 */
export function useGetIndexProduct() {
  // ...
}
```

### 8.3 Commentaires dans le Code

```php
// ✅ BON - Explique le POURQUOI
// Le slug doit être unique même parmi les produits supprimés
// pour éviter les conflits lors d'une restauration
while (Product::withTrashed()->where('slug', $slug)->exists()) {
    $slug = $originalSlug . '-' . $counter;
    $counter++;
}

// ❌ MAUVAIS - Explique le QUOI (déjà visible dans le code)
// Incrémente le compteur
$counter++;
```

---

## Résumé

```
┌─────────────────────────────────────────────────────────────────┐
│                    CHECKLIST BONNES PRATIQUES                   │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│   SOLID                                                         │
│   [x] Single Responsibility - Une classe = une responsabilité  │
│   [x] Open/Closed - Extension sans modification                │
│   [x] Liskov Substitution - Sous-types substituables           │
│   [x] Interface Segregation - Interfaces spécifiques           │
│   [x] Dependency Inversion - Injection de dépendances          │
│                                                                 │
│   DRY                                                           │
│   [x] Composables réutilisables (Vue.js)                       │
│   [x] Services partagés (Laravel)                              │
│   [x] Traits pour logique commune                              │
│                                                                 │
│   KISS                                                          │
│   [x] Solutions simples et directes                            │
│   [x] Éviter l'over-engineering                                │
│                                                                 │
│   CLEAN CODE                                                    │
│   [x] Noms significatifs                                       │
│   [x] Fonctions courtes et focalisées                          │
│   [x] Éviter les magic numbers                                 │
│                                                                 │
│   TESTS                                                         │
│   [x] Tests unitaires sur les services                         │
│   [x] Tests d'intégration sur l'API                            │
│   [x] Factories pour les données de test                       │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

---

## Ressources

- [Clean Code (Robert C. Martin)](https://www.oreilly.com/library/view/clean-code-a/9780136083238/)
- [SOLID Principles](https://en.wikipedia.org/wiki/SOLID)
- [Laravel Best Practices](https://github.com/alexeymezenin/laravel-best-practices)
- [Vue.js Style Guide](https://vuejs.org/style-guide/)
- [PestPHP Documentation](https://pestphp.com/)

---
