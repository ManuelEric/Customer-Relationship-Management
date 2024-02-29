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
use App\Http\Traits\MailingEventOfflineTrait;
use App\Models\ClientEventLogMail;
use App\Models\UserClient;
use App\Models\UserClientAdditionalInfo;
use Illuminate\Support\Facades\Mail;

class ReminderRegistrationImport implements ToCollection, WithHeadingRow, WithValidation
{
    /**
     * @param Collection $collection
     */

    use Importable;
    use StandardizePhoneNumberTrait;
    use CreateCustomPrimaryKeyTrait;
    use CheckExistingClient;
    use MailingEventOfflineTrait;

    public function collection(Collection $rows)
    {

            foreach ($rows as $row) {
                    
                $this->sendMailReminder($row['client_id'], $row['event_id'], 'first-send', 'registration', $row['child_id'], $row['notes']);
                                   
            }
                
    }
          
    

    public function prepareForValidation($data)
    {

   
        $data = [
            'client_id' => $data['client_id'],
            'event_id' => $data['event_id'],
            'full_name' => $data['full_name'],
            'child_id' => $data['child_id'],
            'notes' => $data['notes'],
        ];

        return $data;
    }

    public function rules(): array
    {
        return [
            '*.client_id' => ['required'],
            '*.event_id' => ['required'],
            '*.full_name' => ['required'],
            '*.child_id' => ['nullable'],
            '*.notes' => ['required'],  
        ];
    }

}
