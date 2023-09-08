<?php

namespace App\Imports;

use App\Http\Traits\CheckExistingClient;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Http\Traits\StandardizePhoneNumberTrait;
use Maatwebsite\Excel\Concerns\Importable;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\CreateReferralCodeTrait;
use App\Http\Traits\MailingEventOfflineTrait;
use App\Models\ClientEventLogMail;
use App\Models\UserClient;
use App\Models\UserClientAdditionalInfo;
use Illuminate\Support\Facades\Mail;

class InvitationMailImport implements ToCollection, WithHeadingRow, WithValidation
{
    /**
     * @param Collection $collection
     */

    use Importable;
    use StandardizePhoneNumberTrait;
    use CreateCustomPrimaryKeyTrait;
    use CheckExistingClient;
    use MailingEventOfflineTrait;
    use CreateReferralCodeTrait;

    public function collection(Collection $rows)
    {

            foreach ($rows as $row) {
                
                $client = UserClient::where('mail', $row['email'])->first();

                $data['email'] = $row['email'];
                $data['event_id'] = $row['event_id'];
                $data['recipient'] = $row['full_name'];
                $data['title'] = "Invitation For STEM+ Wonderlab";
                $data['param'] = [
                    'refcode' => $this->createReferralCode($client->first_name, $client->id),
                    'event' => $row['event_id'],
                    // 'link' => 'program/event/reg-exp/' . $client['id'] . '/' . $row['event_id']
                ];

                $this->sendMailInvitation($data, $client, 'first-send');
               
                }
            }
          
    

    public function prepareForValidation($data)
    {

   
        $data = [
            'event_id' => $data['event_id'],
            'full_name' => $data['full_name'],
            'email' => $data['email'],
        ];

        return $data;
    }

    public function rules(): array
    {
        return [
            '*.event_id' => ['required'],
            '*.full_name' => ['required'],
            '*.email' => ['required', 'exists:tbl_client,mail'],
        ];
    }

}
