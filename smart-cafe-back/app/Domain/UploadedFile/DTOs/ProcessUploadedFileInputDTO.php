<?php

namespace App\Domain\UploadedFile\DTOs;

use App\Domain\StoredFile\Enumeration\DiskEnum;
use Illuminate\Http\UploadedFile;

readonly class ProcessUploadedFileInputDTO
{
    public function __construct(
        public UploadedFile $file,
        public int $userId,
        public DiskEnum $disk = DiskEnum::PUBLIC,
        public ?string $directory = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            file: $data['file'],
            userId: $data['user_id'],
            disk: isset($data['disk']) ? DiskEnum::from($data['disk']) : DiskEnum::PUBLIC,
            directory: $data['directory'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'file' => $this->file,
            'user_id' => $this->userId,
            'disk' => $this->disk->value,
            'directory' => $this->directory,
        ];
    }
}
