<?php

namespace App\Domain\Store\Services;

use App\Domain\Store\DTOs\CreateStoreInputDTO;
use App\Models\Store;
use App\Models\User;

/**
 * Service de création de magasin.
 */
class CreateStoreService
{
    /**
     * Crée un nouveau magasin.
     *
     * @param  CreateStoreInputDTO  $dto  Les données du magasin à créer
     * @param  User  $creator  L'utilisateur qui crée le magasin
     * @return Store Le magasin créé avec ses relations
     */
    public function execute(CreateStoreInputDTO $dto, User $creator): Store
    {
        $store = Store::create([
            'name' => $dto->name,
            'banner_stored_file_id' => $dto->bannerStoredFileId,
            'logo_stored_file_id' => $dto->logoStoredFileId,
            'address_id' => $dto->addressId,
            'status' => $dto->status->value,
            'created_by' => $creator->id,
        ]);

        return $store->load(['banner', 'logo', 'address', 'creator']);
    }
}
