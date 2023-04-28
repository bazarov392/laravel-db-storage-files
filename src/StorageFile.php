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
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($this->path).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Access-Control-Allow-Origin: *');
        header('Content-Length: ' . strlen($this->data));

        echo $this->data;
        exit;
    }

    public function setInfoAttribute(array $info): void 
    {
        $this->attributes['info'] = json_encode($info);
    }

    public function getInfoAttribute($json): array 
    {
        return json_decode($json, true);
    }
}
