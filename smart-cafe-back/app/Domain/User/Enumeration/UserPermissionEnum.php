<?php

namespace App\Domain\User\Enumeration;

enum UserPermissionEnum: string
{
    // Product permissions
    case PRODUCT_VIEW = 'product.view';
    case PRODUCT_CREATE = 'product.create';
    case PRODUCT_UPDATE = 'product.update';
    case PRODUCT_DELETE = 'product.delete';

    // Variant permissions
    case VARIANT_VIEW = 'variant.view';
    case VARIANT_CREATE = 'variant.create';
    case VARIANT_UPDATE = 'variant.update';
    case VARIANT_DELETE = 'variant.delete';

    // Variant Option permissions
    case VARIANT_OPTION_VIEW = 'variant_option.view';
    case VARIANT_OPTION_CREATE = 'variant_option.create';
    case VARIANT_OPTION_UPDATE = 'variant_option.update';
    case VARIANT_OPTION_DELETE = 'variant_option.delete';

    // Store permissions
    case STORE_VIEW = 'store.view';
    case STORE_CREATE = 'store.create';
    case STORE_UPDATE = 'store.update';
    case STORE_DELETE = 'store.delete';
    case STORE_PRODUCT_ATTACH = 'store.product.attach';
    case STORE_PRODUCT_DETACH = 'store.product.detach';

    // Order permissions
    case ORDER_VIEW = 'order.view';
    case ORDER_CREATE = 'order.create';
    case ORDER_UPDATE = 'order.update';
    case ORDER_DELETE = 'order.delete';
    case ORDER_CANCEL = 'order.cancel';
    case ORDER_REFUND = 'order.refund';

    // User permissions
    case USER_VIEW = 'user.view';
    case USER_CREATE = 'user.create';
    case USER_UPDATE = 'user.update';
    case USER_DELETE = 'user.delete';

    public function label(): string
    {
        return match ($this) {
            // Product
            self::PRODUCT_VIEW => 'Voir les produits',
            self::PRODUCT_CREATE => 'Créer des produits',
            self::PRODUCT_UPDATE => 'Modifier des produits',
            self::PRODUCT_DELETE => 'Supprimer des produits',
            // Variant
            self::VARIANT_VIEW => 'Voir les variantes',
            self::VARIANT_CREATE => 'Créer des variantes',
            self::VARIANT_UPDATE => 'Modifier des variantes',
            self::VARIANT_DELETE => 'Supprimer des variantes',
            // Variant Option
            self::VARIANT_OPTION_VIEW => 'Voir les options de variantes',
            self::VARIANT_OPTION_CREATE => 'Créer des options de variantes',
            self::VARIANT_OPTION_UPDATE => 'Modifier des options de variantes',
            self::VARIANT_OPTION_DELETE => 'Supprimer des options de variantes',
            // Store
            self::STORE_VIEW => 'Voir les magasins',
            self::STORE_CREATE => 'Créer des magasins',
            self::STORE_UPDATE => 'Modifier des magasins',
            self::STORE_DELETE => 'Supprimer des magasins',
            self::STORE_PRODUCT_ATTACH => 'Ajouter des produits aux magasins',
            self::STORE_PRODUCT_DETACH => 'Retirer des produits des magasins',
            // Order
            self::ORDER_VIEW => 'Voir les commandes',
            self::ORDER_CREATE => 'Créer des commandes',
            self::ORDER_UPDATE => 'Modifier des commandes',
            self::ORDER_DELETE => 'Supprimer des commandes',
            self::ORDER_CANCEL => 'Annuler des commandes',
            self::ORDER_REFUND => 'Rembourser des commandes',
            // User
            self::USER_VIEW => 'Voir les utilisateurs',
            self::USER_CREATE => 'Créer des utilisateurs',
            self::USER_UPDATE => 'Modifier des utilisateurs',
            self::USER_DELETE => 'Supprimer des utilisateurs',
        };
    }

    /**
     * Returns all permission values as array.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Returns permissions for Admin role (all permissions).
     *
     * @return array<self>
     */
    public static function adminPermissions(): array
    {
        return self::cases();
    }

    /**
     * Returns permissions for Manager role.
     *
     * @return array<self>
     */
    public static function managerPermissions(): array
    {
        return [
            self::PRODUCT_VIEW,
            self::VARIANT_VIEW,
            self::VARIANT_OPTION_VIEW,
            self::ORDER_VIEW,
            self::ORDER_CANCEL,
            self::ORDER_REFUND,
            self::STORE_VIEW,
        ];
    }

    /**
     * Returns permissions for Employer role.
     *
     * @return array<self>
     */
    public static function employerPermissions(): array
    {
        return [
            self::PRODUCT_VIEW,
            self::VARIANT_VIEW,
            self::VARIANT_OPTION_VIEW,
            self::ORDER_VIEW,
            self::STORE_VIEW,
        ];
    }
}
