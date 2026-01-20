# Domain Store

## Vue d'ensemble

Le domaine Store gère les points de vente (cafés) et contrôle l'accès aux données en fonction des utilisateurs associés à chaque store. Il gère également la relation avec les produits et les stocks.

## Structure

```
app/Domain/Store/
├── Constant/
│   └── StoreConstant.php          # Constantes (pagination, messages)
├── DTOs/
│   ├── CreateStoreInputDTO.php    # DTO création magasin
│   ├── UpdateStoreInputDTO.php    # DTO mise à jour magasin
│   ├── ListStoresFilterDTO.php    # DTO filtres liste magasins
│   └── AttachUsersToStoreDTO.php  # DTO association utilisateurs
├── Enumeration/
│   └── StoreStatusEnum.php        # Statuts (active, draft, unpublish)
└── Services/
    ├── CreateStoreService.php     # Création magasin
    ├── UpdateStoreService.php     # Mise à jour magasin
    ├── DeleteStoreService.php     # Suppression (soft delete)
    ├── ListStoresService.php      # Liste paginée avec filtres
    ├── GetStoreService.php        # Récupération par ID
    ├── AttachUsersToStoreService.php   # Association utilisateurs
    └── DetachUserFromStoreService.php  # Dissociation utilisateur
```

## Table stores

| Colonne | Type | Description |
|---------|------|-------------|
| id | bigint | Clé primaire |
| name | string | Nom du magasin |
| banner_stored_file_id | FK nullable | Image bannière |
| logo_stored_file_id | FK nullable | Logo |
| created_by | FK | Admin créateur |
| address_id | FK nullable | Adresse du magasin |
| status | enum | Statut (active/draft/unpublish) |
| created_at | timestamp | Date de création |
| updated_at | timestamp | Date de modification |
| deleted_at | timestamp nullable | Soft delete |

## Table pivot store_user

| Colonne | Type | Description |
|---------|------|-------------|
| id | bigint | Clé primaire |
| store_id | FK | Référence au magasin |
| user_id | FK | Référence à l'utilisateur |
| created_at | timestamp | Date d'association |

## Table pivot product_store

Relation many-to-many entre les stores et les produits.

| Colonne | Type | Description |
|---------|------|-------------|
| id | bigint | Clé primaire |
| product_id | FK | Référence au produit |
| store_id | FK | Référence au store |
| created_at | timestamp | Date d'association |
| updated_at | timestamp | Date de modification |

## Table store_product_variant

Gestion du stock des variants par store.

| Colonne | Type | Description |
|---------|------|-------------|
| id | bigint | Clé primaire |
| store_id | FK | Référence au store |
| product_variant_id | FK | Référence au variant |
| stock | integer nullable | Quantité (null = illimité) |
| created_at | timestamp | Date de création |
| updated_at | timestamp | Date de modification |

## Statuts

| Statut | Valeur | Description |
|--------|--------|-------------|
| ACTIVE | `active` | Magasin visible aux utilisateurs associés |
| DRAFT | `draft` | Magasin en brouillon, non visible |
| UNPUBLISH | `unpublish` | Magasin désactivé, accès restreint |

## Relations du modèle Store

```php
// Utilisateurs associés au store
$store->users; // BelongsToMany User

// Produits vendus dans le store
$store->products; // BelongsToMany Product

// Variants disponibles dans le store (avec stock)
$store->productVariants; // BelongsToMany ProductVariant (via store_product_variant)

// Créateur du store
$store->creator; // BelongsTo User

// Adresse du store
$store->address; // BelongsTo Address

// Bannière et logo
$store->banner; // BelongsTo StoredFile
$store->logo; // BelongsTo StoredFile
```

## Règles Métier

### Association Store ↔ Users

| Rôle | Règle |
|------|-------|
| Admin | Accès global, ne peut PAS être associé à un store |
| Manager | Peut être associé à PLUSIEURS stores |
| Employer | Peut être associé à UN SEUL store |

### Association Store ↔ Products

- Un produit peut être vendu dans plusieurs stores
- Un store peut vendre plusieurs produits
- La gestion des associations produit-store se fait via `ProductStoreController`
- Seuls les admins peuvent associer/dissocier des produits aux stores

### Stock par Store

- Le stock est géré par variant ET par store
- `stock = null` signifie stock illimité
- Quand un produit est retiré d'un store, les stocks des variants sont également supprimés

### Visibilité des stores

| Rôle | Visibilité |
|------|------------|
| Admin | Tous les stores (tous statuts) |
| Manager | Stores associés avec status ACTIVE uniquement |
| Employer | Store associé avec status ACTIVE uniquement |

### Création et modification

- Seuls les Admin peuvent créer/modifier/supprimer des stores
- Le champ `created_by` est automatiquement rempli avec l'ID de l'admin créateur

## Endpoints API

### Routes Admin (`/api/admin/stores`)

| Méthode | Route | Action | Description |
|---------|-------|--------|-------------|
| GET | `/` | index | Liste tous les stores |
| POST | `/` | store | Créer un store |
| GET | `/{store}` | show | Détail d'un store |
| PUT | `/{store}` | update | Modifier un store |
| DELETE | `/{store}` | destroy | Supprimer un store |
| POST | `/{store}/users` | attachUsers | Associer des utilisateurs |
| DELETE | `/{store}/users/{user}` | detachUser | Dissocier un utilisateur |

### Routes authentifiées (`/api/stores`)

| Méthode | Route | Action | Description |
|---------|-------|--------|-------------|
| GET | `/` | indexAccessible | Liste les stores accessibles |
| GET | `/{store}` | showAccessible | Détail si accessible |

### Routes Produits-Store (`/api/admin/products/{product}/stores`)

| Méthode | Route | Action | Description |
|---------|-------|--------|-------------|
| GET | `/` | indexStores | Liste des stores du produit |
| POST | `/` | attachStores | Associer des stores au produit |
| DELETE | `/{store}` | detachStore | Retirer un store du produit |

## Paramètres de requête (liste)

| Paramètre | Type | Description |
|-----------|------|-------------|
| `search` | string | Recherche par nom |
| `status` | string | Filtrer par statut (active, draft, unpublish) |
| `with_trashed` | boolean | Inclure les stores supprimés (admin) |
| `per_page` | integer | Nombre d'éléments par page (max: 100) |

## Exemples de réponses

### Création d'un store (201)

```json
{
  "success": true,
  "message": "Magasin créé avec succès.",
  "data": {
    "id": 1,
    "name": "Café Central",
    "status": "draft",
    "status_label": "Brouillon",
    "banner": null,
    "logo": null,
    "address": null,
    "creator": {
      "id": 1,
      "name": "Admin",
      "email": "admin@cafe.local"
    },
    "users": [],
    "is_deleted": false,
    "created_at": "2026-01-20 12:00:00",
    "updated_at": "2026-01-20 12:00:00"
  }
}
```

### Liste des stores accessibles

```json
{
  "success": true,
  "message": "Liste des magasins récupérée avec succès.",
  "data": [
    {
      "id": 1,
      "name": "Café Central",
      "status": "active",
      "status_label": "Actif",
      "banner": { ... },
      "logo": { ... },
      "address": { ... },
      "creator": { ... },
      "is_deleted": false,
      "created_at": "2026-01-20 12:00:00",
      "updated_at": "2026-01-20 12:00:00"
    }
  ],
  "paging": {
    "current_page": 1,
    "per_page": 15,
    "total": 1,
    "last_page": 1
  }
}
```

### Association d'utilisateurs

**Requête:**
```json
{
  "user_ids": [2, 3]
}
```

**Réponse:**
```json
{
  "success": true,
  "message": "Utilisateur(s) associé(s) au magasin avec succès.",
  "data": {
    "id": 1,
    "name": "Café Central",
    "users": [
      { "id": 2, "name": "Manager", "roles": ["manager"] },
      { "id": 3, "name": "Employer", "roles": ["employer"] }
    ]
  }
}
```

### Erreurs possibles

| Code | Message | Description |
|------|---------|-------------|
| 403 | Un administrateur ne peut pas être associé à un magasin | Tentative d'associer un admin |
| 403 | Un employé ne peut être associé qu'à un seul magasin | Employer déjà associé ailleurs |
| 403 | Vous n'avez pas accès à ce magasin | Accès non autorisé |
| 422 | Utilisateur non associé à ce magasin | Tentative de dissocier un user non lié |

## Utilisation dans le Code

### Créer un store

```php
use App\Domain\Store\DTOs\CreateStoreInputDTO;
use App\Domain\Store\Services\CreateStoreService;

$dto = CreateStoreInputDTO::fromArray([
    'name' => 'Mon Café',
    'status' => 'draft',
    'address_id' => 1,
]);

$service = app(CreateStoreService::class);
$store = $service->execute($dto, $admin);
```

### Lister les stores accessibles

```php
use App\Domain\Store\DTOs\ListStoresFilterDTO;
use App\Domain\Store\Services\ListStoresService;

$filters = ListStoresFilterDTO::fromArray([
    'search' => 'central',
    'status' => 'active',
    'per_page' => 20,
]);

$service = app(ListStoresService::class);
$stores = $service->execute($filters, $currentUser);
```

### Associer des utilisateurs

```php
use App\Domain\Store\DTOs\AttachUsersToStoreDTO;
use App\Domain\Store\Services\AttachUsersToStoreService;

$dto = AttachUsersToStoreDTO::fromArray([
    'user_ids' => [2, 3],
]);

$service = app(AttachUsersToStoreService::class);
$store = $service->execute($store, $dto);
```

### Vérifier l'accès à un store

```php
// Via le modèle
if ($store->isAccessibleBy($user)) {
    // L'utilisateur peut accéder au store
}

// Via la Policy
Gate::authorize('view', $store);
```

### Accéder aux produits d'un store

```php
// Tous les produits du store
$products = $store->products;

// Produits actifs
$activeProducts = $store->products()->where('is_active', true)->get();

// Produits avec leurs variants et stocks
$store->load(['products.variants.storeStocks' => function ($query) use ($store) {
    $query->where('store_id', $store->id);
}]);
```

### Accéder aux stocks des variants d'un store

```php
// Tous les variants avec leur stock dans ce store
$variants = $store->productVariants;

foreach ($variants as $variant) {
    $stock = $variant->pivot->stock; // null = illimité
}

// Ou via le modèle StoreProductVariant
use App\Models\StoreProductVariant;

$stocks = StoreProductVariant::where('store_id', $store->id)
    ->with(['variant.product'])
    ->get();
```

## Seeders

### StoreSeeder

Crée deux magasins de démonstration :

| Magasin | Status | Utilisateurs associés |
|---------|--------|----------------------|
| Café Central | active | Manager, Employer |
| Café du Parc | draft | Manager |

### Exécution

```bash
php artisan db:seed --class=StoreSeeder
```

## Voir aussi

- [STORE_SERVICES.md](STORE_SERVICES.md) - Documentation des services
- [STORE_ENUMERATION.md](STORE_ENUMERATION.md) - Documentation du StoreStatusEnum
- [PRODUCT_README.md](../Product/PRODUCT_README.md) - Documentation des produits
