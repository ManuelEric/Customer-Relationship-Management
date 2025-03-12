<?php

namespace app\Actions\Meta;

use app\Http\Traits\PrefixSeparatorMeta;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Models\FailedMetaLead;
use Illuminate\Support\Collection;

class Handle
{
    use PrefixSeparatorMeta, StandardizePhoneNumberTrait;

    public function execute($form_details, $leads)
    {
        $form_name = $form_details->name;
        $prefix = $this->getPrefix($form_name);
        $identifier = $this->getIdentifier($form_name);

        /**
         * fetch the leads detail
         * insert client to raw data
         */
        $collections = collect($leads);
        $details = [
            'parent_name' => $collections->where('name', 'nama_anda')->first()['value'][0],
            'parent_phone' => $collections->where('name', 'nomor_hp_anda_')->first()['value'][0],
            'parent_email' => $collections->where('name', 'email_anda')->first()['value'][0],
            'child_name' => $collections->where('name', 'nama_anak_anda')->first()['value'][0],
            'child_graduation_year' => $collections->where('name', 'tahun_kelulusan_anak_anda')->first()['value'][0],
            'child_school' => $collections->where('name', 'sekolah_anak_anda')->first()['value'][0],
        ];
        FailedMetaLead::create($details);

        /**
         * insert interested program or client event
         */
        switch ($prefix) 
        {
            case "PR":

                break;

            case "EV":

                break;
        }
    }
}