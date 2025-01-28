<?php

namespace App\Http\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait StoreAttachmentProgramTrait
{

    public function getFileNameAttachment($file)
    {

        $file_name = Str::slug($file, "_") . '_' . Str::slug(Carbon::now(), "_");

        return $file_name;
    }

    protected function attachmentProgram($attachment, $id, $file_name, $type)
    {
        $file = $attachment;
        $extension = $file->getClientOriginalExtension();
        switch ($type) {
            case 'school_program':
                $directory_type = 'sch_prog_attach';
                break;
            case 'partner_program':
                $directory_type = 'partner_prog_attach';
                break;
        }
        $file_location = 'project/crm/attachment/'. $directory_type .'/' . $id . '/';
        $file_attachment = $file_location . $file_name . '.' . $extension;
        Storage::disk('s3')->put($file_attachment, file_get_contents($file));
        
        return $file_name . '.' . $extension;
    }
}
