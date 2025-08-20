<?php

namespace App\Imports;

use App\Http\Traits\CheckExistingClient;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\CreateReferralCodeTrait;
use App\Http\Traits\MailingEventOfflineTrait;
use App\Http\Traits\StandardizePhoneNumberTrait;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class InvitationMailInfoImport implements ToCollection, WithHeadingRow, WithValidation
{
    use CheckExistingClient;
    use CreateCustomPrimaryKeyTrait;
    use CreateReferralCodeTrait;

    /**
     * @param  Collection  $collection
     */
    use Importable;

    use MailingEventOfflineTrait;
    use StandardizePhoneNumberTrait;

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
                'notes' => 'WxSFs0LGh',
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
