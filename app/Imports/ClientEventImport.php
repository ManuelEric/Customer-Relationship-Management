<?php

namespace App\Imports;

use App\Http\Traits\CheckExistingClient;
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
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;

class ClientEventImport implements ToCollection, WithHeadingRow, WithValidation

{
    /**
     * @param Collection $collection
     */

    use Importable;
    use StandardizePhoneNumberTrait;
    use CreateCustomPrimaryKeyTrait;
    use CheckExistingClient;

    public function collection(Collection $rows)
    {
        $data = [];

        DB::beginTransaction();
        try {


            foreach ($rows as $row) {

                $existClient = ['isExist' => false];

                $phone = $this->setPhoneNumber($row['phone_number']);

                // Check existing client 
                $existClient = $this->checkExistingClient($phone, $row['email']);

                // Check existing school
                $school = School::where('sch_name', $row['school'])->get()->pluck('sch_id')->first();

                $status = $row['status'] == 'Join' ? 0 : 1;

                if (!isset($school)) {
                    $newSchool = $this->createSchoolIfNotExists($row['school']);
                }

                # From tbl client
                // $client_phone = UserClient::select('id', 'phone')->get();
                // $std_phone = $client_phone->map(function ($item, int $key) {
                //     return [
                //         'id' => $item['id'],
                //         'phone' => $this->setPhoneNumber($item['phone'])
                //     ];
                // });

                // $client = $std_phone->where('phone', $phone)->first();

                // if (!isset($client)) {

                //     # From tbl client additional info
                //     $client_phone = UserClientAdditionalInfo::select('client_id', 'value')->get();
                //     $std_phone = $client_phone->map(function ($item, int $key) {
                //         return [
                //             'id' => $item['client_id'],
                //             'phone' => $this->setPhoneNumber($item['value'])
                //         ];
                //     });

                //     $client = $std_phone->where('phone', $phone)->first();
                // }


                $majorDetails = [];
                $destinationCountryDetails = [];

                //  Set major
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

                // Set destination country
                if (isset($row['destination_country'])) {
                    $countries = explode(', ', $row['destination_country']);
                    foreach ($countries as $country) {
                        switch ($country) {

                            case preg_match('/australia/i', $country) == 1:
                                $regionName = "Australia";
                                break;

                            case preg_match(
                                "/United State|State|US/i",
                                $country
                            ) == 1:
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
                                'country_name' => $regionName == 'Other' ? $country : null,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ];
                        } else {
                            // $newCountry = Tag::create(['name' => $regionName]);
                            $destinationCountryDetails[] = [
                                'tag_id' => 7,
                                'country_name' => $country,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ];
                        }
                    }
                }

                //  insert new client
                if (!$existClient['isExist']) {
                    $roleId = Role::whereRaw('LOWER(role_name) = (?)', [strtolower($row['audience'])])->first();

                    $fullname = explode(' ', $row['name']);
                    $limit = count($fullname);

                    $firstname = $lastname = null;
                    if ($limit > 1) {
                        $lastname = $fullname[$limit - 1];
                        unset($fullname[$limit - 1]);
                        $firstname = implode(" ", $fullname);
                    } else {
                        $firstname = implode(" ", $fullname);
                    }

                    $studentId = null;
                    // if ($row['audience'] == 'Student') {
                    //     $last_id = UserClient::max('st_id');
                    //     $student_id_without_label = $this->remove_primarykey_label($last_id, 3);
                    //     $studentId = 'ST-' . $this->add_digit((int) $student_id_without_label + 1, 4);
                    // }

                    if ($row['class_of'] != null || $row['class_of'] != '') {
                        $st_grade = 12 - ($row['class_of'] - date('Y'));
                    }

                    $dataClient = [
                        'sch_id' => isset($school) ? $school : $newSchool->sch_id,
                        'st_id' => isset($studentId) ? $studentId : null,
                        'last_name' => $lastname,
                        'first_name' => $firstname,
                        'mail' => isset($row['email']) && $row['email'] != '' ? $row['email'] : null,
                        'phone' => $phone,
                        'graduation_year' => $row['class_of'] != null || $row['class_of'] != '' ? $row['class_of'] : null,
                        'st_grade' => $row['class_of'] != null || $row['class_of'] != '' ? $st_grade : null,
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
                } else {
                    // Exist client
                    $existClientStudent = UserClient::find($existClient['id']);
                    isset($majorDetails) ? $existClientStudent->interestMajor()->sync($majorDetails) : '';
                    isset($destinationCountryDetails) ? $existClientStudent->destinationCountries()->sync($destinationCountryDetails) : null;
                }

                // Insert client event
                $data = [
                    'event_id' => $row['event_name'],
                    'joined_date' => isset($row['date']) ? $row['date'] : null,
                    'client_id' => $existClient['isExist'] ? $existClient['id'] : $newClient->id,
                    'lead_id' => $row['lead'],
                    'status' => $status,
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


            $event_name = Event::where('event_title', $data['event_name'])->get()->pluck('event_id')->first();
            if ($data['lead'] == 'School' || $data['lead'] == 'Counselor') {
                $data['lead'] = 'School/Counselor';
            }

            if ($data['lead'] == 'KOL') {
                $lead = 'KOL';
            } else {
                $lead = Lead::where('main_lead', $data['lead'])->get()->pluck('lead_id')->first();
            }

            // $event = Event::where('event_title', $data['event'])->get()->pluck('event_id')->first();
            $getAllEduf = EdufLead::all();
            $edufair = $getAllEduf->where('organizerName', $data['edufair'])->pluck('id')->first();
            $partner = Corporate::where('corp_name', $data['partner'])->get()->pluck('corp_id')->first();
            $kol = Lead::where('main_lead', 'KOL')->where('sub_lead', $data['kol'])->get()->pluck('lead_id')->first();

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Import client event failed : ' . $e->getMessage());
        }


        $data = [
            'event_name' => isset($event_name) ? $event_name : $data['event_name'],
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
            'lead' => isset($lead) ? $lead : $data['lead'],
            // 'event' => isset($event) ? $event : $data['event'],
            'partner' => isset($partner) ? $partner : $data['partner'],
            'edufair' => isset($edufair) ? $edufair : $data['edufair'],
            'kol' => isset($kol) ? $kol : $data['kol'],
            'itended_major' => $data['itended_major'],
            'destination_country' => $data['destination_country'],
            'reason_join' => $data['reason_join'],
            'expectation_join' => $data['expectation_join'],
            'status' => $data['status'],
        ];

        return $data;
    }

    public function rules(): array
    {
        return [
            '*.event_name' => ['required', 'exists:tbl_events,event_id'],
            '*.date' => ['required', 'date'],
            '*.name' => ['required'],
            '*.existing_new_leads' => ['required', 'in:Existing,New'],
            '*.mentee_non_mentee' => ['required', 'in:Mentee,Non-mentee'],
            '*.audience' => ['required', 'in:Student,Parent,Teacher'],
            '*.email' => ['required', 'email'],
            '*.phone_number' => ['required'],
            '*.school' => ['required'],
            '*.class_of' => ['nullable', 'integer'],
            '*.lead' => ['required'],
            // '*.event' => ['required_if:lead,LS003', 'nullable', 'exists:tbl_events,event_id'],
            '*.partner' => ['required_if:lead,LS010', 'nullable', 'exists:tbl_corp,corp_id'],
            '*.edufair' => ['required_if:lead,LS017', 'nullable', 'exists:tbl_eduf_lead,id'],
            '*.kol' => ['required_if:lead,KOL', 'nullable', 'exists:tbl_lead,lead_id'],
            '*.itended_major' => ['nullable'],
            '*.destination_country' => ['nullable'],
            '*.reason_join' => ['nullable'],
            '*.expectation_join' => ['nullable'],
            '*.status' => ['required', 'in:Join,Attend'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            // '*.event.required_if' => 'The :attribute is required when lead All-In Event',
            '*.partner.required_if' => 'The :attribute is required when lead All-In Partners',
            '*.edufair.required_if' => 'The :attribute is required when lead External Edufair',
        ];
    }

    private function createSchoolIfNotExists($sch_name)
    {
        $last_id = School::max('sch_id');
        $school_id_without_label = $this->remove_primarykey_label($last_id, 4);
        $school_id_with_label = 'SCH-' . $this->add_digit($school_id_without_label + 1, 4);

        $newSchool = School::create(['sch_id' => $school_id_with_label, 'sch_name' => $sch_name]);

        return $newSchool;
    }

    // private function checkExistingClient($phone, $email)
    // {
    //     $existClient = [];

    //     // Check existing client by phone number and email
    //     $clientExistPhone = $this->checkExistingByPhoneNumber($phone);
    //     $clientExistEmail = $this->checkExistingByEmail($email);

    //     if ($clientExistPhone && $clientExistEmail) {
    //         $existClient['isExist'] = true;
    //         $existClient['id'] = $clientExistPhone['id'];
    //     } else if ($clientExistPhone && !$clientExistEmail) {
    //         $existClient['isExist'] = true;
    //         $existClient['id'] = $clientExistPhone['id'];

    //         // Add email to client addtional info
    //         $additionalInfo = [
    //             'client_id' => $clientExistPhone['id'],
    //             'category' => 'mail',
    //             'value' => $email,
    //         ];
    //         UserClientAdditionalInfo::create($additionalInfo);
    //     } else if (!$clientExistPhone && $clientExistEmail) {
    //         $existClient['isExist'] = true;
    //         $existClient['id'] = $clientExistEmail['id'];

    //         // Add email to client addtional info
    //         $additionalInfo = [
    //             'client_id' => $clientExistEmail['id'],
    //             'category' => 'phone',
    //             'value' => $phone,
    //         ];
    //         UserClientAdditionalInfo::create($additionalInfo);
    //     } else {
    //         $existClient['isExist'] = false;
    //     }

    //     return $existClient;
    // }

    // public function checkExistingByPhoneNumber($phone)
    // {
    //     # From tbl client
    //     $client_phone = UserClient::select('id', 'mail', 'phone')->whereNot('phone', null)->whereNot('phone', '')->get();
    //     $std_phone = $client_phone->map(function ($item, int $key) {
    //         return [
    //             'id' => $item['id'],
    //             'mail' => $item['mail'],
    //             'phone' => $this->setPhoneNumber($item['phone'])
    //         ];
    //     });

    //     $client = $std_phone->where('phone', $phone)->first();

    //     if (!isset($client)) {

    //         # From tbl client additional info
    //         $client_phone = UserClientAdditionalInfo::select('client_id', 'category', 'value')->where('category', 'phone')->whereNot('value', null)->whereNot('value', '')->get();
    //         $std_phone = $client_phone->map(function ($item, int $key) {
    //             return [
    //                 'id' => $item['client_id'],
    //                 'mail' => $item['category'] == 'mail' ? $item['value'] : null,
    //                 'phone' => $this->setPhoneNumber($item['value'])
    //             ];
    //         });

    //         $client = $std_phone->where('phone', $phone)->first();
    //     }

    //     return $client;
    // }

    // public function checkExistingByEmail($email)
    // {
    //     # From tbl client
    //     $client_mail = UserClient::select('id', 'mail', 'phone')->whereNot('mail', null)->whereNot('mail', '')->get();

    //     $client = $client_mail->where('mail', $email)->first();

    //     if (!isset($client)) {

    //         # From tbl client additional info
    //         $client_mail = UserClientAdditionalInfo::select('client_id', 'category', 'value')->where('category', 'mail')->whereNot('value', null)->whereNot('value', '')->get();
    //         $getMail = $client_mail->map(function ($item, int $key) {
    //             return [
    //                 'id' => $item['client_id'],
    //                 'mail' => $item['category'] == 'mail' ? $item['value'] : null,
    //                 'phone' => $this->setPhoneNumber($item['value'])
    //             ];
    //         });

    //         $client = $getMail->where('mail', $email)->first();
    //     }

    //     return $client;
    // }
}
