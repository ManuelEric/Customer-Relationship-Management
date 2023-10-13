<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Log;

trait LoggingTrait
{

    public function logError()
    {
        
    }

    public function logSuccess($type, $modul, $user, $id=null, $from=null, $into=null)
    {

        switch ($type) {
            case 'store':
                $message = 'Successfully Stored New ' . $modul . ' (ID: ' . $id . ') By ' . $user;
                break;

            case 'update':
                $message = 'Successfully Updated ' . $modul . ' From: ' . json_encode($from)  . ' into: ' . json_encode($into) . ' By ' . $user;
                break;
        }

        Log::notice($message);
        
    }
}
