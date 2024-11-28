<?php

namespace App\Http\Traits;

trait DeleteFileIfExistTrait
{
    public function tnDeleteFile(String $directory, String $file_name)
    {
        if (file_exists(public_path($directory . $file_name)))
            unlink(public_path($directory . $file_name));

        return null;
    }
}