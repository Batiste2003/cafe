<?php

namespace App\Domain\Store\DTOs;

readonly class AttachUsersToStoreDTO
{
    /**
     * @param  array<int>  $userIds
     */
    public function __construct(
        public array $userIds,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            userIds: $data['user_ids'],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'user_ids' => $this->userIds,
        ];
    }
}
