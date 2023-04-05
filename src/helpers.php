<?php

use Bazarov392\StorageFiles;

if(!function_exists('storageFiles'))
{
    function storageFiles(): StorageFiles
    {
        return new StorageFiles();
    }
}