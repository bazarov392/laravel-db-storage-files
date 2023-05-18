<?php

namespace Bazarov392;

use Bazarov392\StorageFile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

final class StorageFiles
{
    public function __construct()
    {
        $queryDelete = StorageFile::query()
            ->where('deletion_date', '!=', null)
            ->where('deletion_date', '<', Carbon::now());

        if($queryDelete->count() > 0) 
        {
            $files = $queryDelete->select('path', 'file_id')->get();
            foreach($files as $file)
            {
                $this->deleteFromCache($file->path);
                $file->delete();
            }
        }
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

    public function write(string $path, string $contents, Carbon|null $deletionDate = null, ?array $info = null): StorageFile
    {
        $this->addToCache($path);
        $file = StorageFile::updateOrCreate([
            'path' => $path
        ],
        [
            'size' => strlen($contents),
            'hash' => hash('sha256', $contents, false),
            'deletion_date' => $deletionDate,
            'info' => $info ?? []
        ]);

        StorageFileContents::updateOrCreate([
            'file_id' => $file->file_id
        ],
        [
            'data' => $contents
        ]);

        return $file;
    }

    public function delete(StorageFile|string $pathOrModel): void
    {
        if($pathOrModel instanceof StorageFile)
        {
            $this->deleteFromCache($pathOrModel->path);
            $pathOrModel->delete();
        }
        else
        {
            $this->deleteFromCache($pathOrModel);
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

    private function getPathInfo($path): array
    {
        preg_match("/^(.+)?(\/)(.+)$/", $path, $matchesPath);
        return [
            'folder' => ($matchesPath[1] == '') ? $matchesPath[2] : $matchesPath[1],
            'file' => basename($path)
        ];
    }

    private function addToCache($path): void
    {
        $pathInfo = $this->getPathInfo($path);
        $cachePath = "dbf_{$pathInfo['folder']}";
        $cache = Cache::get($cachePath);
        if($cache !== null)
        {
            if(!isset($cache[$pathInfo['file']]))
            {
                $cache[$pathInfo['file']] = 'file';
                Cache::put($cachePath, $cache);
            }
            
        }
        else Cache::put($cachePath, [
            $pathInfo['file'] => 'file'
        ]);
    }

    private function deleteFromCache($path): void
    {
        $pathInfo = $this->getPathInfo($path);
        $cachePath = "dbf_{$pathInfo['folder']}";
        $cache = Cache::get($cachePath);
        if($cache !== null)
        {
            if(isset($cache[$pathInfo['file']]))
            {
                unset($cache[$pathInfo['file']]);
                if(count($cache) > 0) Cache::put($cachePath, $cache);
                else Cache::forget($cachePath);
            }
        }
    }

    public function hierarchicalArray(): array
    {
        $paths = $this->getList('/');
        $result = [];
        foreach ($paths as $path) {
        $parts = explode('/', $path); 

        $current = &$result;

        foreach ($parts as $part) {
            if (!isset($current[$part])) {
            $current[$part] = [];
            }

            $current = &$current[$part]; 
        }

        if(count($current) == 0) $current = $path;
        else $current[] = $path; 
        }

        return $result;
    }

    public function reloadCache(): void
    {
        $result = $this->hierarchicalArray();
        $cache = $this->cacheFoldersContent($result, '');
        foreach($cache as $key => $value)
            Cache::put('dbf_'.$key, $value);
    }

    public function cacheFoldersContent(array $folders, string $path, array &$cache = []): array
    {
        foreach ($folders as $key => $value) {
            $currentPath = $path . '/' . $key;
            if (is_array($value)) {
                $cache[$currentPath] =
                    array_map(fn ($item) => is_array($item) ? 'folder' : 'file', $value);
                $this->cacheFoldersContent($value, ($currentPath == '/' ? '' : $currentPath), $cache); // Рекурсивно кэшируем содержимое вложенных папок
            }
        }
        return $cache;
    }

    public function getContentsFolder(string $path): array|null
    {
        return Cache::get('dbf_'.$path);
    }
}
