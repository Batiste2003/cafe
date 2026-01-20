<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'product_id',
        'sku',
        'price_cent_ht',
        'cost_price_cent_ht',
        'is_default',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price_cent_ht' => 'integer',
            'cost_price_cent_ht' => 'integer',
            'is_default' => 'boolean',
        ];
    }

    /**
     * Get the product that owns the variant.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the gallery images for the variant.
     */
    public function gallery(): BelongsToMany
    {
        return $this->belongsToMany(StoredFile::class, 'product_variant_gallery')
            ->withPivot('position')
            ->withTimestamps()
            ->orderByPivot('position');
    }

    /**
     * Get the option values for this variant.
     */
    public function optionValues(): BelongsToMany
    {
        return $this->belongsToMany(ProductOptionValue::class, 'variant_option_values');
    }

    /**
     * Get the store stocks for this variant.
     */
    public function storeStocks(): HasMany
    {
        return $this->hasMany(StoreProductVariant::class);
    }

    /**
     * Get the stores that have this variant in stock.
     */
    public function stores(): BelongsToMany
    {
        return $this->belongsToMany(Store::class, 'store_product_variant')
            ->withPivot('stock')
            ->withTimestamps();
    }

    /**
     * Scope a query to only include default variants.
     */
    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope a query to filter by product.
     */
    public function scopeForProduct(Builder $query, int $productId): Builder
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Get the price in euros (formatted).
     */
    public function getPriceEurosAttribute(): float
    {
        return $this->price_cent_ht / 100;
    }

    /**
     * Get the cost price in euros (formatted).
     */
    public function getCostPriceEurosAttribute(): ?float
    {
        return $this->cost_price_cent_ht !== null ? $this->cost_price_cent_ht / 100 : null;
    }

    /**
     * Get the stock for a specific store.
     *
     * @param  Store  $store  Le store concerné
     * @return int|null Le stock (null = illimité)
     */
    public function getStockForStore(Store $store): ?int
    {
        $storeStock = $this->storeStocks()->where('store_id', $store->id)->first();

        return $storeStock?->stock;
    }

    /**
     * Check if the variant is in stock for a specific store.
     *
     * @param  Store  $store  Le store concerné
     * @return bool True si en stock ou illimité
     */
    public function isInStockForStore(Store $store): bool
    {
        $storeStock = $this->storeStocks()->where('store_id', $store->id)->first();

        if (! $storeStock) {
            return false; // Le variant n'est pas disponible dans ce store
        }

        return $storeStock->isInStock();
    }

    /**
     * Check if the variant is available in a specific store.
     *
     * @param  Store  $store  Le store concerné
     * @return bool True si le variant est disponible dans ce store
     */
    public function isAvailableInStore(Store $store): bool
    {
        return $this->storeStocks()->where('store_id', $store->id)->exists();
    }
}
