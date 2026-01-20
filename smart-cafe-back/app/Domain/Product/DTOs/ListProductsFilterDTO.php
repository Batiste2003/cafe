<?php

namespace App\Domain\Product\DTOs;

use App\Domain\Product\Constant\ProductConstant;

readonly class ListProductsFilterDTO
{
    public function __construct(
        public ?string $search = null,
        public ?int $storeId = null,
        public ?int $categoryId = null,
        public ?bool $isActive = null,
        public ?bool $isFeatured = null,
        public bool $withTrashed = false,
        public int $perPage = ProductConstant::DEFAULT_PER_PAGE,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            search: $data['search'] ?? null,
            storeId: isset($data['store_id']) ? (int) $data['store_id'] : null,
            categoryId: isset($data['category_id']) ? (int) $data['category_id'] : null,
            isActive: isset($data['is_active']) ? filter_var($data['is_active'], FILTER_VALIDATE_BOOLEAN) : null,
            isFeatured: isset($data['is_featured']) ? filter_var($data['is_featured'], FILTER_VALIDATE_BOOLEAN) : null,
            withTrashed: filter_var($data['with_trashed'] ?? false, FILTER_VALIDATE_BOOLEAN),
            perPage: min(
                (int) ($data['per_page'] ?? ProductConstant::DEFAULT_PER_PAGE),
                ProductConstant::MAX_PER_PAGE
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
            'store_id' => $this->storeId,
            'category_id' => $this->categoryId,
            'is_active' => $this->isActive,
            'is_featured' => $this->isFeatured,
            'with_trashed' => $this->withTrashed,
            'per_page' => $this->perPage,
        ];
    }
}
