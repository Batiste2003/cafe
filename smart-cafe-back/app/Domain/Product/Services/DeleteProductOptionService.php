<?php

namespace App\Domain\Product\Services;

use App\Models\ProductOption;

/**
 * Service de suppression d'option de produit.
 */
class DeleteProductOptionService
{
    /**
     * Supprime une option (hard delete car pas de soft delete).
     *
     * Les valeurs associées seront supprimées en cascade.
     *
     * @param  ProductOption  $option  L'option à supprimer
     */
    public function execute(ProductOption $option): void
    {
        $option->delete();
    }
}
