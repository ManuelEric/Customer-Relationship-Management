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
use App\Models\Event;
use App\Models\UserClient;
use App\Models\UserClientAdditionalInfo;
use Illuminate\Support\Facades\Mail;

class InvitationMailInfoImport implements ToCollection, WithHeadingRow, WithValidation
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
            
            $data = [
                'client' => [
                    'client_id' => $row['client_id'],
                    'email' => $row['email'],
                    'recipient' => $row['full_name'],
                ],
                'event_id' => $row['event_id'],
                'notes' => 'WxSFs0LGh'
            ];

            $this->sendMailInvitationInfo($data, 'first-send');
               
        }
    }
          
    

    public function prepareForValidation($data)
    {

   
        $data = [
            'client_id' => $data['client_id'],
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'event_id' => $data['event_id'],
        ];

        return $data;
    }

    public function rules(): array
    {
        return [
            '*.client_id' => ['required'],
            '*.full_name' => ['required'],
            '*.email' => ['required', 'exists:tbl_client,mail'],
            '*.event_id' => ['required', 'exists:tbl_events,event_id'],
        ];
    }

}
