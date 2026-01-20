<?php

namespace App\Domain\Address\Services;

use App\Domain\Address\DTOs\UpdateAddressInputDTO;
use App\Models\Address;

class UpdateAddressService
{
    public function execute(Address $address, UpdateAddressInputDTO $dto): Address
    {
        $data = array_filter([
            'label' => $dto->label,
            'address_line1' => $dto->addressLine1,
            'address_line2' => $dto->addressLine2,
            'city' => $dto->city,
            'postal_code' => $dto->postalCode,
            'country' => $dto->country,
        ], fn ($value) => $value !== null);

        $address->update($data);

        return $address->fresh();
    }
}
