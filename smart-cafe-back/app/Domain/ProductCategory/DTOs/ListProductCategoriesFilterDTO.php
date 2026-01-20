<?php

namespace App\Domain\ProductCategory\DTOs;

use App\Domain\ProductCategory\Constant\ProductCategoryConstant;

readonly class ListProductCategoriesFilterDTO
{
    public function __construct(
        public ?string $search = null,
        public ?bool $isActive = null,
        public ?int $parentId = null,
        public bool $onlyRoot = false,
        public bool $withTrashed = false,
        public int $perPage = ProductCategoryConstant::DEFAULT_PER_PAGE,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            search: $data['search'] ?? null,
            isActive: isset($data['is_active']) ? filter_var($data['is_active'], FILTER_VALIDATE_BOOLEAN) : null,
            parentId: isset($data['parent_id']) ? (int) $data['parent_id'] : null,
            onlyRoot: filter_var($data['only_root'] ?? false, FILTER_VALIDATE_BOOLEAN),
            withTrashed: filter_var($data['with_trashed'] ?? false, FILTER_VALIDATE_BOOLEAN),
            perPage: min(
                (int) ($data['per_page'] ?? ProductCategoryConstant::DEFAULT_PER_PAGE),
                ProductCategoryConstant::MAX_PER_PAGE
            ),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'search' => $this->search,
            'is_active' => $this->isActive,
            'parent_id' => $this->parentId,
            'only_root' => $this->onlyRoot,
            'with_trashed' => $this->withTrashed,
            'per_page' => $this->perPage,
        ];
    }
}
