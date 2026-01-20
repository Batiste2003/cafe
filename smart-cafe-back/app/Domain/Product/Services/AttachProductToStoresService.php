<?php

namespace App\Domain\Product\Services;

use App\Domain\Product\DTOs\AttachProductToStoresInputDTO;
use App\Models\Product;

/**
 * Service pour associer un produit à des stores.
 */
class AttachProductToStoresService
{
    /**
     * Associe un produit à des stores.
     *
     * @param  Product  $product  Le produit à associer
     * @param  AttachProductToStoresInputDTO  $dto  Les données contenant les IDs des stores
     * @return Product Le produit avec ses stores mis à jour
     */
    public function execute(Product $product, AttachProductToStoresInputDTO $dto): Product
    {
        // Sync ajoute les nouveaux stores sans supprimer les existants
        $product->stores()->syncWithoutDetaching($dto->storeIds);

        return $product->load('stores');
    }
}
