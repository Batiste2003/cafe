<?php

namespace App\Domain\Address\Services;

use App\Domain\Address\DTOs\CreateAddressInputDTO;
use App\Models\Address;

class CreateAddressService
{
    public function execute(CreateAddressInputDTO $dto): Address
    {
        return Address::create([
            'label' => $dto->label,
            'address_line1' => $dto->addressLine1,
            'address_line2' => $dto->addressLine2,
            'city' => $dto->city,
            'postal_code' => $dto->postalCode,
            'country' => $dto->country,
        ]);
    }
}
