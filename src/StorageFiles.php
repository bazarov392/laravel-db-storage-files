<?php

namespace Bazarov392;

use App\Models\StorageFile;
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
        return (StorageFile::where('path', $path)->count() > 0) ? true : false;
    }

    public function containsFileId(string $fileId): bool
    {
        return (StorageFile::where('file_id', $fileId)->count() > 0) ? true : false;
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

        $file = null;
        if(StorageFile::where('path', $path)->count() > 0)
        {
            $file = StorageFile::where('path', $path)->first();
            $file->update([
                'data' => $contents,
                'size' => strlen($contents),
                'hash' => hash('sha256', $contents),
                'deletion_date' => $deletionDate
            ]);
        }
        else
        {

            $file = StorageFile::create([
                'path' => $path,
                'size' => strlen($contents),
                'data' => $contents,
                'hash' => hash('sha256', $contents),
                'deletion_date' => $deletionDate,
            ]);
        }

        return $file;
    }

    public function delete(StorageFile|string $pathOrModel): void
    {
        if(is_string($pathOrModel))
        {
            $storage = StorageFile::where('path', $pathOrModel);
            if($storage->count() > 0)
            {
                $storage->first()->delete();
            }
        }
        else
        {
            $pathOrModel->delete();
        }
    }

    public function getList(string $path): array
    {
        $query = StorageFile::select('path')->where('path', 'like', $path.'%');

        return ($query->count() > 0)
            ? $query->get()->map(fn (StorageFile $file) => $file->path)->toArray()
            : [];
    }
}
