# Services Product

## Services Produit

### CreateProductService

Crée un nouveau produit avec génération automatique du slug et association aux stores.

```php
public function execute(CreateProductInputDTO $dto, User $creator): Product
```

**Entrée** : `CreateProductInputDTO`
- `name` (string) : Nom du produit
- `description` (?string) : Description
- `productCategoryId` (int) : ID de la catégorie
- `storeIds` (array<int>) : IDs des stores où le produit sera vendu
- `isActive` (bool) : Actif ou non
- `isFeatured` (bool) : Mis en avant

**Sortie** : `Product` avec relations chargées (stores, category, creator)

### UpdateProductService

Met à jour un produit existant.

```php
public function execute(Product $product, UpdateProductInputDTO $dto): Product
```

### DeleteProductService

Supprime un produit (soft delete). Supprime également les variants en cascade.

```php
public function execute(Product $product): bool
```

### ListProductsService

Liste les produits avec filtres et pagination.

```php
public function execute(ListProductsFilterDTO $dto, ?User $user = null): LengthAwarePaginator
```

**Filtres** :
- `search` : Recherche par nom
- `categoryId` : Filtrer par catégorie
- `storeId` : Filtrer par store (vérifie la relation many-to-many)
- `isActive` : Filtrer par statut
- `isFeatured` : Filtrer les mis en avant

### GetProductService

Récupère un produit avec toutes ses relations.

```php
public function execute(Product $product): Product
```

## Services Association Produit-Store

### AttachProductToStoresService

Associe un produit à plusieurs stores.

```php
public function execute(Product $product, AttachProductToStoresInputDTO $dto): Product
```

**Entrée** : `AttachProductToStoresInputDTO`
- `storeIds` (array<int>) : IDs des stores à associer

**Comportement** : Utilise `syncWithoutDetaching()` pour ajouter les nouveaux stores sans supprimer les existants.

**Sortie** : `Product` avec la relation `stores` chargée

### DetachProductFromStoreService

Retire un produit d'un store. Supprime également les stocks des variants de ce produit dans ce store.

```php
public function execute(Product $product, Store $store): Product
```

**Comportement** :
1. Supprime toutes les entrées `store_product_variant` pour ce produit dans ce store
2. Détache le produit du store

**Sortie** : `Product` avec la relation `stores` mise à jour

## Services Galerie Produit

### AttachGalleryToProductService

Ajoute une image à la galerie d'un produit.

```php
public function execute(Product $product, int $storedFileId, ?int $position = null): Product
```

**Validation** : Maximum 10 images par produit.

### DetachGalleryFromProductService

Retire une image de la galerie.

```php
public function execute(Product $product, int $storedFileId): Product
```

## Services Variant

### CreateProductVariantService

Crée un nouveau variant pour un produit.

```php
public function execute(Product $product, CreateProductVariantInputDTO $dto): ProductVariant
```

**Entrée** : `CreateProductVariantInputDTO`
- `sku` (string) : SKU unique
- `priceHt` (int) : Prix en centimes
- `costPriceHt` (?int) : Prix de revient
- `isDefault` (bool) : Variant par défaut

**Note** : Le stock n'est plus géré ici mais via `UpdateVariantStockService`.

**Note** : Si `isDefault = true`, les autres variants du produit sont mis à `is_default = false`.

### UpdateProductVariantService

Met à jour un variant existant.

```php
public function execute(ProductVariant $variant, UpdateProductVariantInputDTO $dto): ProductVariant
```

### DeleteProductVariantService

Supprime un variant (soft delete).

```php
public function execute(ProductVariant $variant): bool
```

## Services Galerie Variant

### AttachGalleryToVariantService

Ajoute une image à la galerie d'un variant.

```php
public function execute(ProductVariant $variant, int $storedFileId, ?int $position = null): ProductVariant
```

### DetachGalleryFromVariantService

Retire une image de la galerie d'un variant.

```php
public function execute(ProductVariant $variant, int $storedFileId): ProductVariant
```

## Services Stock Variant

### UpdateVariantStockService

Met à jour ou crée le stock d'un variant dans un store.

```php
public function execute(ProductVariant $variant, Store $store, UpdateVariantStockInputDTO $dto): StoreProductVariant
```

**Entrée** : `UpdateVariantStockInputDTO`
- `stock` (?int) : Quantité en stock (null = illimité)

**Comportement** : Utilise `updateOrCreate()` pour créer ou mettre à jour l'entrée.

**Sortie** : `StoreProductVariant` l'entrée de stock créée ou mise à jour

**Exemple** :
```php
use App\Domain\Product\DTOs\UpdateVariantStockInputDTO;
use App\Domain\Product\Services\UpdateVariantStockService;

$dto = UpdateVariantStockInputDTO::fromArray(['stock' => 50]);
$service = new UpdateVariantStockService();
$storeProductVariant = $service->execute($variant, $store, $dto);

// Pour un stock illimité
$dto = UpdateVariantStockInputDTO::fromArray(['stock' => null]);
$storeProductVariant = $service->execute($variant, $store, $dto);
```

### DeleteVariantStockService

Supprime le stock d'un variant dans un store.

```php
public function execute(ProductVariant $variant, Store $store): bool
```

**Comportement** : Supprime l'entrée `store_product_variant` si elle existe.

**Sortie** : `bool` - `true` si une entrée a été supprimée, `false` sinon

## Services Option

### CreateProductOptionService

Crée une nouvelle option pour un produit.

```php
public function execute(Product $product, CreateProductOptionInputDTO $dto): ProductOption
```

**Entrée** : `CreateProductOptionInputDTO`
- `name` (string) : Nom de l'option
- `isRequired` (bool) : Option obligatoire

### UpdateProductOptionService

Met à jour une option existante.

```php
public function execute(ProductOption $option, UpdateProductOptionInputDTO $dto): ProductOption
```

### DeleteProductOptionService

Supprime une option et toutes ses valeurs.

```php
public function execute(ProductOption $option): bool
```

## Services Valeur d'Option

### CreateProductOptionValueService

Crée une nouvelle valeur pour une option.

```php
public function execute(ProductOption $option, CreateProductOptionValueInputDTO $dto): ProductOptionValue
```

**Entrée** : `CreateProductOptionValueInputDTO`
- `value` (string) : Valeur de l'option
- `priceAddHt` (int) : Supplément de prix en centimes

### UpdateProductOptionValueService

Met à jour une valeur d'option.

```php
public function execute(ProductOptionValue $value, UpdateProductOptionValueInputDTO $dto): ProductOptionValue
```

### DeleteProductOptionValueService

Supprime une valeur d'option. Détache également la valeur des variants qui l'utilisent.

```php
public function execute(ProductOptionValue $value): bool
```

## Utilisation dans le Code

### Créer un produit avec plusieurs stores

```php
use App\Domain\Product\DTOs\CreateProductInputDTO;
use App\Domain\Product\Services\CreateProductService;

$dto = CreateProductInputDTO::fromArray([
    'name' => 'Cappuccino',
    'description' => 'Espresso avec lait mousseux',
    'product_category_id' => 1,
    'store_ids' => [1, 2, 3],
    'is_active' => true,
    'is_featured' => true,
]);

$service = app(CreateProductService::class);
$product = $service->execute($dto, $admin);
```

### Associer des stores à un produit existant

```php
use App\Domain\Product\DTOs\AttachProductToStoresInputDTO;
use App\Domain\Product\Services\AttachProductToStoresService;

$dto = AttachProductToStoresInputDTO::fromArray([
    'store_ids' => [4, 5],
]);

$service = app(AttachProductToStoresService::class);
$product = $service->execute($product, $dto);
```

### Retirer un produit d'un store

```php
use App\Domain\Product\Services\DetachProductFromStoreService;

$service = app(DetachProductFromStoreService::class);
$product = $service->execute($product, $store);
// Les stocks des variants dans ce store sont également supprimés
```

### Gérer le stock d'un variant dans un store

```php
use App\Domain\Product\DTOs\UpdateVariantStockInputDTO;
use App\Domain\Product\Services\UpdateVariantStockService;

// Définir un stock de 50 unités
$dto = UpdateVariantStockInputDTO::fromArray(['stock' => 50]);
$service = app(UpdateVariantStockService::class);
$storeProductVariant = $service->execute($variant, $store, $dto);

// Définir un stock illimité
$dto = UpdateVariantStockInputDTO::fromArray(['stock' => null]);
$storeProductVariant = $service->execute($variant, $store, $dto);

echo $storeProductVariant->isUnlimited(); // true
echo $storeProductVariant->isInStock(); // true
```

### Vérifier le stock d'un variant dans un store

```php
// Via le modèle ProductVariant
$stock = $variant->getStockForStore($store); // int|null (null = illimité)
$isInStock = $variant->isInStockForStore($store); // bool
$isAvailable = $variant->isAvailableInStore($store); // bool (a une entrée stock)

// Via le modèle StoreProductVariant
$storeProductVariant = StoreProductVariant::where('product_variant_id', $variant->id)
    ->where('store_id', $store->id)
    ->first();

if ($storeProductVariant) {
    echo $storeProductVariant->isUnlimited(); // bool
    echo $storeProductVariant->isInStock(); // bool

    // Décrémenter le stock (pour une commande)
    $storeProductVariant->decrementStock(2);

    // Incrémenter le stock (pour un retour)
    $storeProductVariant->incrementStock(1);
}
```
