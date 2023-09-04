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
use App\Http\Traits\RegisterExpressTrait;
use App\Models\ClientEventLogMail;
use App\Models\UserClient;
use App\Models\UserClientAdditionalInfo;
use Illuminate\Support\Facades\Mail;

class ReminderEventImport implements ToCollection, WithHeadingRow, WithValidation
{
    /**
     * @param Collection $collection
     */

    use Importable;
    use StandardizePhoneNumberTrait;
    use CreateCustomPrimaryKeyTrait;
    use CheckExistingClient;
    use RegisterExpressTrait;

    public function collection(Collection $rows)
    {

            foreach ($rows as $row) {
                
                $client = UserClient::where('mail', $row['email'])->first();

                $data['email'] = $row['email'];
                $data['recipient'] = $row['full_name'];
                $data['title'] = "Reminder For STEM+ Wonderlab";
                $data['param'] = [
                    'link' => 'program/event/reg-exp/' . $client['id'] . '/' . $row['event_id']
                ];

                try {

                    Mail::send('mail-template.invitation-email', $data, function ($message) use ($data) {
                        $message->to($data['email'], $data['recipient'])
                            ->subject($data['title']);
                    });
                    $sent_mail = 1;
        
                } catch (Exception $e) {
        
                    $sent_mail = 0;
                    Log::info('Failed to send reminder mail STEM Wonderlab : ' . $e->getMessage());
        
                }
               
                    $logDetails = [
                        'client_id' => $client['id'],
                        'sent_status' => $sent_mail,
                        'category' => 'invitation-mail'
                    ];
            
                    ClientEventLogMail::create($logDetails);
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
            '*.email' => ['required'],
        ];
    }

}
