<?php

namespace App\Domain\ProductCategory\DTOs;

readonly class UpdateProductCategoryInputDTO
{
    public function __construct(
        public ?string $name = null,
        public ?string $description = null,
        public ?int $parentId = null,
        public ?bool $isActive = null,
        public bool $clearParent = false,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            description: $data['description'] ?? null,
            parentId: $data['parent_id'] ?? null,
            isActive: $data['is_active'] ?? null,
            clearParent: array_key_exists('parent_id', $data) && $data['parent_id'] === null,
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

        if ($this->parentId !== null || $this->clearParent) {
            $data['parent_id'] = $this->parentId;
        }

        if ($this->isActive !== null) {
            $data['is_active'] = $this->isActive;
        }

        return $data;
    }
}
