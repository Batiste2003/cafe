<?php

namespace App\Domain\ProductCategory\DTOs;

readonly class CreateProductCategoryInputDTO
{
    public function __construct(
        public string $name,
        public ?string $description = null,
        public ?int $parentId = null,
        public bool $isActive = true,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            description: $data['description'] ?? null,
            parentId: $data['parent_id'] ?? null,
            isActive: $data['is_active'] ?? true,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'parent_id' => $this->parentId,
            'is_active' => $this->isActive,
        ];
    }
}
