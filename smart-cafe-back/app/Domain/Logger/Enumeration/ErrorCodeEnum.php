<?php

namespace App\Domain\Logger\Enumeration;

enum ErrorCodeEnum: string
{
    // Erreurs génériques
    case GENERIC_ERROR = 'GENERIC_ERROR';
    case VALIDATION_ERROR = 'VALIDATION_ERROR';
    case UNAUTHORIZED = 'UNAUTHORIZED';
    case FORBIDDEN = 'FORBIDDEN';
    case NOT_FOUND = 'NOT_FOUND';

    // Erreurs d'authentification
    case AUTH_INVALID_CREDENTIALS = 'AUTH_INVALID_CREDENTIALS';
    case AUTH_TOKEN_EXPIRED = 'AUTH_TOKEN_EXPIRED';
    case AUTH_TOKEN_INVALID = 'AUTH_TOKEN_INVALID';
    case AUTH_EMAIL_NOT_VERIFIED = 'AUTH_EMAIL_NOT_VERIFIED';

    // Erreurs de base de données
    case DATABASE_ERROR = 'DATABASE_ERROR';
    case DATABASE_CONNECTION_ERROR = 'DATABASE_CONNECTION_ERROR';
    case DATABASE_QUERY_ERROR = 'DATABASE_QUERY_ERROR';

    // Erreurs métier
    case BUSINESS_RULE_VIOLATION = 'BUSINESS_RULE_VIOLATION';
    case RESOURCE_ALREADY_EXISTS = 'RESOURCE_ALREADY_EXISTS';
    case RESOURCE_NOT_AVAILABLE = 'RESOURCE_NOT_AVAILABLE';

    // Erreurs externes
    case EXTERNAL_SERVICE_ERROR = 'EXTERNAL_SERVICE_ERROR';
    case EXTERNAL_SERVICE_TIMEOUT = 'EXTERNAL_SERVICE_TIMEOUT';

    public function message(): string
    {
        return match ($this) {
            self::GENERIC_ERROR => 'Une erreur est survenue.',
            self::VALIDATION_ERROR => 'Les données fournies sont invalides.',
            self::UNAUTHORIZED => 'Authentification requise.',
            self::FORBIDDEN => 'Accès refusé.',
            self::NOT_FOUND => 'Ressource non trouvée.',
            self::AUTH_INVALID_CREDENTIALS => 'Identifiants invalides.',
            self::AUTH_TOKEN_EXPIRED => 'Le token a expiré.',
            self::AUTH_TOKEN_INVALID => 'Token invalide.',
            self::AUTH_EMAIL_NOT_VERIFIED => 'Adresse email non vérifiée.',
            self::DATABASE_ERROR => 'Erreur de base de données.',
            self::DATABASE_CONNECTION_ERROR => 'Impossible de se connecter à la base de données.',
            self::DATABASE_QUERY_ERROR => 'Erreur lors de l\'exécution de la requête.',
            self::BUSINESS_RULE_VIOLATION => 'Règle métier non respectée.',
            self::RESOURCE_ALREADY_EXISTS => 'Cette ressource existe déjà.',
            self::RESOURCE_NOT_AVAILABLE => 'Ressource non disponible.',
            self::EXTERNAL_SERVICE_ERROR => 'Erreur du service externe.',
            self::EXTERNAL_SERVICE_TIMEOUT => 'Le service externe n\'a pas répondu à temps.',
        };
    }

    public function httpStatusCode(): int
    {
        return match ($this) {
            self::GENERIC_ERROR => 500,
            self::VALIDATION_ERROR => 422,
            self::UNAUTHORIZED, self::AUTH_INVALID_CREDENTIALS, self::AUTH_TOKEN_EXPIRED, self::AUTH_TOKEN_INVALID => 401,
            self::FORBIDDEN, self::AUTH_EMAIL_NOT_VERIFIED => 403,
            self::NOT_FOUND => 404,
            self::DATABASE_ERROR, self::DATABASE_CONNECTION_ERROR, self::DATABASE_QUERY_ERROR => 500,
            self::BUSINESS_RULE_VIOLATION => 422,
            self::RESOURCE_ALREADY_EXISTS => 409,
            self::RESOURCE_NOT_AVAILABLE => 410,
            self::EXTERNAL_SERVICE_ERROR, self::EXTERNAL_SERVICE_TIMEOUT => 502,
        };
    }
}
