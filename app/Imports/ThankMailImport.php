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
use App\Models\ClientEvent;
use App\Models\Event;
use App\Models\UserClient;
use App\Models\UserClientAdditionalInfo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class ThankMailImport implements ToCollection, WithHeadingRow, WithValidation
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
                $this->register($row['email'], $row['event_id'], 'VVIP');
                               
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
