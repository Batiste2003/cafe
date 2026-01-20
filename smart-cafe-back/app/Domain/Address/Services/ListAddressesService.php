<?php

namespace App\Domain\Address\Services;

use App\Models\Address;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListAddressesService
{
    public function execute(int $perPage = 20): LengthAwarePaginator
    {
        return Address::paginate($perPage);
    }
}
