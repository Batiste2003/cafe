# Modèles Product

## Product

```php
namespace App\Models;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'product_category_id',
        'store_id',
        'created_by',
        'is_active',
        'is_featured',
    ];

    // Relations
    public function category(): BelongsTo;      // ProductCategory
    public function store(): BelongsTo;         // Store
    public function creator(): BelongsTo;       // User
    public function variants(): HasMany;        // ProductVariant[]
    public function options(): HasMany;         // ProductOption[]
    public function gallery(): BelongsToMany;   // StoredFile[] (pivot: product_gallery)

    // Scopes
    public function scopeActive($query);
    public function scopeFeatured($query);
    public function scopeAccessibleBy($query, User $user);  // Filtre par store
}
```

## ProductVariant

```php
namespace App\Models;

class ProductVariant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'sku',
        'price_cent_ht',
        'cost_price_cent_ht',
        'stock',
        'is_default',
    ];

    // Relations
    public function product(): BelongsTo;           // Product
    public function gallery(): BelongsToMany;       // StoredFile[] (pivot: product_variant_gallery)
    public function optionValues(): BelongsToMany;  // ProductOptionValue[] (pivot: variant_option_values)

    // Accesseurs
    public function getPriceEurosAttribute(): float;      // price_cent_ht / 100
    public function getCostPriceEurosAttribute(): ?float; // cost_price_cent_ht / 100

    // Méthodes
    public function isInStock(): bool;  // stock > 0
}
```

## ProductOption

```php
namespace App\Models;

class ProductOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'is_required',
    ];

    // Relations
    public function product(): BelongsTo;  // Product
    public function values(): HasMany;     // ProductOptionValue[]
}
```

## ProductOptionValue

```php
namespace App\Models;

class ProductOptionValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_option_id',
        'value',
        'price_add_ht',
    ];

    // Relations
    public function option(): BelongsTo;        // ProductOption
    public function variants(): BelongsToMany;  // ProductVariant[] (pivot: variant_option_values)

    // Accesseurs
    public function getPriceAddEurosAttribute(): float;  // price_add_ht / 100
}
```

## Tables de base de données

### products

| Colonne | Type | Description |
|---------|------|-------------|
| id | bigint | PK |
| name | string | Nom du produit |
| slug | string unique | Slug URL |
| description | text nullable | Description |
| product_category_id | FK | Catégorie |
| store_id | FK | Store propriétaire |
| created_by | FK | Admin créateur |
| is_active | boolean | Produit actif |
| is_featured | boolean | Mis en avant |
| created_at | timestamp | |
| updated_at | timestamp | |
| deleted_at | timestamp | Soft delete |

### product_variants

| Colonne | Type | Description |
|---------|------|-------------|
| id | bigint | PK |
| product_id | FK | Produit parent |
| sku | string unique | SKU |
| price_cent_ht | integer | Prix HT en centimes |
| cost_price_cent_ht | integer nullable | Prix de revient |
| stock | integer | Stock disponible |
| is_default | boolean | Variant par défaut |
| created_at | timestamp | |
| updated_at | timestamp | |
| deleted_at | timestamp | Soft delete |

### product_options

| Colonne | Type | Description |
|---------|------|-------------|
| id | bigint | PK |
| product_id | FK | Produit |
| name | string | Nom de l'option |
| is_required | boolean | Obligatoire |
| created_at | timestamp | |
| updated_at | timestamp | |

### product_option_values

| Colonne | Type | Description |
|---------|------|-------------|
| id | bigint | PK |
| product_option_id | FK | Option parente |
| value | string | Valeur |
| price_add_ht | integer | Supplément en centimes |
| created_at | timestamp | |
| updated_at | timestamp | |

### Tables pivot

**product_gallery**
- `product_id` (FK)
- `stored_file_id` (FK)
- `position` (int)

**product_variant_gallery**
- `product_variant_id` (FK)
- `stored_file_id` (FK)
- `position` (int)

**variant_option_values**
- `product_variant_id` (FK)
- `product_option_value_id` (FK)
