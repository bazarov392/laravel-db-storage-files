<?php

namespace Bazarov392;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class StorageFile extends Model
{
    use HasUuids;

    protected $guarded = false;
    protected $primaryKey = 'file_id';

    public function download(): void
    {
        $data = $this->getData();
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.$this->name.'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Access-Control-Allow-Origin: *');
        header('Content-Length: ' . strlen($data));

        echo $data;
        exit;
    }

    public function setInfoAttribute(array $info): void 
    {
        $this->attributes['info'] = json_encode($info);
    }

    public function getInfoAttribute(string $json): array 
    {
        return json_decode($json, true);
    }

    public function getData(): string
    {
        return StorageFileContents::where('file_id', $this->file_id)->value('data');
    }

    public function getDataAttribute(): string
    {
        return $this->getData();
    }

    public function getNameAttribute(): string
    {   
        return basename($this->path);
    }
}
