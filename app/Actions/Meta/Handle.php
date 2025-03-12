<?php

namespace App\Actions\Meta;

use App\Http\Traits\CheckExistingClientImport;
use App\Http\Traits\PrefixSeparatorMeta;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Http\Traits\SyncClientTrait;
use App\Models\FailedMetaLead;
use App\Models\Role;
use App\Models\School;
use App\Models\UserClient;
use Illuminate\Support\Collection;

class Handle
{
    use PrefixSeparatorMeta, StandardizePhoneNumberTrait, SyncClientTrait, CheckExistingClientImport;

    public function execute($form_details, $leads)
    {
        $form_name = $form_details->name;
        $prefix = $this->getPrefix($form_name);
        $identifier = $this->getIdentifier($form_name);

        /**
         * fetch the leads detail
         * insert client to failed meta lead
         */
        $collections = collect($leads);
        $incoming_data = [
            'parent_name' => $collections->where('name', 'nama_anda')->first()['value'][0],
            'parent_phone' => $collections->where('name', 'nomor_hp_anda_')->first()['value'][0],
            'parent_email' => $collections->where('name', 'email_anda')->first()['value'][0],
            'child_name' => $collections->where('name', 'nama_anak_anda')->first()['value'][0],
            'child_graduation_year' => $collections->where('name', 'tahun_kelulusan_anak_anda')->first()['value'][0],
            'child_school' => $collections->where('name', 'sekolah_anak_anda')->first()['value'][0],
        ];
        FailedMetaLead::create($incoming_data);

        /**
         * check if the parent is exists
         */
        $parent = $this->checkExistingClientImport($incoming_data['parent_phone'], $incoming_data['parent_email']);
        if (! $parent['isExist'])
        {
            /**
             * preparing the data for raw data 
             */
            $parentName = $this->explodeName($incoming_data['parent_name']);
    
            $parentDetails = [
                'first_name' => $parentName['firstname'],
                'last_name' => isset($parentName['lastname']) ? $parentName['lastname'] : null,
                'mail' => $incoming_data['parent_email'],
                'phone' => $this->tnSetPhoneNumber($incoming_data['parent_phone']),
                'lead_id' => 'LS045', # facebook ads
            ];

            $roleId = Role::whereRaw('LOWER(role_name) = (?)', ['parent'])->first();

            $parent = UserClient::create($parentDetails);
            $parent->roles()->attach($roleId);
        }
        else 
        {
            $parent = UserClient::withTrashed()->where('id', $parent['id'])->first();
        }

        /**
         * check if the children is exists
         * but we need to check if the field `child` has been filled beforehand
         */
        $selected_child = null;
        if ( isset($incoming_data['child_name']) && isset($incoming_data['child_graduation_year']) && isset($incoming_data['child_school']) )
        {
            $child = $this->checkExistClientRelation('parent', $parent, $incoming_data['child_name']);
            if ( $child['isExist'] && $child['client'] != null )
            {
                $selected_child = $child['client'];
                $selected_child_id[] = $selected_child;
            }
            elseif (! $child['isExist'] )
            {
                $childName = $this->explodeName($incoming_data['child_name']);
                

                if (! $childSchool = School::where('sch_name', $incoming_data['child_school'])->first() )
                    $childSchool = $this->createSchoolIfNotExists($incoming_data['child_school']);
            }
        }

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