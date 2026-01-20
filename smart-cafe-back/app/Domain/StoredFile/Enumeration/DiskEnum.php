<?php

namespace App\Domain\StoredFile\Enumeration;

enum DiskEnum: string
{
    case LOCAL = 'local';
    case PUBLIC = 'public';
    case S3 = 's3';

    /**
     * Get a human-readable label for the disk.
     */
    public function label(): string
    {
        return match ($this) {
            self::LOCAL => 'Stockage local privé',
            self::PUBLIC => 'Stockage public',
            self::S3 => 'Amazon S3',
        };
    }

    /**
     * Check if the disk is publicly accessible.
     */
    public function isPublic(): bool
    {
        return $this === self::PUBLIC || $this === self::S3;
    }

    /**
     * Check if the disk is local storage.
     */
    public function isLocal(): bool
    {
        return $this === self::LOCAL || $this === self::PUBLIC;
    }

    /**
     * Get the base path for the disk.
     */
    public function basePath(): string
    {
        return match ($this) {
            self::LOCAL => storage_path('app/private'),
            self::PUBLIC => storage_path('app/public'),
            self::S3 => '',
        };
    }
}
