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

class InvitationMailVIPImport implements ToCollection, WithHeadingRow, WithValidation
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

            $this->sendMailInvitation($row['client_id'], $row['event_id'], $row['child_id'], 'WxSFs0LGh');

        }
    }

    public function prepareForValidation($data)
    {

        $data = [
            'event_id' => $data['event_id'],
            'full_name' => $data['full_name'],
            'client_id' => $data['client_id'],
            'child_id' => $data['child_id'],
        ];

        return $data;
    }

    public function rules(): array
    {
        return [
            '*.event_id' => ['required'],
            '*.full_name' => ['required'],
            '*.client_id' => ['required', 'exists:tbl_client,id'],
            '*.child_id' => ['nullable', 'exists:tbl_client,id'],
        ];
    }
}
