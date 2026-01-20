<?php

namespace App\Domain\StoredFile\Services;

use App\Models\StoredFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeleteStoredFileService
{
    /**
     * Delete a stored file record.
     *
     * If multiple records share the same hash, only soft delete the record.
     * If this is the only record with this hash, permanently delete the physical file.
     */
    public function execute(StoredFile $storedFile): bool
    {
        return DB::transaction(function () use ($storedFile) {
            $hash = $storedFile->hash;
            $disk = $storedFile->disk->value;
            $path = $storedFile->path;

            $otherRecordsCount = StoredFile::where('hash', $hash)
                ->where('id', '!=', $storedFile->id)
                ->whereNull('deleted_at')
                ->count();

            if ($otherRecordsCount > 0) {
                return $storedFile->delete();
            }

            $storedFile->delete();

            $this->deletePhysicalFile($disk, $path);

            return true;
        });
    }

    /**
     * Force delete a stored file (hard delete record + physical file if no other references).
     */
    public function forceExecute(StoredFile $storedFile): bool
    {
        return DB::transaction(function () use ($storedFile) {
            $hash = $storedFile->hash;
            $disk = $storedFile->disk->value;
            $path = $storedFile->path;

            $storedFile->forceDelete();

            $remainingCount = StoredFile::where('hash', $hash)->count();

            if ($remainingCount === 0) {
                $this->deletePhysicalFile($disk, $path);
            }

            return true;
        });
    }

    /**
     * Delete the physical file from storage.
     */
    private function deletePhysicalFile(string $disk, string $path): bool
    {
        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->delete($path);
        }

        return true;
    }
}
