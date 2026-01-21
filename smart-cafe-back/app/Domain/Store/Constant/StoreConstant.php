<?php

namespace App\Domain\Store\Constant;

final class StoreConstant
{
    // Pagination
    public const int DEFAULT_PER_PAGE = 15;
    public const int MAX_PER_PAGE = 100;

    // Validation
    public const int NAME_MIN_LENGTH = 2;
    public const int NAME_MAX_LENGTH = 255;

    // Messages
    public const string STORE_CREATED = 'Magasin créé avec succès.';
    public const string STORE_UPDATED = 'Magasin mis à jour avec succès.';
    public const string STORE_DELETED = 'Magasin supprimé avec succès.';
    public const string STORE_NOT_FOUND = 'Magasin non trouvé.';
    public const string STORES_RETRIEVED = 'Liste des magasins récupérée avec succès.';
    public const string STORE_RETRIEVED = 'Magasin récupéré avec succès.';

    // User association messages
    public const string USER_ATTACHED = 'Utilisateur(s) associé(s) au magasin avec succès.';
    public const string USER_DETACHED = 'Utilisateur retiré du magasin avec succès.';
    public const string USER_ALREADY_ATTACHED = 'Utilisateur déjà associé à ce magasin.';
    public const string USER_NOT_ATTACHED = 'Utilisateur non associé à ce magasin.';

    // Variant stock messages
    public const string VARIANT_STOCKS_RETRIEVED = 'Stocks des variantes récupérés avec succès.';
    public const string STORE_PRODUCTS_RETRIEVED = 'Produits du magasin récupérés avec succès.';

    // Error messages
    public const string EMPLOYER_ALREADY_HAS_STORE = 'Un employé ne peut être associé qu\'à un seul magasin.';
    public const string CANNOT_ATTACH_ADMIN = 'Un administrateur ne peut pas être associé à un magasin.';
    public const string STORE_NOT_ACCESSIBLE = 'Vous n\'avez pas accès à ce magasin.';
}
