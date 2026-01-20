<?php

namespace App\Domain\Store\DTOs;

use App\Domain\Store\Constant\StoreConstant;
use App\Domain\Store\Enumeration\StoreStatusEnum;

readonly class ListStoresFilterDTO
{
    public function __construct(
        public ?string $search = null,
        public ?StoreStatusEnum $status = null,
        public bool $withTrashed = false,
        public int $perPage = StoreConstant::DEFAULT_PER_PAGE,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $status = isset($data['status']) ? StoreStatusEnum::tryFrom($data['status']) : null;

        return new self(
            search: $data['search'] ?? null,
            status: $status,
            withTrashed: filter_var($data['with_trashed'] ?? false, FILTER_VALIDATE_BOOLEAN),
            perPage: min(
                (int) ($data['per_page'] ?? StoreConstant::DEFAULT_PER_PAGE),
                StoreConstant::MAX_PER_PAGE
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
            'status' => $this->status?->value,
            'with_trashed' => $this->withTrashed,
            'per_page' => $this->perPage,
        ];
    }
}
