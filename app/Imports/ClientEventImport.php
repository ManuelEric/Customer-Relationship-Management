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
use App\Http\Traits\SplitNameTrait;
use App\Interfaces\ClientRepositoryInterface;
use App\Models\ClientEventLogMail;
use App\Models\Major;
use App\Models\Tag;
use App\Models\UserClientAdditionalInfo;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Throwable;

class ClientEventImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure

{
    /**
     * @param Collection $collection
     */

    use Importable, SkipsErrors, SkipsFailures;
    use StandardizePhoneNumberTrait;
    use CreateCustomPrimaryKeyTrait;
    use CheckExistingClient;
    use SplitNameTrait;

    private ClientRepositoryInterface $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    public function onError(Throwable $error)
    {
        echo 'a';
    }

    public function collection(Collection $rows)
    {

        $data = [];

        DB::beginTransaction();
        try {

            foreach ($rows as $row) {

                # initiate variables
                $status = $row['status'] == 'Join' ? 0 : 1;

                // Check existing school
                if (!$school = School::where('sch_name', $row['school'])->pluck('sch_id')->first())
                    $school = $this->createSchoolIfNotExists($row['school']);

                // Set major
                $majorDetails = $this->splitMajorIntoArray($row['itended_major']);

                // Set destination country
                $destinationCountryDetails = $this->splitDestinationCountriesIntoArray($row['destination_country']);

                switch ($row['audience']) {
                    case 'Student':
                        $roleSub = 'Parent';
                        break;
                    case 'Parent':
                        $roleSub = 'Student';
                        break;
                }
 
                $createdMainClient = $this->createClient($row, 'main', $row['audience'], $majorDetails, $destinationCountryDetails, $school);
                $createdSubClient = $row['audience'] == 'Student' || $row['audience'] == 'Parent' ? $this->createClient($row, 'sub', $roleSub, $majorDetails, $destinationCountryDetails, $school) : null;

                // Create relation parent and student
                if(($row['audience'] == 'Parent' || $row['audience'] == 'Student') && isset($createdSubClient)){
                    switch ($row['audience']) {
                        case 'Parent':
                            $parent = UserClient::find($createdMainClient);
                            $parent->childrens()->syncWithoutDetaching($createdSubClient);
                            break;

                        case 'Student':
                            $parent = UserClient::find($createdSubClient);
                            $parent->childrens()->syncWithoutDetaching($createdMainClient);
                            break;
                    }
                }

                // Insert client event
                $data = [
                    'event_id' => $row['event_name'],
                    'joined_date' => isset($row['date']) ? $row['date'] : null,
                    'client_id' => $createdMainClient,
                    'lead_id' => $row['lead'],
                    'status' => $status,
                    'registration_type' => isset($row['registration_type']) ? $row['registration_type'] : null,
                    'number_of_attend' => isset($row['number_of_attend']) ? $row['number_of_attend'] : 1,
                    'referral_code' => isset($row['referral_code']) ? $row['referral_code'] : null,
                ];

                # add additional identification
                if ($row['audience'] == "Parent")
                    $data['child_id'] = $createdSubClient;
                elseif ($row['audience'] == "Student")
                    $data['parent_id'] = $createdMainClient;

                $existClientEvent = ClientEvent::where('event_id', $data['event_id'])
                    ->where('client_id', $data['client_id'])
                    ->where('joined_date', $data['joined_date'])
                    ->first();

                if (!isset($existClientEvent)) {
                    $insertedClientEvent = ClientEvent::create($data);

                    # add to log client event 
                    # to trigger the cron for send the qr email
                    ClientEventLogMail::create([
                        'clientevent_id' => $insertedClientEvent,
                        'event_id' => $row['event_name'],
                        'sent_status' => 0,
                        'category' => 'qrcode-mail'
                    ]);
                }
            }
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Import client event failed : ' . $e->getMessage().' | Line '.$e->getLine());

        }

    }

    public function prepareForValidation($data)
    {

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

        $data = [
            'event_name' => isset($event_name) ? $event_name : $data['event_name'],
            'date' => isset($data['date']) ? Date::excelToDateTimeObject($data['date'])->format('Y-m-d') : null,
            'audience' => $data['audience'],
            'name' => $data['name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'child_parent_name' => $data['child_parent_name'],
            'child_parent_email' => $data['child_parent_email'],
            'child_parent_phone_number' => $data['child_parent_phone_number'],
            // 'existing_new_leads' => $data['existing_new_leads'],
            // 'mentee_non_mentee' => $data['mentee_non_mentee'],
            'registration_type' => $data['registration_type'],
            'school' => $data['school'],
            'class_of' => $data['class_of'],
            'lead' => isset($lead) ? $lead : $data['lead'],
            // 'event' => isset($event) ? $event : $data['event'],
            'partner' => isset($partner) ? $partner : $data['partner'],
            'edufair' => isset($edufair) ? $edufair : $data['edufair'],
            'kol' => isset($kol) ? $kol : $data['kol'],
            'itended_major' => $data['itended_major'],
            'destination_country' => $data['destination_country'],
            'number_of_attend' => $data['number_of_attend'],
            'referral_code' => $data['referral_code'],
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
            '*.audience' => ['required', 'in:Student,Parent,Teacher'],
            '*.name' => ['required'],
            '*.email' => ['required', 'email'],
            '*.phone_number' => ['required'],
            '*.child_parent_name' => ['nullable'],
            '*.child_parent_email' => ['nullable'],
            '*.child_parent_phone_number' => ['nullable'],
            '*.registration_type' => ['nullable', 'in:PR,OTS'],
            // '*.existing_new_leads' => ['required', 'in:Existing,New'],
            // '*.mentee_non_mentee' => ['required', 'in:Mentee,Non-mentee'],
            '*.school' => ['required'],
            '*.class_of' => ['nullable', 'integer'],
            '*.lead' => ['required'],
            // '*.event' => ['required_if:lead,LS003', 'nullable', 'exists:tbl_events,event_id'],
            '*.partner' => ['required_if:lead,LS010', 'nullable', 'exists:tbl_corp,corp_id'],
            '*.edufair' => ['required_if:lead,LS017', 'nullable', 'exists:tbl_eduf_lead,id'],
            '*.kol' => ['required_if:lead,KOL', 'nullable', 'exists:tbl_lead,lead_id'],
            '*.itended_major' => ['nullable'],
            '*.destination_country' => ['nullable'],
            '*.number_of_attend' => ['nullable'],
            '*.referral_code' => ['nullable'],
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

        return $newSchool->sch_id;
    }

    ## 
    private function createClient($row, $type, $role, $majorDetails, $destinationCountryDetails, $school)
    {
        $clientId = '';

        switch ($type) {
            case 'main':
                $phone = $this->setPhoneNumber($row['phone_number']);
                $existClient = $this->checkExistingClient($phone, $row['email']);
                $email = $row['email'];
                $fullname = $row['name'];
                break;

            case 'sub':
                $phone = isset($row['child_parent_phone_number']) ? $this->setPhoneNumber($row['phone_number']) : null;
                $email = isset($row['child_parent_email']) ? $row['child_parent_email'] : null;
                $existClient = $this->checkExistingClient($phone, $email);
                $fullname = $row['child_parent_name'];
                break;
        }

        $firstname = $this->split($fullname)['first_name'];
        $lastname = $this->split($fullname)['last_name'];

        $roleId = Role::whereRaw('LOWER(role_name) = (?)', [strtolower($role)])->first();

        switch ($role) {
            case 'Student':
                if (!$existClient['isExist']) {
                    
                    $studentId = null;
        
                    if ($row['class_of'] != null || $row['class_of'] != '') {
                        $st_grade = 12 - ($row['class_of'] - date('Y'));
                    }
        
                    $dataClient = [
                        'sch_id' => $school,
                        'st_id' => isset($studentId) ? $studentId : null,
                        'last_name' => $lastname,
                        'first_name' => $firstname,
                        'mail' => $email,
                        'phone' => $phone,
                        'graduation_year' => $row['class_of'] != null || $row['class_of'] != '' ? $row['class_of'] : null,
                        'st_grade' => $row['class_of'] != null || $row['class_of'] != '' ? $st_grade : null,
                        'register_as' => $row['audience']
                    ];
        
                    if (!$newClient = UserClient::create($dataClient)) {
                        throw new Exception('Failed to store new client');
                        Log::error('Failed to store new client');
                    } else {
                        $clientId = $newClient->id;
                        $thisNewClient = UserClient::find($newClient->id);
        
                        $thisNewClient->roles()->attach($roleId);
                        isset($majorDetails) ? $thisNewClient->interestMajor()->sync($majorDetails) : '';
                        isset($destinationCountryDetails) ? $thisNewClient->destinationCountries()->sync($destinationCountryDetails) : null;
                    }
                } else {
                    // Exist client
                    $clientId = $existClient['id'];
                    $existClientStudent = UserClient::find($existClient['id']);
                    isset($majorDetails) ? $existClientStudent->interestMajor()->sync($majorDetails) : '';
                    isset($destinationCountryDetails) ? $existClientStudent->destinationCountries()->sync($destinationCountryDetails) : null;
                }
                
                break;

            case 'Parent':

                if (!$existClient['isExist']) {
                
                    $dataClient = [
                        'last_name' => $lastname,
                        'first_name' => $firstname,
                        'mail' => $email,
                        'phone' => $phone,
                        'register_as' => $row['audience']
                    ];
        
                    if (!$newClient = UserClient::create($dataClient)) {
                        throw new Exception('Failed to store new client');
                        Log::error('Failed to store new client');
                    } else {
                        $clientId = $newClient->id;
                        $thisNewClient = UserClient::find($newClient->id);
        
                        $thisNewClient->roles()->attach($roleId);
                    }
                }else{
                    $clientId = $existClient['id'];
                }
                break;

            case 'Teacher':
                if (!$existClient['isExist']) {
                
                    $dataClient = [
                        'sch_id' => $school,
                        'last_name' => $lastname,
                        'first_name' => $firstname,
                        'mail' => $email,
                        'phone' => $phone,
                        'register_as' => $row['audience']
                    ];
        
                    if (!$newClient = UserClient::create($dataClient)) {
                        throw new Exception('Failed to store new client');
                        Log::error('Failed to store new client');
                    } else {
                        $clientId = $newClient->id;
                        $thisNewClient = UserClient::find($newClient->id);
        
                        $thisNewClient->roles()->attach($roleId);
                    }
                }else{
                    $clientId = $existClient['id'];
                }
                break;
            
        }

        return $clientId;
       
    }

    private function splitMajorIntoArray($majors)
    {
        if (!isset($majors) || is_null($majors))
            return [];
        
        $major_arr = explode(', ', $majors);
        foreach ($major_arr as $major) {
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

        return $majorDetails;
        
    }

    private function splitDestinationCountriesIntoArray($countries) 
    {
        if (!isset($countries) || is_null($countries)) 
            return [];

        $countries = explode(', ', $countries);
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

        return $destinationCountryDetails;
        
    }
}