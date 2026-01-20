<?php

namespace App\Domain\Product\DTOs;

readonly class UpdateProductInputDTO
{
    public function __construct(
        public ?string $name = null,
        public ?string $description = null,
        public ?int $productCategoryId = null,
        public ?bool $isActive = null,
        public ?bool $isFeatured = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            description: $data['description'] ?? null,
            productCategoryId: isset($data['product_category_id']) ? (int) $data['product_category_id'] : null,
            isActive: $data['is_active'] ?? null,
            isFeatured: $data['is_featured'] ?? null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [];

        if ($this->name !== null) {
            $data['name'] = $this->name;
        }

        if ($this->description !== null) {
            $data['description'] = $this->description;
        }

        if ($this->productCategoryId !== null) {
            $data['product_category_id'] = $this->productCategoryId;
        }

        if ($this->isActive !== null) {
            $data['is_active'] = $this->isActive;
        }

        if ($this->isFeatured !== null) {
            $data['is_featured'] = $this->isFeatured;
        }

        return $data;
    }
}
