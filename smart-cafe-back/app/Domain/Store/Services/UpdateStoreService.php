<?php

namespace App\Domain\Store\Services;

use App\Domain\Store\DTOs\UpdateStoreInputDTO;
use App\Models\Store;

/**
 * Service de mise à jour de magasin.
 */
class UpdateStoreService
{
    /**
     * Met à jour un magasin existant.
     *
     * @param  Store  $store  Le magasin à modifier
     * @param  UpdateStoreInputDTO  $dto  Les données de mise à jour
     * @return Store Le magasin mis à jour avec ses relations
     */
    public function execute(Store $store, UpdateStoreInputDTO $dto): Store
    {
        $data = [];

        if ($dto->name !== null) {
            $data['name'] = $dto->name;
        }

        if ($dto->removeBanner) {
            $data['banner_stored_file_id'] = null;
        } elseif ($dto->bannerStoredFileId !== null) {
            $data['banner_stored_file_id'] = $dto->bannerStoredFileId;
        }

        if ($dto->removeLogo) {
            $data['logo_stored_file_id'] = null;
        } elseif ($dto->logoStoredFileId !== null) {
            $data['logo_stored_file_id'] = $dto->logoStoredFileId;
        }

        if ($dto->removeAddress) {
            $data['address_id'] = null;
        } elseif ($dto->addressId !== null) {
            $data['address_id'] = $dto->addressId;
        }

        if ($dto->status !== null) {
            $data['status'] = $dto->status->value;
        }

        if (! empty($data)) {
            $store->update($data);
        }

        return $store->fresh()->load(['banner', 'logo', 'address', 'creator', 'users.roles']);
    }
}
