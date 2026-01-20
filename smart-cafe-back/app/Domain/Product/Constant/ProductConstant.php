<?php

namespace App\Domain\Product\Constant;

final class ProductConstant
{
    // Pagination
    public const int DEFAULT_PER_PAGE = 15;
    public const int MAX_PER_PAGE = 100;

    // Validation
    public const int NAME_MIN_LENGTH = 2;
    public const int NAME_MAX_LENGTH = 255;
    public const int DESCRIPTION_MAX_LENGTH = 5000;
    public const int SKU_MIN_LENGTH = 2;
    public const int SKU_MAX_LENGTH = 100;

    // Gallery
    public const int MAX_GALLERY_IMAGES = 10;

    // Messages
    public const string PRODUCT_CREATED = 'Produit créé avec succès.';
    public const string PRODUCT_UPDATED = 'Produit mis à jour avec succès.';
    public const string PRODUCT_DELETED = 'Produit supprimé avec succès.';
    public const string PRODUCT_NOT_FOUND = 'Produit non trouvé.';
    public const string PRODUCTS_RETRIEVED = 'Liste des produits récupérée avec succès.';
    public const string PRODUCT_RETRIEVED = 'Produit récupéré avec succès.';

    // Gallery messages
    public const string GALLERY_IMAGE_ADDED = 'Image ajoutée à la galerie avec succès.';
    public const string GALLERY_IMAGE_REMOVED = 'Image retirée de la galerie avec succès.';
    public const string GALLERY_MAX_IMAGES_REACHED = 'Nombre maximum d\'images dans la galerie atteint.';

    // Error messages
    public const string PRODUCT_NOT_ACCESSIBLE = 'Vous n\'avez pas accès à ce produit.';
    public const string STORE_NOT_FOUND = 'Magasin non trouvé.';
    public const string CATEGORY_NOT_FOUND = 'Catégorie non trouvée.';

    // Variant messages
    public const string VARIANT_CREATED = 'Variante créée avec succès.';
    public const string VARIANT_UPDATED = 'Variante mise à jour avec succès.';
    public const string VARIANT_DELETED = 'Variante supprimée avec succès.';
    public const string VARIANT_RETRIEVED = 'Variante récupérée avec succès.';
    public const string VARIANTS_RETRIEVED = 'Liste des variantes récupérée avec succès.';

    // Option messages
    public const string OPTION_CREATED = 'Option créée avec succès.';
    public const string OPTION_UPDATED = 'Option mise à jour avec succès.';
    public const string OPTION_DELETED = 'Option supprimée avec succès.';
    public const string OPTION_RETRIEVED = 'Option récupérée avec succès.';
    public const string OPTIONS_RETRIEVED = 'Liste des options récupérée avec succès.';

    // Option Value messages
    public const string OPTION_VALUE_CREATED = 'Valeur d\'option créée avec succès.';
    public const string OPTION_VALUE_UPDATED = 'Valeur d\'option mise à jour avec succès.';
    public const string OPTION_VALUE_DELETED = 'Valeur d\'option supprimée avec succès.';
    public const string OPTION_VALUE_RETRIEVED = 'Valeur d\'option récupérée avec succès.';
    public const string OPTION_VALUES_RETRIEVED = 'Liste des valeurs d\'option récupérée avec succès.';

    // Option validation
    public const int OPTION_NAME_MIN_LENGTH = 1;
    public const int OPTION_NAME_MAX_LENGTH = 100;
    public const int OPTION_VALUE_MIN_LENGTH = 1;
    public const int OPTION_VALUE_MAX_LENGTH = 100;

    // Product-Store messages
    public const string STORES_ATTACHED = 'Stores associés au produit avec succès.';
    public const string STORE_DETACHED = 'Store retiré du produit avec succès.';
    public const string PRODUCT_STORES_RETRIEVED = 'Liste des stores du produit récupérée avec succès.';
    public const string PRODUCT_NOT_IN_STORE = 'Le produit n\'est pas disponible dans ce store.';

    // Variant Stock messages
    public const string VARIANT_STOCK_UPDATED = 'Stock du variant mis à jour avec succès.';
    public const string VARIANT_STOCK_DELETED = 'Stock du variant supprimé avec succès.';
    public const string VARIANT_STOCKS_RETRIEVED = 'Liste des stocks du variant récupérée avec succès.';
    public const string VARIANT_NOT_IN_STORE = 'Le variant n\'est pas disponible dans ce store.';
}
