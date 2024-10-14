<?php

namespace App\Services;

class FileUploadService 
{
    public function snUploadFile($file, $path, $file_name)
    {
        $file_format = $file->getClientOriginalExtension();
        $file->storeAs($path, $file_name.'.'.$file_format);
    }
}   