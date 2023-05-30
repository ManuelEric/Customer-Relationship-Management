<?php

namespace App\Http\Traits;

use Carbon\Carbon;
use Illuminate\Support\Str;

trait StoreAttachmentProgramTrait
{

    public function getFileNameAttachment($file)
    {

        $file_name = Str::slug($file, "_") . '_' . Str::slug(Carbon::now(), "_");

        return $file_name;
    }

    protected function attachmentProgram($attachment, $id, $file_name)
    {
        $file = $attachment;
        $extension = $file->getClientOriginalExtension();
        $file_location = 'attachment/sch_prog_attach/' . $id . '/';
        $file_attachment = $file_location . $file_name . '.' . $extension;
        $file->move($file_location, $file_name . '.' . $extension);
        return $file_attachment;
    }
}
