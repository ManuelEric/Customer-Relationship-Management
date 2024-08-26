<?php
namespace App\Http\Traits;

use App\Models\UserClientAdditionalInfo;

trait CheckExistingClient {

    public function checkExistingClient($phone = null, $email = null)
    {
        $existClient = [];

        // Check existing client by phone number and email
        $clientExistPhone = $this->clientRepository->checkExistingByPhoneNumber($phone);
        $clientExistEmail = $this->clientRepository->checkExistingByEmail($email);

        # if both instruments are exists 
        if ($clientExistPhone && $clientExistEmail) {

            $existClient['isExist'] = true;
            # get the existing client from query that check existing using phone number
            $existClient['id'] = $clientExistPhone['id'];

        } else if ( $clientExistPhone && !$clientExistEmail ) {

            $existClient['isExist'] = true;
            $existClient['id'] = $clientExistPhone['id'];

            if (isset($email)) {
                // Add email to client addtional info
                $additionalInfo = [
                    'client_id' => $clientExistPhone['id'],
                    'category' => 'mail',
                    'value' => $email,
                ];
                UserClientAdditionalInfo::create($additionalInfo);
            }

        } else if ( !$clientExistPhone && $clientExistEmail ) {

            $existClient['isExist'] = true;
            $existClient['id'] = $clientExistEmail['id'];

            if (isset($phone)) {
                // Add phone to client addtional info
                $additionalInfo = [
                    'client_id' => $clientExistEmail['id'],
                    'category' => 'phone',
                    'value' => $phone,
                ];
                UserClientAdditionalInfo::create($additionalInfo);
            }

        } else {

            $existClient['isExist'] = false;

        }

        return $existClient;
    }
}