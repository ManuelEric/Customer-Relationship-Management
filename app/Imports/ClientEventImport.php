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
use App\Models\Major;
use App\Models\Tag;
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

                // Check existing client by phone number
                $phone = $this->setPhoneNumber($row['phone_number']);

                # From tbl client
                $client_phone = UserClient::select('id', 'phone')->get();
                $std_phone = $client_phone->map(function ($item, int $key) {
                    return [
                        'id' => $item['id'],
                        'phone' => $this->setPhoneNumber($item['phone'])
                    ];
                });

                $client = $std_phone->where('phone', $phone)->first();

                if (!isset($client)) {

                    # From tbl client additional info
                    $client_phone = UserClientAdditionalInfo::select('client_id', 'value')->get();
                    $std_phone = $client_phone->map(function ($item, int $key) {
                        return [
                            'id' => $item['client_id'],
                            'phone' => $this->setPhoneNumber($item['value'])
                        ];
                    });

                    $client = $std_phone->where('phone', $phone)->first();
                }

                // Check existing school
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

                //  insert new client
                $majorDetails = [];
                $destinationCountryDetails = [];

                if (!isset($client)) {
                    $roleId = Role::whereRaw('LOWER(role_name) = (?)', [strtolower($row['audience'])])->first();

                    $fullname = explode(' ', $row['name']);
                    $limit = count($fullname);

                    if (isset($row['itended_major'])) {
                        $majors = explode(', ', $row['itended_major']);
                        foreach ($majors as $major) {
                            $majorFromDB = Major::where('name', $major)->first();
                            if (isset($majorFromDB)) {
                                $majorDetails[] = [
                                    'major_id' => $majorFromDB->id,
                                    'created_at' => Carbon::now(),
                                    'updated_at' => Carbon::now()
                                ];
                            } else {
                                $newMajor = Major::create(['name' => $major]);
                                $majorDetails[] = [
                                    'major_id' => $newMajor->id,
                                    'created_at' => Carbon::now(),
                                    'updated_at' => Carbon::now()
                                ];
                            }
                        }
                    }

                    if (isset($row['destination_country'])) {
                        $countries = explode(', ', $row['destination_country']);
                        foreach ($countries as $country) {
                            switch ($country) {

                                case preg_match('/australia/i', $country) == 1:
                                    $regionName = "Australia";
                                    break;

                                case preg_match("/United State|State|US/i", $country) == 1:
                                    $regionName = "US";
                                    break;

                                case preg_match('/United Kingdom|Kingdom|UK/i', $country) == 1:
                                    $regionName = "UK";
                                    break;

                                case preg_match('/canada/i', $country) == 1:
                                    $regionName = "Canada";
                                    break;

                                default:
                                    $regionName = "Other";
                            }

                            $tagFromDB = Tag::where('name', $regionName)->first();
                            if (isset($tagFromDB)) {
                                $destinationCountryDetails[] = [
                                    'tag_id' => $tagFromDB->id,
                                    'created_at' => Carbon::now(),
                                    'updated_at' => Carbon::now()
                                ];
                            } else {
                                $newCountry = Tag::create(['name' => $regionName]);
                                $destinationCountryDetails[] = [
                                    'tag_id' => $newCountry->id,
                                    'created_at' => Carbon::now(),
                                    'updated_at' => Carbon::now()
                                ];
                            }
                        }
                    }


                    $firstname = $lastname = null;
                    if ($limit > 1) {
                        $lastname = $fullname[$limit - 1];
                        unset($fullname[$limit - 1]);
                        $firstname = implode(" ", $fullname);
                    } else {
                        $firstname = implode(" ", $fullname);
                    }

                    $studentId = null;
                    if ($row['audience'] == 'Student') {
                        $last_id = UserClient::max('st_id');
                        $student_id_without_label = $this->remove_primarykey_label($last_id, 3);
                        $studentId = 'ST-' . $this->add_digit((int) $student_id_without_label + 1, 4);
                    }

                    $st_grade = 12 - ($row['class_of'] - date('Y'));

                    $dataClient = [
                        'sch_id' => isset($school) ? $school : $newSchool->sch_id,
                        'st_id' => isset($studentId) ? $studentId : null,
                        'last_name' => $lastname,
                        'first_name' => $firstname,
                        'mail' => isset($row['email']) && $row['email'] != '' ? $row['email'] : null,
                        'phone' => $phone,
                        'graduation_year' => $row['class_of'],
                        'st_grade' => $st_grade
                    ];

                    if (!$newClient = UserClient::create($dataClient)) {
                        throw new Exception('Failed to store new client');
                        Log::error('Failed to store new client');
                    } else {
                        $thisNewClient = UserClient::find($newClient->id);

                        $thisNewClient->roles()->attach($roleId);
                        isset($majorDetails) ? $thisNewClient->interestMajor()->sync($majorDetails) : '';
                        isset($destinationCountryDetails) ? $thisNewClient->destinationCountries()->sync($destinationCountryDetails) : null;
                    }
                }


                // Insert client event
                $data = [
                    'event_id' => $row['event_name'],
                    'joined_date' => isset($row['date']) ? $row['date'] : null,
                    'client_id' => isset($client) ? $client['id'] : $newClient->id,
                    'lead_id' => $row['leads_source'],
                    'status' => 0,
                ];

                $existClientEvent = ClientEvent::where('event_id', $data['event_id'])
                    ->where('client_id', $data['client_id'])
                    ->where('joined_date', $data['joined_date'])
                    ->first();

                if (!isset($existClientEvent)) {
                    ClientEvent::create($data);
                }
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
            if ($data['leads_source'] == 'School' || $data['leads_source'] == 'Counselor') {
                $data['leads_source'] = 'School/Counselor';
            }
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
            '*.class_of' => ['nullable', 'integer'],
            '*.leads_source' => ['required', 'exists:tbl_lead,lead_id'],
            '*.itended_major' => ['nullable'],
            '*.destination_country' => ['nullable'],
            '*.reason_join' => ['nullable'],
            '*.expectation_join' => ['nullable'],
            // '*.status' => ['required', 'in:0,1'],
        ];
    }
}
