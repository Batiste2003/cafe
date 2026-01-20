<?php

namespace App\Domain\StoredFile\Constant;

final class StoredFileConstant
{
    /**
     * Hash algorithm used for file deduplication.
     */
    public const HASH_ALGORITHM = 'sha256';

    /**
     * Maximum file size in bytes (50 MB).
     */
    public const MAX_FILE_SIZE = 52428800;

    /**
     * Default disk for file storage.
     */
    public const DEFAULT_DISK = 'local';

    /**
     * Allowed MIME types.
     */
    public const ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'text/plain',
    ];
}
