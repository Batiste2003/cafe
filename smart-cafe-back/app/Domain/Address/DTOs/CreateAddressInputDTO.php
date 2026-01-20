<?php

namespace App\Domain\Address\DTOs;

readonly class CreateAddressInputDTO
{
    public function __construct(
        public string $label,
        public string $addressLine1,
        public ?string $addressLine2,
        public string $city,
        public string $postalCode,
        public string $country
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            label: $data['label'],
            addressLine1: $data['address_line1'],
            addressLine2: $data['address_line2'] ?? null,
            city: $data['city'],
            postalCode: $data['postal_code'],
            country: $data['country']
        );
    }
}
