<?php

namespace App\Domain\ProductCategory\Constant;

final class ProductCategoryConstant
{
    // Pagination
    public const int DEFAULT_PER_PAGE = 15;
    public const int MAX_PER_PAGE = 100;

    // Validation
    public const int NAME_MIN_LENGTH = 2;
    public const int NAME_MAX_LENGTH = 255;
    public const int DESCRIPTION_MAX_LENGTH = 1000;

    // Messages
    public const string CATEGORY_CREATED = 'Catégorie créée avec succès.';
    public const string CATEGORY_UPDATED = 'Catégorie mise à jour avec succès.';
    public const string CATEGORY_DELETED = 'Catégorie supprimée avec succès.';
    public const string CATEGORY_NOT_FOUND = 'Catégorie non trouvée.';
    public const string CATEGORIES_RETRIEVED = 'Liste des catégories récupérée avec succès.';
    public const string CATEGORY_RETRIEVED = 'Catégorie récupérée avec succès.';

    // Error messages
    public const string CANNOT_SET_SELF_AS_PARENT = 'Une catégorie ne peut pas être son propre parent.';
    public const string CANNOT_SET_DESCENDANT_AS_PARENT = 'Une catégorie ne peut pas avoir un de ses descendants comme parent.';
    public const string CATEGORY_HAS_PRODUCTS = 'Cette catégorie contient des produits et ne peut pas être supprimée.';
}
