<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Log;

trait LoggingTrait
{

    public function logError()
    {
        
    }

    public function logSuccess($type, $inputFrom, $modul, $user, $new=null, $old=null)
    {

        switch ($type) {
            case 'store':
                $message = $inputFrom  . ': Successfully Stored New ' . $modul . ' By ' . $user;
                $context = $this->checkType($new);
                break;

            case 'update':
                $message = $inputFrom . ': Successfully Updated ' . $modul . ' By ' . $user;
                $context = ['Form' => $this->checkType($old), 'Into' => $this->checkType($new)];
                break;
        }

        Log::notice($message, $context);
        
    }

    private function checkType ($data)
    {
        return gettype($data) != 'array' ? $data->toArray() : $data;
    }
}
