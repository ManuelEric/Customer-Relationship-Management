<?php

namespace App\Imports;

use App\Models\ClientEvent;
use App\Models\Corporate;
use App\Models\EdufLead;
use App\Models\Event;
use App\Models\Lead;
use App\Models\Role;
use App\Models\School;
use App\Models\UserClient;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Models\UserClientAdditionalInfo;

class ClientEventImport implements ToCollection, WithHeadingRow, WithValidation
{
    /**
     * @param Collection $collection
     */

    use Importable;
    use StandardizePhoneNumberTrait;
    use CreateCustomPrimaryKeyTrait;

    public function collection(Collection $rows)
    {
        $data = [];

        DB::beginTransaction();
        try {


            foreach ($rows as $row) {

                $phone = $this->setPhoneNumber($row['phone_number']);

                $client_phone = UserClient::select('id', 'phone')->get();
                $std_phone = $client_phone->map(function ($item, int $key) {
                    return [
                        'id' => $item['id'],
                        'phone' => $this->setPhoneNumber($item['phone'])
                    ];
                });

                $client = $std_phone->where('phone', $phone)->first();

                $school = School::where('sch_name', $row['school'])->get()->pluck('sch_id')->first();

                if (!isset($school)) {
                    $last_id = School::max('sch_id');
                    $school_id_without_label = $this->remove_primarykey_label($last_id, 4);
                    $school_id_with_label = 'SCH-' . $this->add_digit($school_id_without_label + 1, 4);

                    if (!$newSchool = School::create(['sch_id' => $school_id_with_label, 'sch_name' => $row['school']])) {

                        throw new Exception('Failed to store new school');
                        Log::error('Failed to store new school');
                    }
                }

                if (!isset($client)) {
                    $roleId = Role::whereRaw('LOWER(role_name) = (?)', [strtolower($row['audience'])])->first();
                    $dataClient = [
                        'sch_id' => isset($school) ? $school : $newSchool->sch_id,
                        'first_name' => $row['name'],
                        'mail' => isset($row['email']) && $row['email'] != '' ? $row['email'] : null,
                        'phone' => $phone,
                        'graduation_year' => $row['class_of'],
                    ];

                    if (!$newClient = UserClient::create($dataClient)) {
                        throw new Exception('Failed to store new client');
                        Log::error('Failed to store new client');
                    } else {
                        $thisNewClient = UserClient::find($newClient->id);

                        $thisNewClient->roles()->attach($roleId);
                    }
                }


                $data = [
                    'event_id' => $row['event_name'],
                    'joined_date' => isset($row['date']) ? $row['date'] : null,
                    'client_id' => isset($client) ? $client['id'] : $newClient->id,
                    'lead_id' => $row['leads_source'],
                    'status' => 0,
                ];


                ClientEvent::create($data);
            }
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Import client event failed : ' . $e->getMessage());
        }
    }

    public function prepareForValidation($data)
    {

        DB::beginTransaction();
        try {

            $event = Event::where('event_title', $data['event_name'])->get()->pluck('event_id')->first();
            $lead = Lead::where('main_lead', $data['leads_source'])->get()->pluck('lead_id')->first();

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Import client event failed : ' . $e->getMessage());
        }


        $data = [
            'event_name' => isset($event) ? $event : $data['event_name'],
            'date' => isset($data['date']) ? Date::excelToDateTimeObject($data['date'])
                ->format('Y-m-d') : null,
            'name' => $data['name'],
            'existing_new_leads' => $data['existing_new_leads'],
            'mentee_non_mentee' => $data['mentee_non_mentee'],
            'audience' => $data['audience'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'school' => $data['school'],
            'class_of' => $data['class_of'],
            'leads_source' => isset($lead) ? $lead : $data['leads_source'],
            'itended_major' => $data['itended_major'],
            'destination_country' => $data['destination_country'],
            'reason_join' => $data['reason_join'],
            'expectation_join' => $data['expectation_join'],
            // 'status' => 0,
        ];

        return $data;
    }

    public function rules(): array
    {
        return [
            '*.event_name' => ['required', 'exists:tbl_events,event_id'],
            '*.date' => ['required', 'date'],
            '*.name' => ['required'],
            '*.existing_new_leads' => ['nullable', 'in:Existing,New'],
            '*.mentee_non_mentee' => ['nullable', 'in:Mentee,Non-mentee'],
            '*.audience' => ['required', 'in:Student,Parent,Teacher'],
            '*.email' => ['nullable', 'email'],
            '*.phone_number' => ['required'],
            '*.school' => ['required'],
            '*.class_of' => ['required'],
            '*.leads_source' => ['required', 'exists:tbl_lead,lead_id'],
            '*.itended_major' => ['nullable'],
            '*.destination_country' => ['nullable'],
            '*.reason_join' => ['nullable'],
            '*.expectation_join' => ['nullable'],
            // '*.status' => ['required', 'in:0,1'],
        ];
    }

    // public function customValidationMessages()
    // {
    //     return [
    //         '*.partner_name.required_if' => 'The :attribute field is required when conversion lead is All-In Partners.',
    //         '*.edufair_name.required_if' => 'The :attribute field is required when conversion lead is External Edufair.',
    //         '*.kol_name.required_if' => 'The :attribute field is required when conversion lead is KOL.',
    //     ];
    // }
}
