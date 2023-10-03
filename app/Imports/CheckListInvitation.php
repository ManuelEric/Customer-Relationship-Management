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

class CheckListInvitation implements ToCollection, WithHeadingRow, WithValidation
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
        $data = [];
        $dataChild = [];
        foreach ($rows as $row) {
            $client = UserClient::where('mail', $row['parent_mail'])->first();
            $clientMail = UserClient::where('mail', $row['parent_mail'])->get();

            if(isset($client)){
                $childs = $client->childrens()->get();
    
                $dataChild = [];
                foreach ($childs as $child) {
                    $dataChild[] = [
                        'full_name_db' => $child->full_name,
                        'full_name_excel' => $row['name'],
                    ];
                }
                $data[]=[
                    'client_id' => $client->id,
                    'count_mail' => $clientMail->count(),
                    'parent_name_db' => $client->full_name,
                    'parent_name_excel' => $row['parent_name'],
                    'parent_mail_db' => $client->mail,
                    'parent_mail_excel' => $row['parent_mail'],
                    'parent_phone_db' => $client->phone,
                    'parent_phone_excel' => $row['parent_phone'],
                    'phoneIsEqual' => $client->phone == $row['parent_phone'] ? 'true' : 'false',
                    'parentNameIsEqual' => $client->full_name == $row['parent_name'] ? 'true' : 'false',
                    'childs' => [
                        $dataChild
                    ]
                ];

                
            }else{
                $data[]=[
                    'client_id' => null,
                    'parent_name' => $row['parent_name'],
                    'parent_phone' => $row['parent_phone'],
                    'parent_mail' => $row['parent_mail'],
                ];

            }

        }

        Log::info($data);

    }
          
    

    public function prepareForValidation($data)
    {
        $data = [
            'name' => $data['name'],
            'parent_name' => $data['parent_name'],
            'parent_mail' => $data['parent_mail'],
            'parent_phone' => $data['parent_phone'],
        ];


        return $data;
    }

    public function rules(): array
    {
        return [
            '*.name' => ['nullable'],
            '*.parent_name' => ['nullable'],
            '*.parent_mail' => ['required'],
            '*.parent_phone' => ['nullable'],
        ];
    }

}
