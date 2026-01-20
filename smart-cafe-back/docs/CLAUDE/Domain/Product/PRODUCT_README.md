# Module Product

## Vue d'ensemble

Le module Product gère l'ensemble des produits vendus dans les stores de l'application Smart Café. Il inclut la gestion des produits, variants, options, galeries d'images et stock par store.

## Structure

```
app/
├── Domain/Product/
│   ├── Constant/
│   │   └── ProductConstant.php
│   ├── DTOs/
│   │   ├── CreateProductInputDTO.php
│   │   ├── UpdateProductInputDTO.php
│   │   ├── ListProductsFilterDTO.php
│   │   ├── CreateProductVariantInputDTO.php
│   │   ├── UpdateProductVariantInputDTO.php
│   │   ├── CreateProductOptionInputDTO.php
│   │   ├── UpdateProductOptionInputDTO.php
│   │   ├── CreateProductOptionValueInputDTO.php
│   │   ├── UpdateProductOptionValueInputDTO.php
│   │   ├── AttachProductToStoresInputDTO.php
│   │   └── UpdateVariantStockInputDTO.php
│   └── Services/
│       ├── CreateProductService.php
│       ├── UpdateProductService.php
│       ├── DeleteProductService.php
│       ├── ListProductsService.php
│       ├── GetProductService.php
│       ├── AttachGalleryToProductService.php
│       ├── DetachGalleryFromProductService.php
│       ├── CreateProductVariantService.php
│       ├── UpdateProductVariantService.php
│       ├── DeleteProductVariantService.php
│       ├── AttachGalleryToVariantService.php
│       ├── DetachGalleryFromVariantService.php
│       ├── CreateProductOptionService.php
│       ├── UpdateProductOptionService.php
│       ├── DeleteProductOptionService.php
│       ├── CreateProductOptionValueService.php
│       ├── UpdateProductOptionValueService.php
│       ├── DeleteProductOptionValueService.php
│       ├── AttachProductToStoresService.php
│       ├── DetachProductFromStoreService.php
│       ├── UpdateVariantStockService.php
│       └── DeleteVariantStockService.php
├── Models/
│   ├── Product.php
│   ├── ProductVariant.php
│   ├── ProductOption.php
│   ├── ProductOptionValue.php
│   └── StoreProductVariant.php
├── Http/
│   ├── Controllers/
│   │   ├── ProductController.php
│   │   ├── ProductVariantController.php
│   │   ├── ProductOptionController.php
│   │   ├── ProductOptionValueController.php
│   │   └── ProductStoreController.php
│   ├── Requests/
│   │   ├── StoreProductRequest.php
│   │   ├── UpdateProductRequest.php
│   │   ├── StoreProductVariantRequest.php
│   │   ├── UpdateProductVariantRequest.php
│   │   ├── StoreProductOptionRequest.php
│   │   ├── UpdateProductOptionRequest.php
│   │   ├── StoreProductOptionValueRequest.php
│   │   ├── UpdateProductOptionValueRequest.php
│   │   ├── AttachProductGalleryRequest.php
│   │   ├── AttachVariantGalleryRequest.php
│   │   ├── AttachProductToStoresRequest.php
│   │   └── UpdateVariantStockRequest.php
│   └── Resources/
│       ├── ProductResource.php
│       ├── ProductVariantResource.php
│       ├── ProductOptionResource.php
│       ├── ProductOptionValueResource.php
│       └── StoreProductVariantResource.php
└── Policies/
    ├── ProductPolicy.php
    ├── ProductVariantPolicy.php
    ├── ProductOptionPolicy.php
    └── ProductOptionValuePolicy.php
```

## Concepts clés

### Produit (Product)

Un produit représente un article vendu dans un ou plusieurs stores (ex: Cappuccino, Croissant). Il appartient à :
- Une catégorie (`ProductCategory`)
- Plusieurs stores (`Store`) via la table pivot `product_store`
- Un créateur (`User` admin)

### Relation Many-to-Many Product <-> Store

Un produit peut être vendu dans plusieurs stores, et un store peut vendre plusieurs produits.

**Table pivot `product_store`** :

| Colonne | Type | Description |
|---------|------|-------------|
| id | bigint | Clé primaire |
| product_id | FK | Référence au produit |
| store_id | FK | Référence au store |
| created_at | timestamp | Date d'association |
| updated_at | timestamp | Date de modification |

### Variant (ProductVariant)

Un variant représente une déclinaison d'un produit avec :
- Un SKU unique
- Un prix HT (en centimes)
- Un flag `is_default` (un seul par produit)

Le stock n'est plus stocké sur le variant mais dans la table `store_product_variant`.

Exemple : Un Cappuccino peut avoir des variants Small, Medium, Large.

### Stock par Variant par Store

Le stock est géré par variant ET par store via la table `store_product_variant`.

**Table `store_product_variant`** :

| Colonne | Type | Description |
|---------|------|-------------|
| id | bigint | Clé primaire |
| store_id | FK | Référence au store |
| product_variant_id | FK | Référence au variant |
| stock | integer nullable | Quantité en stock (null = illimité) |
| created_at | timestamp | Date de création |
| updated_at | timestamp | Date de modification |

**Stock illimité** : Quand `stock = null`, le variant est considéré comme ayant un stock illimité (boissons, café, etc.).

**Méthodes disponibles sur `StoreProductVariant`** :
- `isUnlimited()` : Retourne `true` si le stock est null
- `isInStock()` : Retourne `true` si illimité ou stock > 0
- `decrementStock(int $quantity = 1)` : Décrémente le stock
- `incrementStock(int $quantity = 1)` : Incrémente le stock

### Option (ProductOption)

Une option représente un choix disponible pour un produit :
- Nom de l'option (ex: "Type de lait", "Sucre")
- Flag `is_required` si le choix est obligatoire

### Valeur d'option (ProductOptionValue)

Une valeur d'option représente un choix possible :
- Valeur (ex: "Lait d'avoine", "Sans sucre")
- Supplément de prix HT (en centimes)

## Prix

Tous les prix sont stockés en **centimes** (integer) pour éviter les erreurs d'arrondi :
- `price_cent_ht` : Prix hors taxes du variant
- `price_add_ht` : Supplément de prix pour une valeur d'option
- `cost_price_cent_ht` : Prix de revient (optionnel)

Les modèles fournissent des accesseurs pour obtenir le prix en euros :
- `$variant->price_euros` → `3.50`
- `$optionValue->price_add_euros` → `0.50`

## Galerie d'images

Les produits et variants peuvent avoir une galerie d'images :
- Pivot table `product_gallery` et `product_variant_gallery`
- Utilise le modèle `StoredFile`
- Maximum 10 images par produit/variant
- Position pour l'ordre d'affichage

## Permissions

| Rôle | Permissions |
|------|-------------|
| Admin | CRUD complet sur tous les produits, association aux stores, gestion des stocks |
| Manager | Lecture seule sur produits de ses stores |
| Employer | Lecture seule sur produits de ses stores |

## Routes API

### Admin - Produits

```
GET    /api/admin/products                    Liste tous les produits
POST   /api/admin/products                    Créer un produit
GET    /api/admin/products/{id}               Détail d'un produit
PUT    /api/admin/products/{id}               Modifier un produit
DELETE /api/admin/products/{id}               Supprimer un produit
POST   /api/admin/products/{id}/gallery       Ajouter des images
DELETE /api/admin/products/{id}/gallery/{img} Retirer une image
```

### Admin - Association Produit-Store

```
GET    /api/admin/products/{id}/stores           Liste des stores du produit
POST   /api/admin/products/{id}/stores           Associer des stores au produit
DELETE /api/admin/products/{id}/stores/{store}   Retirer un store du produit
```

### Admin - Variants

```
GET    /api/admin/products/{id}/variants           Liste les variants
POST   /api/admin/products/{id}/variants           Créer un variant
GET    /api/admin/products/{id}/variants/{vid}     Détail
PUT    /api/admin/products/{id}/variants/{vid}     Modifier
DELETE /api/admin/products/{id}/variants/{vid}     Supprimer
POST   /api/admin/products/{id}/variants/{vid}/gallery     Ajouter images
DELETE /api/admin/products/{id}/variants/{vid}/gallery/{img} Retirer image
```

### Admin - Stock Variant par Store

```
GET    /api/admin/products/{id}/variants/{vid}/stocks         Liste des stocks par store
PUT    /api/admin/products/{id}/variants/{vid}/stocks/{store} Définir le stock
DELETE /api/admin/products/{id}/variants/{vid}/stocks/{store} Supprimer le stock
```

### Admin - Options

```
GET    /api/admin/products/{id}/options            Liste les options
POST   /api/admin/products/{id}/options            Créer une option
GET    /api/admin/products/{id}/options/{oid}      Détail
PUT    /api/admin/products/{id}/options/{oid}      Modifier
DELETE /api/admin/products/{id}/options/{oid}      Supprimer
```

### Admin - Valeurs d'options

```
GET    /api/admin/product-options/{oid}/values           Liste les valeurs
POST   /api/admin/product-options/{oid}/values           Créer une valeur
GET    /api/admin/product-options/{oid}/values/{vid}     Détail
PUT    /api/admin/product-options/{oid}/values/{vid}     Modifier
DELETE /api/admin/product-options/{oid}/values/{vid}     Supprimer
```

### Authentifié - Lecture

```
GET    /api/products                          Liste produits accessibles
GET    /api/products/{id}                     Détail si accessible
```

## Exemples de requêtes

### Créer un produit avec plusieurs stores

**Requête POST `/api/admin/products`** :
```json
{
  "name": "Cappuccino",
  "description": "Espresso avec lait mousseux",
  "product_category_id": 1,
  "store_ids": [1, 2],
  "is_active": true,
  "is_featured": true
}
```

### Associer des stores à un produit existant

**Requête POST `/api/admin/products/{id}/stores`** :
```json
{
  "store_ids": [3, 4]
}
```

### Définir le stock d'un variant dans un store

**Requête PUT `/api/admin/products/{id}/variants/{vid}/stocks/{store}`** :
```json
{
  "stock": 50
}
```

Pour un stock illimité :
```json
{
  "stock": null
}
```

## Exemples de réponses

### Produit avec plusieurs stores

```json
{
  "success": true,
  "message": "Produit récupéré avec succès.",
  "data": {
    "id": 1,
    "name": "Cappuccino",
    "slug": "cappuccino",
    "description": "Espresso avec lait mousseux",
    "is_active": true,
    "is_featured": true,
    "category": {
      "id": 1,
      "name": "Cafés"
    },
    "stores": [
      { "id": 1, "name": "Café Central" },
      { "id": 2, "name": "Café du Parc" }
    ],
    "variants": [
      {
        "id": 1,
        "sku": "CAP-S",
        "price_cent_ht": 350,
        "price_euros": "3.50",
        "is_default": true,
        "store_stocks": [
          { "store_id": 1, "stock": null, "is_unlimited": true, "is_in_stock": true },
          { "store_id": 2, "stock": 50, "is_unlimited": false, "is_in_stock": true }
        ]
      }
    ]
  }
}
```

### Stock d'un variant

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
    "store": { "id": 1, "name": "Café Central" },
    "variant": { "id": 1, "sku": "CAP-S" }
  }
}
```

## Voir aussi

- [PRODUCT_SERVICES.md](PRODUCT_SERVICES.md) - Documentation des services
- [PRODUCT_MODELS.md](PRODUCT_MODELS.md) - Documentation des modèles
- [PRODUCT_VARIANT_README.md](PRODUCT_VARIANT_README.md) - Documentation détaillée des variants
