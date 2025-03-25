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
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Handler
{
    use PrefixSeparatorMeta, StandardizePhoneNumberTrait, SyncClientTrait, CheckExistingClientImport, GetGradeAndGraduationYear, CreateCustomPrimaryKeyTrait;

    public function execute($form_details, $leads)
    {
        $form_name = $form_details['name'];
        $prefix = $this->getPrefix($form_name);
        if ( $prefix == 'NA' # prefix that supposed to be not inserted to CRM
            || strlen($prefix) > 3 # if getPrefix() doesn't return prefix, then return false. let's say meta leads form named "automated chat 24/03/2025", instead of getting the prefix code, we got the whole words so in order to stop this kind of form name. then we're using strlen()
            ) 
            return;

        $identifier = $this->getIdentifier($form_name);

        /**
         * fetch the leads detail
         * insert client to failed meta lead
         */
        $collections = collect($leads);

        $possibilities_of_parent_name = ['nama_anda', 'nama_anda_'];
        $possibilities_of_parent_phone = ['nomor_hp_anda', 'nomor_hp_anda_'];
        $possibilities_of_parent_email = ['email_anda', 'email_anda_'];
        $possibilities_of_child_name = ['nama_anak_anda', 'nama_anak_anda_'];
        $possibilities_of_child_graduation_year = ['tahun_kelulusan_anak_anda', 'tahun_kelulusan_anak_anda_'];
        $possibilities_of_child_school = ['nama_sekolah_anak_anda', 'nama_sekolah_anak_anda_', 'sekolah_anak_anda', 'sekolah_anak_anda_'];

        $incoming_data = [
            'parent_name' => $collections->whereIn('name', $possibilities_of_parent_name)->first()['values'][0],
            'parent_phone' => $collections->whereIn('name', $possibilities_of_parent_phone)->first()['values'][0],
            'parent_email' => $collections->whereIn('name', $possibilities_of_parent_email)->first()['values'][0],
            'child_name' => $collections->whereIn('name', $possibilities_of_child_name)->first()['values'][0],
            'child_graduation_year' => $collections->whereIn('name', $possibilities_of_child_graduation_year)->first()['values'][0],
            'child_school' => $collections->whereIn('name', $possibilities_of_child_school)->first()['values'][0],
        ];
        
        DB::beginTransaction();
        try {

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

                case "EV":
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
                $log_client_details[] = [
                    'client_id' => $selected_child->id,
                    'first_name' => $selected_child->first_name,
                    'last_name' => $selected_child->last_name,
                    'lead_source' => 'LS045', # facebook ads
                    'inputted_from' => 'facebook-api',
                    'clientprog_id' => null
                ];

                ProcessInsertLogClient::dispatch($log_client_details)->onQueue('insert-log-client');
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            FailedMetaLead::create($incoming_data);
            throw new Exception($e->getMessage());
        }

        

    }
}