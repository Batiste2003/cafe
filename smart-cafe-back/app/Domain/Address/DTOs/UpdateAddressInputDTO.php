<?php

namespace App\Domain\Address\DTOs;

readonly class UpdateAddressInputDTO
{
    public function __construct(
        public ?string $label,
        public ?string $addressLine1,
        public ?string $addressLine2,
        public ?string $city,
        public ?string $postalCode,
        public ?string $country
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            label: $data['label'] ?? null,
            addressLine1: $data['address_line1'] ?? null,
            addressLine2: $data['address_line2'] ?? null,
            city: $data['city'] ?? null,
            postalCode: $data['postal_code'] ?? null,
            country: $data['country'] ?? null
        );
    }
}
