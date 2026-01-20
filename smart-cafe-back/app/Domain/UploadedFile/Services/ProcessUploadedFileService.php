<?php

namespace App\Domain\UploadedFile\Services;

use App\Domain\StoredFile\DTOs\CreateStoredFileInputDTO;
use App\Domain\StoredFile\Services\CreateStoredFileService;
use App\Domain\UploadedFile\DTOs\ProcessUploadedFileInputDTO;
use App\Models\StoredFile;

class ProcessUploadedFileService
{
    public function __construct(
        private readonly CreateStoredFileService $createStoredFileService
    ) {}

    /**
     * Process an uploaded file and create a StoredFile record.
     */
    public function execute(ProcessUploadedFileInputDTO $dto): StoredFile
    {
        $createDto = CreateStoredFileInputDTO::fromArray([
            'file' => $dto->file,
            'user_id' => $dto->userId,
            'disk' => $dto->disk->value,
            'directory' => $dto->directory,
        ]);

        return $this->createStoredFileService->execute($createDto);
    }
}
