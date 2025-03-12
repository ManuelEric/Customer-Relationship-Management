<?php

namespace App\Actions\Meta;

use App\Http\Traits\CheckExistingClientImport;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\GetGradeAndGraduationYear;
use App\Http\Traits\PrefixSeparatorMeta;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Http\Traits\SyncClientTrait;
use App\Jobs\Client\ProcessInsertLogClient;
use App\Models\ClientEvent;
use App\Models\FailedMetaLead;
use App\Models\Program;
use App\Models\Role;
use App\Models\School;
use App\Models\UserClient;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class Handle
{
    use PrefixSeparatorMeta, StandardizePhoneNumberTrait, SyncClientTrait, CheckExistingClientImport, GetGradeAndGraduationYear, CreateCustomPrimaryKeyTrait;

    public function execute($form_details, $leads)
    {
        $form_name = $form_details['name'];
        $prefix = $this->getPrefix($form_name);
        $identifier = $this->getIdentifier($form_name);

        /**
         * fetch the leads detail
         * insert client to failed meta lead
         */
        $collections = collect($leads);
        $incoming_data = [
            'parent_name' => $collections->where('name', 'nama_anda')->first()['values'][0],
            'parent_phone' => $collections->where('name', 'nomor_hp_anda_')->first()['values'][0],
            'parent_email' => $collections->where('name', 'email_anda_')->first()['values'][0],
            'child_name' => $collections->where('name', 'nama_anak_anda')->first()['values'][0],
            'child_graduation_year' => $collections->where('name', 'tahun_kelulusan_anak_anda')->first()['values'][0],
            'child_school' => $collections->where('name', 'nama_sekolah_anak_anda')->first()['values'][0],
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
        $selected_child = $st_grade = null;
        if ( isset($incoming_data['child_name']) && isset($incoming_data['child_graduation_year']) && isset($incoming_data['child_school']) )
        {
            $child = $this->checkExistClientRelation('parent', $parent, $incoming_data['child_name']);
            if ( $child['isExist'] && $child['client'] != null )
            {
                $selected_child = $child['client'];
            }
            elseif (! $child['isExist'] )
            {
                $childName = $this->explodeName($incoming_data['child_name']);
                if (! $childSchool = School::where('sch_name', $incoming_data['child_school'])->first() )
                    $childSchool = $this->createSchoolIfNotExists($incoming_data['child_school']);

                if ( isset($incoming_data['child_graduation_year']) )
                    $st_grade = $this->getGradeByGraduationYear($incoming_data['child_graduation_year']);

                $childDetails = [
                    'first_name' => $childName['firstname'],
                    'last_name' => isset($childName['lastname']) ? $childName['lastname'] : null,
                    'sch_id' => $childSchool->sch_id,
                    'graduation_year' => $incoming_data['child_graduation_year'],
                    'st_grade' => $st_grade,
                    'lead_id' => 'LS045',
                ];

                $roleId = Role::whereRaw('LOWER(role_name) = (?)', ['student'])->first();

                $selected_child = UserClient::create($childDetails);
                $selected_child->roles()->attach($roleId);
                $parent->childrens()->attach($selected_child);
            }
        }

        /**
         * insert interested program or client event
         */
        switch ($prefix) 
        {
            case "PR":
                if ( $selected_child )
                {
                    $interest_program_details[] = [
                        'prog_id' => $identifier,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                    
                    $selected_child->interestPrograms()->syncWithoutDetaching($interest_program_details);
                }
                break;

            case "EVT":
                $client_event_details = [
                    'ticket_id' => NULL,
                    'client_id' => $parent->id,
                    'child_id' => $selected_child ? $selected_child->id : NULL,
                    'event_id' => $identifier,
                    'lead_id' => 'LS045', # facebook ads
                    'registration_type' => 'PR',
                    'number_of_attend' => 1
                ];

                ClientEvent::create($client_event_details);
                break;
        }


        /**
         * insert into client log
         * only if they're submitted child's information
         */
        if ( $selected_child )
        {
            $log_client_details = [
                'client_id' => $selected_child->id,
                'first_name' => $selected_child->first_name,
                'last_name' => $selected_child->last_name,
                'lead_source' => 'LS045', # facebook ads
                'inputted_from' => 'facebook-api',
                'clientprog_id' => null
            ];

            ProcessInsertLogClient::dispatch($log_client_details)->onQueue('insert-log-client');
        }

    }
}