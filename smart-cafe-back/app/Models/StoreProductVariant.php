<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modèle pivot pour le stock des variants par store.
 *
 * @property int $id
 * @property int $store_id
 * @property int $product_variant_id
 * @property int|null $stock Stock disponible (null = illimité)
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read Store $store
 * @property-read ProductVariant $variant
 */
class StoreProductVariant extends Model
{
    protected $table = 'store_product_variant';

    protected $fillable = [
        'store_id',
        'product_variant_id',
        'stock',
    ];

    protected $casts = [
        'stock' => 'integer',
    ];

    /**
     * Le store associé.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Le variant associé.
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /**
     * Vérifie si le stock est illimité.
     */
    public function isUnlimited(): bool
    {
        return $this->stock === null;
    }

    /**
     * Vérifie si le variant est en stock pour ce store.
     */
    public function isInStock(): bool
    {
        return $this->isUnlimited() || $this->stock > 0;
    }

    /**
     * Décrémente le stock si possible.
     *
     * @param  int  $quantity  Quantité à décrémenter
     * @return bool True si le stock a été décrémenté, false sinon
     */
    public function decrementStock(int $quantity = 1): bool
    {
        if ($this->isUnlimited()) {
            return true;
        }

        if ($this->stock < $quantity) {
            return false;
        }

        $this->decrement('stock', $quantity);

        return true;
    }

    /**
     * Incrémente le stock.
     *
     * @param  int  $quantity  Quantité à incrémenter
     */
    public function incrementStock(int $quantity = 1): void
    {
        if (! $this->isUnlimited()) {
            $this->increment('stock', $quantity);
        }
    }
}
