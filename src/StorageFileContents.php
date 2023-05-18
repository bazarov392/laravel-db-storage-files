<?php

namespace Bazarov392;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StorageFileContents extends Model
{
    protected $guarded = false;
    protected $primaryKey = 'file_id';
}
