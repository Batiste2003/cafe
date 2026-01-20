# Module ProductCategory

## Vue d'ensemble

Le module ProductCategory gère les catégories de produits avec support de hiérarchie parent/enfant.

## Structure

```
app/
├── Domain/ProductCategory/
│   ├── Constant/
│   │   └── ProductCategoryConstant.php
│   ├── DTOs/
│   │   ├── CreateProductCategoryInputDTO.php
│   │   ├── UpdateProductCategoryInputDTO.php
│   │   └── ListProductCategoriesFilterDTO.php
│   └── Services/
│       ├── CreateProductCategoryService.php
│       ├── UpdateProductCategoryService.php
│       ├── DeleteProductCategoryService.php
│       ├── ListProductCategoriesService.php
│       └── GetProductCategoryService.php
├── Models/
│   └── ProductCategory.php
├── Http/
│   ├── Controllers/
│   │   └── ProductCategoryController.php
│   ├── Requests/
│   │   ├── StoreProductCategoryRequest.php
│   │   └── UpdateProductCategoryRequest.php
│   └── Resources/
│       └── ProductCategoryResource.php
└── Policies/
    └── ProductCategoryPolicy.php
```

## Modèle ProductCategory

```php
class ProductCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'is_active',
    ];

    // Relations
    public function parent(): BelongsTo;    // ProductCategory (parent)
    public function children(): HasMany;     // ProductCategory[] (sous-catégories)
    public function products(): HasMany;     // Product[]

    // Méthodes
    public function ancestors(): Collection;    // Tous les parents
    public function descendants(): Collection;  // Tous les enfants récursifs
    public function isRootCategory(): bool;     // N'a pas de parent
}
```

## Hiérarchie

Les catégories supportent une structure hiérarchique :

```
Boissons chaudes (parent)
├── Cafés
├── Thés
└── Chocolats

Pâtisseries (parent)
├── Viennoiseries
├── Gâteaux
└── Cookies
```

## Table de base de données

### product_categories

| Colonne | Type | Description |
|---------|------|-------------|
| id | bigint | PK |
| name | string | Nom de la catégorie |
| slug | string unique | Slug URL |
| description | text nullable | Description |
| parent_id | FK nullable | Catégorie parente |
| is_active | boolean | Catégorie active |
| created_at | timestamp | |
| updated_at | timestamp | |
| deleted_at | timestamp | Soft delete |

## Permissions

| Rôle | Permissions |
|------|-------------|
| Admin | CRUD complet |
| Manager | Lecture seule |
| Employer | Lecture seule |

## Routes API

### Admin

```
GET    /api/admin/product-categories           Liste des catégories
POST   /api/admin/product-categories           Créer une catégorie
GET    /api/admin/product-categories/{id}      Détail
PUT    /api/admin/product-categories/{id}      Modifier
DELETE /api/admin/product-categories/{id}      Supprimer
```

### Authentifié

```
GET    /api/product-categories                 Liste catégories actives
GET    /api/product-categories/{id}            Détail
```

## Validation

- Le nom doit être unique (incluant les supprimés)
- Le slug est généré automatiquement depuis le nom
- Une catégorie ne peut pas être son propre parent
- Une catégorie ne peut pas avoir un de ses descendants comme parent
