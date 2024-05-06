<?php
namespace App\Http\Traits;

use App\Models\UserClient;
use App\Models\UserClientAdditionalInfo;

trait CheckExistingClientImport {

    public function checkExistingClientImport($phone, $email)
    {
        $existClient = [];

        // Check existing client by phone number and email
        $clientExistPhone = isset($phone) ? $this->checkExistingByPhoneNumber($phone) : false;
        $clientExistEmail = isset($email) ? $this->checkExistingByEmail($email) : false;

        if ($clientExistPhone && $clientExistEmail) {
            $existClient['isExist'] = true;
            $existClient['id'] = $clientExistPhone['id'];
        } else if ($clientExistPhone && !$clientExistEmail) {
            $existClient['isExist'] = true;
            $existClient['id'] = $clientExistPhone['id'];

            if($email != null || $email != ''){
                // Add email to client addtional info
                $additionalInfo = [
                    'client_id' => $clientExistPhone['id'],
                    'category' => 'mail',
                    'value' => $email,
                ];
                UserClientAdditionalInfo::create($additionalInfo);
            }
        } else if (!$clientExistPhone && $clientExistEmail) {
            $existClient['isExist'] = true;
            $existClient['id'] = $clientExistEmail['id'];

            if($phone != null || $phone != ''){
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

    private function checkExistingByPhoneNumber($phone)
    {
        # From tbl client
        $client_phone = UserClient::select('id', 'mail', 'phone')->whereNot('phone', null)->whereNot('phone', '')->get();
        $std_phone = $client_phone->map(function ($item, int $key) {
            return [
                'id' => $item['id'],
                'mail' => $item['mail'],
                'phone' => $this->setPhoneNumber($item['phone'])
            ];
        });

        $client = $std_phone->where('phone', $phone)->first();

        if (!isset($client)) {

            # From tbl client additional info
            $client_phone = UserClientAdditionalInfo::select('client_id', 'category', 'value')->where('category', 'phone')->whereNot('value', null)->whereNot('value', '')->get();
            $std_phone = $client_phone->map(function ($item, int $key) {
                return [
                    'id' => $item['client_id'],
                    'mail' => $item['category'] == 'mail' ? $item['value'] : null,
                    'phone' => $this->setPhoneNumber($item['value'])
                ];
            });

            $client = $std_phone->where('phone', $phone)->first();
        }

        return $client;
    }

    private function checkExistingByEmail($email)
    {
        # From tbl client
        $client_mail = UserClient::select('id', 'mail', 'phone')->whereNot('mail', null)->whereNot('mail', '')->get();

        $client = $client_mail->where('mail', $email)->first();

        if (!isset($client)) {

            # From tbl client additional info
            $client_mail = UserClientAdditionalInfo::select('client_id', 'category', 'value')->where('category', 'mail')->whereNot('value', null)->whereNot('value', '')->get();
            $getMail = $client_mail->map(function ($item, int $key) {
                return [
                    'id' => $item['client_id'],
                    'mail' => $item['category'] == 'mail' ? $item['value'] : null,
                    'phone' => $this->setPhoneNumber($item['value'])
                ];
            });

            $client = $getMail->where('mail', $email)->first();
        }

        return $client;
    }
}