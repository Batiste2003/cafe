<?php

namespace App\Domain\Address\Services;

use App\Models\Address;

class GetAddressService
{
    public function execute(int $id): ?Address
    {
        return Address::find($id);
    }
}
