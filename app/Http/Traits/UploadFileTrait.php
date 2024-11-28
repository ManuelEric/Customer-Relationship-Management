<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;

trait UploadFileTrait
{
    public function tnUploadFile(Request $request, String $fieldname, String $requested_name, String $directory)
    {
        if ( $request->hasFile($fieldname) ) 
        {
            if ( !$request->file($fieldname)->isValid() )
                return redirect()->back()->withErrors('Invalid Document!');
            
            $file_format = $request->file($fieldname)->getClientOriginalExtension();
            $file_name = $requested_name . '.' . $file_format;
            return $request->file($fieldname)->storeAs($directory, $file_name);
        }

        return null;
    }
}