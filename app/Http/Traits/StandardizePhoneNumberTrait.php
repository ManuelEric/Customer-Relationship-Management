<?php
namespace App\Http\Traits;

trait StandardizePhoneNumberTrait {

    public function setPhoneNumber($phoneNumber)
    {
        if (!$phoneNumber)
            return null;

        # remove , from phone number
        $phoneNumber = str_replace(',', '', $phoneNumber);

        # remove - from phone number
        $phoneNumber = str_replace('-', '', $phoneNumber);
        
        # remove space from phone number
        $phoneNumber = str_replace(' ', '', $phoneNumber);

        # add the normalization indonesian number +62
        switch (substr($phoneNumber, 0, 1)) {
            
            # check if the first character is 0
            case 0:
                $phoneNumber = "+62".substr($phoneNumber, 1);
                break;

            # check if the first character is 6 like 62
            case 6: 
                $phoneNumber = "+".$phoneNumber;
                break;

            case "+":
                $phoneNumber = $phoneNumber;
                break;
                
            default:
                $phoneNumber = "+62".$phoneNumber;

        }

        return $phoneNumber;
    }
}