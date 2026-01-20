<?php

namespace App\Domain\Store\DTOs;

use App\Domain\Store\Enumeration\StoreStatusEnum;

readonly class CreateStoreInputDTO
{
    public function __construct(
        public string $name,
        public ?int $bannerStoredFileId = null,
        public ?int $logoStoredFileId = null,
        public ?int $addressId = null,
        public StoreStatusEnum $status = StoreStatusEnum::DRAFT,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            bannerStoredFileId: $data['banner_stored_file_id'] ?? null,
            logoStoredFileId: $data['logo_stored_file_id'] ?? null,
            addressId: $data['address_id'] ?? null,
            status: isset($data['status']) ? StoreStatusEnum::from($data['status']) : StoreStatusEnum::DRAFT,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'banner_stored_file_id' => $this->bannerStoredFileId,
            'logo_stored_file_id' => $this->logoStoredFileId,
            'address_id' => $this->addressId,
            'status' => $this->status->value,
        ];
    }
}
