<?php

namespace App\Domain\Product\DTOs;

readonly class CreateProductInputDTO
{
    /**
     * @param  array<int>  $storeIds  Les IDs des stores où le produit sera vendu
     */
    public function __construct(
        public string $name,
        public int $productCategoryId,
        public array $storeIds = [],
        public ?string $description = null,
        public bool $isActive = true,
        public bool $isFeatured = false,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            productCategoryId: (int) $data['product_category_id'],
            storeIds: $data['store_ids'] ?? [],
            description: $data['description'] ?? null,
            isActive: $data['is_active'] ?? true,
            isFeatured: $data['is_featured'] ?? false,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'product_category_id' => $this->productCategoryId,
            'store_ids' => $this->storeIds,
            'description' => $this->description,
            'is_active' => $this->isActive,
            'is_featured' => $this->isFeatured,
        ];
    }
}
