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

class QuestCompleterMailImport implements ToCollection, WithHeadingRow, WithValidation
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
                
            $this->sendMailCompleterQuest($row['email'], $row['fullname'], $row['level']);
               
        }
    }
          
    public function prepareForValidation($data)
    {

   
        $data = [
            'fullname' => $data['fullname'],
            'email' => $data['email'],
            'level' => $data['level'],
        ];

        return $data;
    }

    public function rules(): array
    {
        return [
            '*.fullname' => ['required'],
            '*.email' => ['required', 'email'],
            '*.level' => ['required'],
        ];
    }

}
