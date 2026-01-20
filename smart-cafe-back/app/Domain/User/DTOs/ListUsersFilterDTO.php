<?php

namespace App\Domain\User\DTOs;

use App\Domain\User\Constant\UserConstant;
use App\Domain\User\Enumeration\UserRoleEnum;

readonly class ListUsersFilterDTO
{
    public function __construct(
        public ?string $search = null,
        public ?UserRoleEnum $role = null,
        public bool $withTrashed = false,
        public int $perPage = UserConstant::DEFAULT_PER_PAGE,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $role = isset($data['role']) ? UserRoleEnum::tryFrom($data['role']) : null;

        return new self(
            search: $data['search'] ?? null,
            role: $role,
            withTrashed: filter_var($data['with_trashed'] ?? false, FILTER_VALIDATE_BOOLEAN),
            perPage: min(
                (int) ($data['per_page'] ?? UserConstant::DEFAULT_PER_PAGE),
                UserConstant::MAX_PER_PAGE
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
            'role' => $this->role?->value,
            'with_trashed' => $this->withTrashed,
            'per_page' => $this->perPage,
        ];
    }
}
