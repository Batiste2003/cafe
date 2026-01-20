<?php

namespace App\Domain\Product\DTOs;

/**
 * DTO pour l'association d'un produit à des stores.
 */
readonly class AttachProductToStoresInputDTO
{
    /**
     * @param  array<int>  $storeIds  Les IDs des stores à associer
     */
    public function __construct(
        public array $storeIds,
    ) {}

    /**
     * Crée une instance depuis un tableau.
     *
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            storeIds: $data['store_ids'] ?? [],
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
            'store_ids' => $this->storeIds,
        ];
    }
}
