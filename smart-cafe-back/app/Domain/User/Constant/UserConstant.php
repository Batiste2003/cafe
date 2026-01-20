<?php

namespace App\Domain\User\Constant;

final class UserConstant
{
    // Pagination
    public const int DEFAULT_PER_PAGE = 15;
    public const int MAX_PER_PAGE = 100;

    // Validation
    public const int NAME_MIN_LENGTH = 2;
    public const int NAME_MAX_LENGTH = 255;
    public const int PASSWORD_MIN_LENGTH = 8;
    public const int PASSWORD_MAX_LENGTH = 255;

    // Messages
    public const string USER_CREATED = 'Utilisateur créé avec succès.';
    public const string USER_UPDATED = 'Utilisateur mis à jour avec succès.';
    public const string USER_DELETED = 'Utilisateur supprimé avec succès.';
    public const string USER_RESTORED = 'Utilisateur restauré avec succès.';
    public const string USER_NOT_FOUND = 'Utilisateur non trouvé.';
    public const string USERS_RETRIEVED = 'Liste des utilisateurs récupérée avec succès.';
    public const string USER_RETRIEVED = 'Utilisateur récupéré avec succès.';

    // Error Messages
    public const string CANNOT_DELETE_SELF = 'Vous ne pouvez pas supprimer votre propre compte.';
    public const string CANNOT_DELETE_ADMIN = 'Vous ne pouvez pas supprimer un administrateur.';
    public const string CANNOT_CREATE_ADMIN = 'Vous ne pouvez pas créer un administrateur.';
}
