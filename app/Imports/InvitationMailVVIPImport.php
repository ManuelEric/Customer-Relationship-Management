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

class InvitationMailVVIPImport implements ToCollection, WithHeadingRow, WithValidation
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

            $this->sendMailInvitation($row['email'], $row['event_id'], 'first-send', $row['index_child'], 'BtSF0x1hK');

        }
    }

    public function prepareForValidation($data)
    {
        $data = [
            'event_id' => $data['event_id'],
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'index_child' => $data['index_child'],
        ];

        return $data;
    }

    public function rules(): array
    {
        return [
            '*.event_id' => ['required'],
            '*.full_name' => ['required'],
            '*.email' => ['required', 'exists:tbl_client,mail'],
            '*.index_child' => ['required'],
        ];
    }
}
