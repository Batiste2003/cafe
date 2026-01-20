<?php

namespace App\Domain\Store\DTOs;

use App\Domain\Store\Enumeration\StoreStatusEnum;

readonly class UpdateStoreInputDTO
{
    public function __construct(
        public ?string $name = null,
        public ?int $bannerStoredFileId = null,
        public ?int $logoStoredFileId = null,
        public ?int $addressId = null,
        public ?StoreStatusEnum $status = null,
        public bool $removeBanner = false,
        public bool $removeLogo = false,
        public bool $removeAddress = false,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            bannerStoredFileId: $data['banner_stored_file_id'] ?? null,
            logoStoredFileId: $data['logo_stored_file_id'] ?? null,
            addressId: $data['address_id'] ?? null,
            status: isset($data['status']) ? StoreStatusEnum::from($data['status']) : null,
            removeBanner: filter_var($data['remove_banner'] ?? false, FILTER_VALIDATE_BOOLEAN),
            removeLogo: filter_var($data['remove_logo'] ?? false, FILTER_VALIDATE_BOOLEAN),
            removeAddress: filter_var($data['remove_address'] ?? false, FILTER_VALIDATE_BOOLEAN),
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

        if ($this->removeBanner) {
            $data['banner_stored_file_id'] = null;
        } elseif ($this->bannerStoredFileId !== null) {
            $data['banner_stored_file_id'] = $this->bannerStoredFileId;
        }

        if ($this->removeLogo) {
            $data['logo_stored_file_id'] = null;
        } elseif ($this->logoStoredFileId !== null) {
            $data['logo_stored_file_id'] = $this->logoStoredFileId;
        }

        if ($this->removeAddress) {
            $data['address_id'] = null;
        } elseif ($this->addressId !== null) {
            $data['address_id'] = $this->addressId;
        }

        if ($this->status !== null) {
            $data['status'] = $this->status->value;
        }

        return $data;
    }
}
