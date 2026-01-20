<?php

namespace App\Domain\Product\DTOs;

/**
 * DTO pour la mise à jour du stock d'un variant dans un store.
 */
readonly class UpdateVariantStockInputDTO
{
    /**
     * @param  int|null  $stock  Le stock (null = illimité)
     */
    public function __construct(
        public ?int $stock,
    ) {}

    /**
     * Crée une instance depuis un tableau.
     *
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            stock: $data['stock'] ?? null,
        );
    }

    /**
     * Convertit en tableau.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'stock' => $this->stock,
        ];
    }

    /**
     * Vérifie si le stock est illimité.
     */
    public function isUnlimited(): bool
    {
        return $this->stock === null;
    }
}
