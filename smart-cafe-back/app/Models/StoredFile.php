<?php

namespace App\Models;

use App\Domain\StoredFile\Enumeration\DiskEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class StoredFile extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'disk',
        'path',
        'full_path',
        'hash',
        'original_name',
        'mime_type',
        'size',
        'extension',
        'user_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'full_path',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'disk' => DiskEnum::class,
            'size' => 'integer',
        ];
    }

    /**
     * Get the user that owns the file.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the public URL for the file (if on public disk).
     */
    public function getUrlAttribute(): ?string
    {
        if ($this->disk === DiskEnum::PUBLIC) {
            return Storage::disk('public')->url($this->path);
        }

        if ($this->disk === DiskEnum::S3) {
            return Storage::disk('s3')->url($this->path);
        }

        return null;
    }

    /**
     * Check if the file exists on disk.
     */
    public function existsOnDisk(): bool
    {
        return Storage::disk($this->disk->value)->exists($this->path);
    }

    /**
     * Scope to find files by hash.
     */
    public function scopeByHash($query, string $hash)
    {
        return $query->where('hash', $hash);
    }

    /**
     * Scope to find active (non-deleted) files by hash.
     */
    public function scopeActiveByHash($query, string $hash)
    {
        return $query->where('hash', $hash)->whereNull('deleted_at');
    }

    /**
     * Get the count of active records with the same hash.
     */
    public static function countByHash(string $hash): int
    {
        return self::where('hash', $hash)->whereNull('deleted_at')->count();
    }
}
