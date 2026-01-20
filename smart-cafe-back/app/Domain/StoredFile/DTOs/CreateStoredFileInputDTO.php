<?php

namespace App\Domain\StoredFile\DTOs;

use App\Domain\StoredFile\Enumeration\DiskEnum;
use Illuminate\Http\UploadedFile;

readonly class CreateStoredFileInputDTO
{
    public function __construct(
        public UploadedFile $file,
        public int $userId,
        public DiskEnum $disk = DiskEnum::LOCAL,
        public ?string $directory = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            file: $data['file'],
            userId: $data['user_id'],
            disk: isset($data['disk']) ? DiskEnum::from($data['disk']) : DiskEnum::LOCAL,
            directory: $data['directory'] ?? null
        );
    }
}
