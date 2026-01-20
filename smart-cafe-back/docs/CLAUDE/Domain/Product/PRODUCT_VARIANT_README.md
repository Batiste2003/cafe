# Product Variant

## Vue d'ensemble

Un ProductVariant représente une déclinaison spécifique d'un produit. Chaque produit doit avoir au moins un variant, et peut en avoir plusieurs (différentes tailles, saveurs, etc.).

## Table product_variants

| Colonne | Type | Description |
|---------|------|-------------|
| id | bigint | Clé primaire |
| product_id | FK | Référence au produit parent |
| sku | string unique | Code SKU unique |
| price_cent_ht | unsigned int | Prix HT en centimes |
| cost_price_cent_ht | unsigned int nullable | Prix de revient en centimes |
| is_default | boolean | Variant par défaut du produit |
| created_at | timestamp | Date de création |
| updated_at | timestamp | Date de modification |
| deleted_at | timestamp nullable | Soft delete |

## Relation avec le Stock

Le stock n'est plus stocké directement sur le variant. Il est géré dans la table pivot `store_product_variant` qui permet d'avoir un stock différent pour chaque combinaison variant/store.

## Modèle ProductVariant

### Relations

```php
// Produit parent
$variant->product; // BelongsTo Product

// Galerie d'images
$variant->gallery; // BelongsToMany StoredFile

// Valeurs d'options associées
$variant->optionValues; // BelongsToMany ProductOptionValue

// Stocks par store
$variant->storeStocks; // HasMany StoreProductVariant

// Stores où le variant est disponible (via storeStocks)
$variant->stores; // BelongsToMany Store
```

### Accesseurs

```php
// Prix en euros (conversion depuis centimes)
$variant->price_euros; // "3.50"

// Prix de revient en euros
$variant->cost_price_euros; // "1.50" ou null
```

### Méthodes de Stock

```php
// Obtenir le stock dans un store spécifique
$stock = $variant->getStockForStore($store); // int|null (null = illimité)

// Vérifier si en stock dans un store
$isInStock = $variant->isInStockForStore($store); // bool

// Vérifier si disponible dans un store (a une entrée stock)
$isAvailable = $variant->isAvailableInStore($store); // bool
```

## Stock par Store (StoreProductVariant)

### Table store_product_variant

| Colonne | Type | Description |
|---------|------|-------------|
| id | bigint | Clé primaire |
| store_id | FK | Référence au store |
| product_variant_id | FK | Référence au variant |
| stock | integer nullable | Quantité (null = illimité) |
| created_at | timestamp | Date de création |
| updated_at | timestamp | Date de modification |

### Modèle StoreProductVariant

```php
use App\Models\StoreProductVariant;

// Récupérer le stock d'un variant dans un store
$storeStock = StoreProductVariant::where('product_variant_id', $variant->id)
    ->where('store_id', $store->id)
    ->first();

// Ou via la relation
$storeStock = $variant->storeStocks()->where('store_id', $store->id)->first();
```

### Méthodes disponibles

```php
// Vérifier si le stock est illimité
$storeStock->isUnlimited(); // true si stock === null

// Vérifier si en stock
$storeStock->isInStock(); // true si illimité OU stock > 0

// Décrémenter le stock (ex: lors d'une commande)
$success = $storeStock->decrementStock(2); // false si stock insuffisant

// Incrémenter le stock (ex: lors d'un retour)
$storeStock->incrementStock(1);
```

### Stock illimité

Pour les produits qui ne nécessitent pas de suivi de stock (boissons, café en grains, etc.), définir `stock = null` :

```php
StoreProductVariant::updateOrCreate(
    ['store_id' => $store->id, 'product_variant_id' => $variant->id],
    ['stock' => null] // Illimité
);
```

## Routes API

### Admin - CRUD Variants

| Méthode | Route | Action |
|---------|-------|--------|
| GET | `/api/admin/products/{product}/variants` | Liste les variants |
| POST | `/api/admin/products/{product}/variants` | Créer un variant |
| GET | `/api/admin/products/{product}/variants/{variant}` | Détail |
| PUT | `/api/admin/products/{product}/variants/{variant}` | Modifier |
| DELETE | `/api/admin/products/{product}/variants/{variant}` | Supprimer |

### Admin - Galerie Variant

| Méthode | Route | Action |
|---------|-------|--------|
| POST | `/api/admin/products/{product}/variants/{variant}/gallery` | Ajouter une image |
| DELETE | `/api/admin/products/{product}/variants/{variant}/gallery/{file}` | Retirer une image |

### Admin - Stock par Store

| Méthode | Route | Action |
|---------|-------|--------|
| GET | `/api/admin/products/{product}/variants/{variant}/stocks` | Liste des stocks |
| PUT | `/api/admin/products/{product}/variants/{variant}/stocks/{store}` | Définir le stock |
| DELETE | `/api/admin/products/{product}/variants/{variant}/stocks/{store}` | Supprimer le stock |

## Exemples de requêtes

### Créer un variant

**POST `/api/admin/products/1/variants`**
```json
{
  "sku": "CAP-L",
  "price_cent_ht": 450,
  "cost_price_cent_ht": 150,
  "is_default": false
}
```

### Définir le stock dans un store

**PUT `/api/admin/products/1/variants/1/stocks/1`**
```json
{
  "stock": 50
}
```

### Définir un stock illimité

**PUT `/api/admin/products/1/variants/1/stocks/1`**
```json
{
  "stock": null
}
```

## Exemples de réponses

### Variant avec stocks par store

```json
{
  "id": 1,
  "sku": "CAP-S",
  "price_cent_ht": 350,
  "price_euros": "3.50",
  "cost_price_cent_ht": 150,
  "cost_price_euros": "1.50",
  "is_default": true,
  "store_stocks": [
    {
      "id": 1,
      "store_id": 1,
      "product_variant_id": 1,
      "stock": null,
      "is_unlimited": true,
      "is_in_stock": true,
      "store": {
        "id": 1,
        "name": "Café Central"
      }
    },
    {
      "id": 2,
      "store_id": 2,
      "product_variant_id": 1,
      "stock": 50,
      "is_unlimited": false,
      "is_in_stock": true,
      "store": {
        "id": 2,
        "name": "Café du Parc"
      }
    }
  ],
  "product": { ... },
  "gallery": [ ... ],
  "option_values": [ ... ]
}
```

### Stock mis à jour

```json
{
  "success": true,
  "message": "Stock du variant mis à jour avec succès.",
  "data": {
    "id": 1,
    "store_id": 1,
    "product_variant_id": 1,
    "stock": 50,
    "is_unlimited": false,
    "is_in_stock": true,
    "store": {
      "id": 1,
      "name": "Café Central"
    },
    "variant": {
      "id": 1,
      "sku": "CAP-S"
    }
  }
}
```

## Utilisation dans le Code

### Créer un variant

```php
use App\Domain\Product\DTOs\CreateProductVariantInputDTO;
use App\Domain\Product\Services\CreateProductVariantService;

$dto = CreateProductVariantInputDTO::fromArray([
    'sku' => 'CAP-L',
    'price_cent_ht' => 450,
    'cost_price_cent_ht' => 150,
    'is_default' => false,
]);

$service = app(CreateProductVariantService::class);
$variant = $service->execute($product, $dto);
```

### Gérer le stock

```php
use App\Domain\Product\DTOs\UpdateVariantStockInputDTO;
use App\Domain\Product\Services\UpdateVariantStockService;

// Définir un stock de 50
$dto = UpdateVariantStockInputDTO::fromArray(['stock' => 50]);
$service = app(UpdateVariantStockService::class);
$storeStock = $service->execute($variant, $store, $dto);

// Définir un stock illimité
$dto = UpdateVariantStockInputDTO::fromArray(['stock' => null]);
$storeStock = $service->execute($variant, $store, $dto);
```

### Vérifier le stock avant une commande

```php
use App\Models\StoreProductVariant;

$storeStock = StoreProductVariant::where('product_variant_id', $variant->id)
    ->where('store_id', $store->id)
    ->first();

if (!$storeStock || !$storeStock->isInStock()) {
    throw new \Exception('Produit en rupture de stock');
}

// Décrémenter le stock
if (!$storeStock->isUnlimited()) {
    $storeStock->decrementStock($quantity);
}
```

### Lister les variants disponibles dans un store

```php
$product->load(['variants.storeStocks' => function ($query) use ($store) {
    $query->where('store_id', $store->id);
}]);

$availableVariants = $product->variants->filter(function ($variant) use ($store) {
    return $variant->isAvailableInStore($store) && $variant->isInStockForStore($store);
});
```

## Règles métier

1. **SKU unique** : Chaque variant doit avoir un SKU unique dans toute l'application
2. **Un seul variant par défaut** : Si `is_default = true` est défini, les autres variants du même produit sont automatiquement mis à `is_default = false`
3. **Stock par store** : Le stock est géré indépendamment pour chaque store
4. **Stock illimité** : `stock = null` indique un stock illimité (pas de décrément)
5. **Soft delete** : Les variants supprimés sont conservés en base (pour l'historique des commandes)

## Voir aussi

- [PRODUCT_README.md](PRODUCT_README.md) - Documentation du module Product
- [PRODUCT_SERVICES.md](PRODUCT_SERVICES.md) - Documentation des services
- [PRODUCT_MODELS.md](PRODUCT_MODELS.md) - Documentation des modèles
