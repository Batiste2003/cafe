<?php

namespace App\Domain\Address\Services;

use App\Models\Address;

class DeleteAddressService
{
    public function execute(Address $address): bool
    {
        return $address->delete();
    }
}
