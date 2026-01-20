<?php

namespace App\Domain\Address\DTOs;

use App\Models\Address;

readonly class AddressOutputDTO
{
    public function __construct(
        public int $id,
        public string $label,
        public string $addressLine1,
        public ?string $addressLine2,
        public string $city,
        public string $postalCode,
        public string $country,
        public ?string $createdAt,
        public ?string $updatedAt
    ) {}

    public static function fromModel(Address $address): self
    {
        return new self(
            id: $address->id,
            label: $address->label,
            addressLine1: $address->address_line1,
            addressLine2: $address->address_line2,
            city: $address->city,
            postalCode: $address->postal_code,
            country: $address->country,
            createdAt: $address->created_at?->format('Y-m-d H:i:s'),
            updatedAt: $address->updated_at?->format('Y-m-d H:i:s')
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'address_line1' => $this->addressLine1,
            'address_line2' => $this->addressLine2,
            'city' => $this->city,
            'postal_code' => $this->postalCode,
            'country' => $this->country,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
