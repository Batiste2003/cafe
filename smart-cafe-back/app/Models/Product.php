<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'product_category_id',
        'created_by',
        'is_active',
        'is_featured',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    /**
     * Get the category of the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    /**
     * Get the stores that sell this product.
     */
    public function stores(): BelongsToMany
    {
        return $this->belongsToMany(Store::class, 'product_store')
            ->withTimestamps();
    }

    /**
     * Get the user who created the product.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the variants for the product.
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Get the options for the product.
     */
    public function options(): HasMany
    {
        return $this->hasMany(ProductOption::class);
    }

    /**
     * Get the gallery images for the product.
     */
    public function gallery(): BelongsToMany
    {
        return $this->belongsToMany(StoredFile::class, 'product_gallery')
            ->withPivot('position')
            ->withTimestamps()
            ->orderByPivot('position');
    }

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include featured products.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to filter by store (via pivot table).
     */
    public function scopeForStore(Builder $query, int $storeId): Builder
    {
        return $query->whereHas('stores', function (Builder $q) use ($storeId) {
            $q->where('stores.id', $storeId);
        });
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeForCategory(Builder $query, int $categoryId): Builder
    {
        return $query->where('product_category_id', $categoryId);
    }

    /**
     * Scope a query to only include products accessible by a user.
     * Admin can access all products, others can only access active products from their stores.
     */
    public function scopeAccessibleBy(Builder $query, User $user): Builder
    {
        if ($user->isAdmin()) {
            return $query;
        }

        $storeIds = $user->stores()->pluck('stores.id');

        return $query->where('is_active', true)
            ->whereHas('stores', function (Builder $q) use ($storeIds) {
                $q->whereIn('stores.id', $storeIds);
            });
    }

    /**
     * Get the default variant.
     */
    public function defaultVariant(): ?ProductVariant
    {
        return $this->variants()->where('is_default', true)->first();
    }

    /**
     * Check if the product is accessible by a given user.
     */
    public function isAccessibleBy(User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if (! $this->is_active) {
            return false;
        }

        $userStoreIds = $user->stores()->pluck('stores.id')->toArray();
        $productStoreIds = $this->stores()->pluck('stores.id')->toArray();

        return ! empty(array_intersect($userStoreIds, $productStoreIds));
    }

    /**
     * Check if the product is sold in a specific store.
     */
    public function isSoldInStore(Store $store): bool
    {
        return $this->stores()->where('stores.id', $store->id)->exists();
    }
}
