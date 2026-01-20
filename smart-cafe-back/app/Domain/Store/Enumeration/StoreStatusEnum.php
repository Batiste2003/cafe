<?php

namespace App\Domain\Store\Enumeration;

enum StoreStatusEnum: string
{
    case ACTIVE = 'active';
    case DRAFT = 'draft';
    case UNPUBLISH = 'unpublish';

    /**
     * Returns the human-readable label for the status.
     */
    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Actif',
            self::DRAFT => 'Brouillon',
            self::UNPUBLISH => 'Non publié',
        };
    }

    /**
     * Checks if the store is visible to non-admin users.
     */
    public function isVisible(): bool
    {
        return $this === self::ACTIVE;
    }

    /**
     * Checks if the store is in draft mode.
     */
    public function isDraft(): bool
    {
        return $this === self::DRAFT;
    }

    /**
     * Checks if the store is unpublished.
     */
    public function isUnpublished(): bool
    {
        return $this === self::UNPUBLISH;
    }

    /**
     * Returns all status values as an array.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
