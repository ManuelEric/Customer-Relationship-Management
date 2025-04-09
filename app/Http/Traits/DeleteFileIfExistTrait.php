<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Storage;

trait DeleteFileIfExistTrait
{
    public function tnDeleteFile(String $directory, String $file_name)
    {
        if (Storage::disk('s3')->exists($directory. $file_name))
                Storage::disk('s3')->delete($directory. $file_name);

        return null;
    }
}