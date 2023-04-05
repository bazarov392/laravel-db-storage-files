<?php

namespace Bazarov392;

use Bazarov392\StorageFile;
use Carbon\Carbon;

final class StorageFiles
{
    public function __construct()
    {
        $queryDelete = StorageFile::query()
            ->where('deletion_date', '!=', null)
            ->where('deletion_date', '<', Carbon::now());

        if($queryDelete->count() > 0) $queryDelete->delete();
    }

    public function containsPath(string $path): bool
    {
        return StorageFile::where('path', $path)->exists();
    }

    public function containsFileId(string $fileId): bool
    {
        return StorageFile::where('file_id', $fileId)->exists();
    }

    public function getFromPath(string $path): StorageFile|null
    {
        return StorageFile::where('path', $path)->first();
    }

    public function getFromFileId(string $fileId): StorageFile|null
    {
        return StorageFile::where('file_id', $fileId)->first();
    }

    public function write(string $path, string $contents, Carbon|null $deletionDate = null): StorageFile
        {
            $file = StorageFile::updateOrCreate([
                'path' => $path
            ],
            [
                'size' => strlen($contents),
                'data' => $contents,
                'hash' => hash('sha256', $contents, false),
                'deletion_date' => $deletionDate
            ]);
    
            return $file;
        }

    public function delete(StorageFile|string $pathOrModel): void
    {
        if($pathOrModel instanceof StorageFile)
        {
            $pathOrModel->delete();
        }
        else
        {
            $query = StorageFile::where('path', $pathOrModel);
            if($query->exists()) $query->delete();
        }
    }

    public function getList(string $path): array
    {
        $query = StorageFile::where('path', 'like', $path.'%');
        return ($query->exists())
            ? $query->pluck('path')->toArray()
            : [];
    }
}
