<?php

namespace App\Http\Traits;

use Exception;
use Illuminate\Support\Facades\Log;

trait LoggingTrait
{

    public function logError()
    {
        
    }

    public function logSuccess($type, $inputFrom=null, $modul, $user, $data=null, $oldData=null)
    {
        $context = [];

        switch ($type) {
            case 'store':
                $message = $inputFrom  . ': Successfully Stored New ' . $modul . ' By ' . $user;
                $context = $this->checkType($data);
                break;

            case 'update':

                # when it comes to updating
                # oldData is necessary to track between the old data and updated data
                if ($oldData === null)
                    throw new Exception('You should include the old data');

                $message = $inputFrom . ': Successfully Updated ' . $modul . ' By ' . $user;
                $context = ['From' => $this->checkType($oldData), 'Into' => $this->checkType($data)];
                break;

            case 'delete':
                $message = 'Successfully Deleted ' . $modul . ' By ' . $user;
                $context = $this->checkType($data);
                break;

            case 'request-sign':
                $message = 'Successfully Send Request Sign ' . $modul . ' By ' . $user;
                $context = $this->checkType($data);
                break;

            case 'send-to-client':
                $message = 'Successfully Send to Client ' . $modul . ' By ' . $user;
                $context = $this->checkType($data);
                break;

            case 'invitation':
                $message = 'Successfully Send Invitation ' . $modul . ' By ' . $user;
                break;

            case 'signed':
                $message = 'Successfully Signed ' . $modul . ' By ' . $user;
                $context = $this->checkType($data);
                break;

            case 'upload':
                $message = 'Successfully Uploaded ' . $modul . ' By ' . $user;
                $context = $this->checkType($data);
                break;

            case 'download':
                $message = 'Successfully Downloaded ' . $modul . ' By ' . $user;
                $context = $this->checkType($data);
                break;

            case 'auth':
                $message =  $modul . ' was Successful for ' . $user;
                break;
        }


        Log::notice($message, $context);
        
    }

    private function checkType ($data)
    {
        return gettype($data) != 'array' ? $data->toArray() : $data;
    }
}
