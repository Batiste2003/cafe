<?php

namespace App\Domain\StoredFile\Services;

use App\Domain\StoredFile\Constant\StoredFileConstant;
use App\Domain\StoredFile\DTOs\CreateStoredFileInputDTO;
use App\Domain\StoredFile\Enumeration\DiskEnum;
use App\Models\StoredFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CreateStoredFileService
{
    /**
     * Execute the file storage operation with hash deduplication.
     */
    public function execute(CreateStoredFileInputDTO $input): StoredFile
    {
        $hash = $this->calculateFileHash($input->file);

        $existingFile = StoredFile::activeByHash($hash)->first();

        return DB::transaction(function () use ($input, $hash, $existingFile) {
            if ($existingFile) {
                return $this->createRecordFromExisting($input, $existingFile, $hash);
            }

            return $this->storeNewFile($input, $hash);
        });
    }

    /**
     * Calculate SHA-256 hash of the uploaded file.
     */
    private function calculateFileHash(UploadedFile $file): string
    {
        return hash_file(StoredFileConstant::HASH_ALGORITHM, $file->getRealPath());
    }

    /**
     * Create a new record pointing to an existing file (deduplication).
     */
    private function createRecordFromExisting(
        CreateStoredFileInputDTO $input,
        StoredFile $existingFile,
        string $hash
    ): StoredFile {
        return StoredFile::create([
            'disk' => $existingFile->disk->value,
            'path' => $existingFile->path,
            'full_path' => $existingFile->full_path,
            'hash' => $hash,
            'original_name' => $input->file->getClientOriginalName(),
            'mime_type' => $input->file->getMimeType(),
            'size' => $input->file->getSize(),
            'extension' => $input->file->getClientOriginalExtension(),
            'user_id' => $input->userId,
        ]);
    }

    /**
     * Store a new file on disk and create the database record.
     */
    private function storeNewFile(CreateStoredFileInputDTO $input, string $hash): StoredFile
    {
        $disk = $input->disk;
        $directory = $input->directory ?? $this->generateDirectory();

        $filename = $this->generateFilename($input->file);

        $path = Storage::disk($disk->value)->putFileAs(
            $directory,
            $input->file,
            $filename
        );

        $fullPath = $this->getFullPath($disk, $path);

        return StoredFile::create([
            'disk' => $disk->value,
            'path' => $path,
            'full_path' => $fullPath,
            'hash' => $hash,
            'original_name' => $input->file->getClientOriginalName(),
            'mime_type' => $input->file->getMimeType(),
            'size' => $input->file->getSize(),
            'extension' => $input->file->getClientOriginalExtension(),
            'user_id' => $input->userId,
        ]);
    }

    /**
     * Generate a date-based directory structure.
     */
    private function generateDirectory(): string
    {
        return date('Y/m/d');
    }

    /**
     * Generate a unique filename preserving the extension.
     */
    private function generateFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        return Str::uuid() . ($extension ? '.' . $extension : '');
    }

    /**
     * Get the full filesystem path for local disks.
     */
    private function getFullPath(DiskEnum $disk, string $path): string
    {
        if ($disk === DiskEnum::S3) {
            return $path;
        }

        return Storage::disk($disk->value)->path($path);
    }
}
