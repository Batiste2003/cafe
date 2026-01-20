<?php

namespace App\Domain\UploadedFile\Constant;

final class UploadedFileConstant
{
    /**
     * Maximum file size for images (5 MB).
     */
    public const MAX_IMAGE_SIZE = 5242880;

    /**
     * Maximum file size in kilobytes for validation rule.
     */
    public const MAX_IMAGE_SIZE_KB = 5120;

    /**
     * Allowed MIME types for images.
     */
    public const ALLOWED_IMAGE_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
    ];

    /**
     * Allowed extensions for images.
     */
    public const ALLOWED_IMAGE_EXTENSIONS = [
        'jpg',
        'jpeg',
        'png',
        'gif',
        'webp',
    ];

    /**
     * Directory for store banners.
     */
    public const STORE_BANNER_DIRECTORY = 'stores/banners';

    /**
     * Directory for store logos.
     */
    public const STORE_LOGO_DIRECTORY = 'stores/logos';

    /**
     * Directory for product gallery images.
     */
    public const PRODUCT_GALLERY_DIRECTORY = 'products/gallery';

    /**
     * Directory for product variant gallery images.
     */
    public const PRODUCT_VARIANT_GALLERY_DIRECTORY = 'products/variants/gallery';

    /**
     * Error messages.
     */
    public const ERROR_FILE_UPLOAD_FAILED = 'Échec du téléchargement du fichier.';
    public const ERROR_INVALID_FILE_TYPE = 'Type de fichier non autorisé.';
    public const ERROR_FILE_TOO_LARGE = 'Le fichier est trop volumineux.';

    /**
     * Success messages.
     */
    public const FILE_UPLOADED = 'Fichier téléchargé avec succès.';
}
