<?php

namespace App\Models;

use App\Domain\Store\Enumeration\StoreStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'banner_stored_file_id',
        'logo_stored_file_id',
        'created_by',
        'address_id',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => StoreStatusEnum::class,
        ];
    }

    /**
     * Get the banner image for the store.
     */
    public function banner(): BelongsTo
    {
        return $this->belongsTo(StoredFile::class, 'banner_stored_file_id');
    }

    /**
     * Get the logo image for the store.
     */
    public function logo(): BelongsTo
    {
        return $this->belongsTo(StoredFile::class, 'logo_stored_file_id');
    }

    /**
     * Get the user who created the store.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the address for the store.
     */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * Get the users associated with the store.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'store_user')
            ->withPivot('created_at');
    }

    /**
     * Get the products sold in this store.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_store')
            ->withTimestamps();
    }

    /**
     * Get the product variants with their stock for this store.
     */
    public function productVariants(): BelongsToMany
    {
        return $this->belongsToMany(ProductVariant::class, 'store_product_variant')
            ->withPivot('stock')
            ->withTimestamps();
    }

    /**
     * Scope a query to only include active stores.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', StoreStatusEnum::ACTIVE->value);
    }

    /**
     * Scope a query to only include stores associated with a user.
     */
    public function scopeForUser(Builder $query, User $user): Builder
    {
        if ($user->isAdmin()) {
            return $query;
        }

        return $query->whereHas('users', function (Builder $q) use ($user) {
            $q->where('users.id', $user->id);
        });
    }

    /**
     * Scope a query to only include stores accessible by a user.
     * Admin can access all stores, others can only access active stores they are associated with.
     */
    public function scopeAccessibleBy(Builder $query, User $user): Builder
    {
        if ($user->isAdmin()) {
            return $query;
        }

        return $query->where('status', StoreStatusEnum::ACTIVE->value)
            ->whereHas('users', function (Builder $q) use ($user) {
                $q->where('users.id', $user->id);
            });
    }

    /**
     * Check if the store is accessible by a given user.
     */
    public function isAccessibleBy(User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($this->status !== StoreStatusEnum::ACTIVE) {
            return false;
        }

        return $this->users()->where('users.id', $user->id)->exists();
    }
}
