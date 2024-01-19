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
use App\Http\Traits\LoggingTrait;
use App\Http\Traits\SplitNameTrait;
use App\Http\Traits\SyncClientTrait;
use App\Interfaces\ClientRepositoryInterface;
use App\Jobs\RawClient\ProcessVerifyClient;
use App\Jobs\RawClient\ProcessVerifyClientParent;
use App\Jobs\RawClient\ProcessVerifyClientTeacher;
use App\Models\ClientEventLogMail;
use App\Models\Major;
use App\Models\Tag;
use App\Models\UserClientAdditionalInfo;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\ImportFailed;
use Maatwebsite\Excel\Validators\Failure;
use Throwable;
use App\Notifications\ImportHasFailedNotification;
use Illuminate\Support\Facades\Notification;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Validators\ValidationException;

class ClientEventImport implements ToCollection, WithHeadingRow, WithValidation, WithChunkReading, ShouldQueue, WithEvents

{
    /**
     * @param Collection $collection
     */

    use Importable, SkipsErrors, SkipsFailures;
    use StandardizePhoneNumberTrait;
    use CreateCustomPrimaryKeyTrait;
    use CheckExistingClient;
    use SplitNameTrait;
    use LoggingTrait;
    use SyncClientTrait;

    private ClientRepositoryInterface $clientRepository;
    public $importedBy;

    public function __construct(ClientRepositoryInterface $clientRepository, $importedBy)
    {
        $this->clientRepository = $clientRepository;
        $this->importedBy = $importedBy;
    }


    public function collection(Collection $rows)
    {
        $data = [];
        $logDetails = $childIds = $parentIds = $teacherIds = [];

        DB::beginTransaction();
        try {

            foreach ($rows as $row) {

                # initiate variables
                $status = $row['status'] == 'Join' ? 0 : 1;

                // Check existing school
                if (!$school = School::where('sch_name', $row['school'])->first())
                    $school = $this->createSchoolIfNotExists($row['school']);

                $roleSub = null;
                switch ($row['audience']) {
                    case 'Student':
                        $roleSub = 'Parent';
                        break;
                    case 'Parent':
                        $roleSub = 'Student';
                        break;
                }
 
                $createdMainClient = $this->createClient($row, 'main', $row['audience'], $row['itended_major'], $row['destination_country'], $school);
                $mainClient = UserClient::find($createdMainClient);
                $createdSubClient = ($row['audience'] == 'Student' || $row['audience'] == 'Parent') && isset($row['child_parent_name']) ? $this->createClient($row, 'sub', $roleSub, $row['itended_major'], $row['destination_country'], $school, $mainClient) : null;

                // Create relation parent and student
                if(($row['audience'] == 'Parent' || $row['audience'] == 'Student') && isset($createdSubClient)){
                    $checkExistChildren = null;
                    switch ($row['audience']) {
                        case 'Parent':
                            $parent = UserClient::find($createdMainClient);
                            $student = UserClient::find($createdSubClient);
                            $checkExistChildren = $this->checkExistClientRelation('parent', $parent, $student->fullName);
                            !$checkExistChildren['isExist'] ? $parent->childrens()->attach($createdSubClient) : null;
                            break;

                        case 'Student':
                            $parent = UserClient::find($createdSubClient);
                            $student = UserClient::find($createdMainClient);
                            $checkExistChildren = $this->checkExistClientRelation('parent', $parent, $student->fullName);
                            !$checkExistChildren['isExist'] ? $parent->childrens()->attach($createdMainClient) : null;
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
                if ($row['audience'] == "Parent"){
                    $parentIds[] = $createdMainClient;
                    if(isset($createdSubClient))
                        $data['child_id'] = $createdSubClient;
                        $childIds[] = $createdSubClient;
                    
                }elseif ($row['audience'] == "Student"){
                    $childIds[] = $createdSubClient;
                    if(isset($createdSubClient))
                        $data['parent_id'] = $createdSubClient;
                        $parentIds[] = $createdMainClient;
                }else{
                    $teacherIds[] = $createdMainClient;
                }


                $existClientEvent = ClientEvent::where('event_id', $data['event_id'])
                    ->where('client_id', $createdMainClient)
                    ->where('joined_date', $data['joined_date'])
                    ->first();

                if (!isset($existClientEvent)) {
                    $insertedClientEvent = ClientEvent::create($data);

                    # add to log client event 
                    # to trigger the cron for send the qr email
                    ClientEventLogMail::create([
                        'clientevent_id' => $insertedClientEvent->clientevent_id,
                        'event_id' => $row['event_name'],
                        'sent_status' => 0,
                        'category' => 'qrcode-mail'
                    ]);

                }

                $logDetails[] = [
                    'clientevent_id' => isset($insertedClientEvent->clientevent_id) ? $insertedClientEvent->clientevent_id : null
                ];
            }

            # trigger to verifying client
            count($childIds) > 0 ? ProcessVerifyClient::dispatch($childIds)->onQueue('verifying-client') : null;
            count($parentIds) > 0 ? ProcessVerifyClientParent::dispatch($parentIds)->onQueue('verifying-client-parent') : null;
            count($teacherIds) > 0 ? ProcessVerifyClientTeacher::dispatch($teacherIds)->onQueue('verifying-client-teacher') : null;

            # store Success
            # create log success
            $this->logSuccess('store', 'Import Client Event', 'Client Event', $this->importedBy->first_name . ' ' . $this->importedBy->last_name, $logDetails);
            

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Import client event failed : ' . $e->getMessage().' | Line '.$e->getLine());

            throw new Exception($e->getMessage(), 500);

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
            'email' => trim($data['email']),
            'phone_number' => isset($data['phone_number']) ? $this->setPhoneNumber($data['phone_number']) : null,
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
            '*.audience' => ['required', 'in:Student,Parent,Teacher/Counselor'],
            '*.name' => ['required'],
            '*.email' => ['required', 'email'],
            '*.phone_number' => ['nullable'],
            '*.child_parent_name' => ['nullable', 'different:*.name'],
            '*.child_parent_email' => ['nullable', 'different:*.email'],
            '*.child_parent_phone_number' => ['nullable', 'different:*.phone_number'],
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

 
    private function createClient($row, $type, $role, $majorDetails, $destinationCountryDetails, $school, $mainClient=null)
    {
        $clientId = '';
        $checkExistClientRelation = [
            'isExist' => false,
            'client' => null,
        ];

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

        if($type == 'sub' && $role == 'Student'){
            $checkExistClientRelation = $this->checkExistClientRelation('parent', $mainClient, $fullname);
        }else if($type == 'sub' && $role == 'Parent'){
            $checkExistClientRelation = $this->checkExistClientRelation('student', $mainClient, $fullname);
        }

        $firstname = $this->split($fullname)['first_name'];
        $lastname = $this->split($fullname)['last_name'];

        $roleId = Role::whereRaw('LOWER(role_name) = (?)', [strtolower($role)])->first();
        if (!$roleId)
            throw new Exception("Role not found");
        
        switch ($role) {
            case 'Student':
                if (!$existClient['isExist'] && !$checkExistClientRelation['isExist']) {
                    
                    $studentId = null;
        
                    if ($row['class_of'] != null || $row['class_of'] != '') {
                        $st_grade = 12 - ($row['class_of'] - date('Y'));
                    }
        
                    $dataClient = [
                        'sch_id' => $school->sch_id,
                        'st_id' => isset($studentId) ? $studentId : null,
                        'last_name' => $lastname,
                        'first_name' => $firstname,
                        'mail' => $email,
                        'phone' => $phone,
                        'graduation_year' => $row['class_of'] != null || $row['class_of'] != '' ? $row['class_of'] : null,
                        'st_grade' => $row['class_of'] != null || $row['class_of'] != '' ? $st_grade : null,
                        'register_as' => $row['audience'],
                        'lead_id' => isset($row['lead']) ? $row['lead'] : null,
                        'eduf_id' => isset($row['edufair']) ? $row['edufair'] : null,
                        'event_id' => isset($row['lead']) && $row['lead'] == 'LS003' ? $row['event_name'] : null,
                    ];
        
                    if (!$newClient = UserClient::create($dataClient)) {
                        throw new Exception('Failed to store new client');
                        Log::error('Failed to store new client');
                    } else {
                        $clientId = $newClient->id;
                        $thisNewClient = UserClient::find($newClient->id);
        
                        $thisNewClient->roles()->attach($roleId);
                        isset($majorDetails) ? $this->syncInterestMajor($row['itended_major'], $thisNewClient) : '';
                        isset($destinationCountryDetails) ? $this->syncDestinationCountry($row['destination_country'], $thisNewClient) : null;
                    }

                } else if ($checkExistClientRelation['isExist'] && $checkExistClientRelation['client'] != null){
                    $existClientStudent = $checkExistClientRelation['client'];
                    $clientId = $existClientStudent->id;
                    isset($majorDetails) ? $this->syncInterestMajor($row['itended_major'], $existClientStudent) : '';
                    isset($destinationCountryDetails) ? $this->syncDestinationCountry($row['destination_country'], $existClientStudent) : null;
                
                } else if ($existClient['isExist']) {
                    $clientId = $existClient['id'];
                    $existClientStudent = UserClient::find($existClient['id']);
                    isset($majorDetails) ? $this->syncInterestMajor($row['itended_major'], $existClientStudent) : '';
                    isset($destinationCountryDetails) ? $this->syncDestinationCountry($row['destination_country'], $existClientStudent) : null;
                }
                
                break;

            case 'Parent':

                if (!$existClient['isExist'] && !$checkExistClientRelation['isExist']) {
                
                    $dataClient = [
                        'last_name' => $lastname,
                        'first_name' => $firstname,
                        'mail' => $email,
                        'phone' => $phone,
                        'register_as' => $row['audience'],
                        'lead_id' => isset($row['lead']) ? $row['lead'] : null,
                        'eduf_id' => isset($row['edufair']) ? $row['edufair'] : null,
                        'event_id' => isset($row['lead']) && $row['lead'] == 'LS003' ? $row['event_name'] : null,
                    ];
        
                    if (!$newClient = UserClient::create($dataClient)) {
                        throw new Exception('Failed to store new client');
                        Log::error('Failed to store new client');
                    } else {
                        $clientId = $newClient->id;
                        $thisNewClient = UserClient::find($newClient->id);
        
                        $thisNewClient->roles()->attach($roleId);
                    }
                } else if ($checkExistClientRelation['isExist'] && $checkExistClientRelation['client'] != null){
                    $client = $checkExistClientRelation['client'];
                    $clientId = $client->id;

                } else if ($existClient['isExist']) {
                    $clientId = $existClient['id'];
                }
                break;

            case 'Teacher/Counselor':
                if (!$existClient['isExist']) {
                
                    $dataClient = [
                        'sch_id' => $school->sch_id,
                        'dob' => null,
                        'last_name' => $lastname,
                        'first_name' => $firstname,
                        'mail' => $email,
                        'phone' => $phone,
                        'register_as' => 'teacher/counsellor',
                        'lead_id' => isset($row['lead']) ? $row['lead'] : null,
                        'eduf_id' => isset($row['edufair']) ? $row['edufair'] : null,
                        'event_id' => isset($row['lead']) && $row['lead'] == 'LS003' ? $row['event_name'] : null,
                    ];
        
                    if (!$newClient = UserClient::create($dataClient)) {
                        throw new Exception('Failed to store new client');
                        Log::error('Failed to store new client');
                    } else {
                        $clientId = $newClient->id;
                        $thisNewClient = UserClient::find($newClient->id);
        
                        $thisNewClient->roles()->attach($roleId);
                    }
                }else if ($existClient['isExist']) {
                    $clientId = $existClient['id'];
                }
                break;
            
        }

        return $clientId;
       
    }

    public function registerEvents(): array
    {
        return [
            ImportFailed::class => function(ImportFailed $event) {
                foreach($event->getException() as $exception){
                    $validation[] = $exception !== null && gettype($exception) == "object" ? $exception->errors()->toArray() : null;
                }
                $validation['user_id'] = $this->importedBy->id;
                event(new \App\Events\MessageSent($validation, 'validation-import'));
            },
        ];
    }

    public function chunkSize(): int
    {
        return 50;
    }

    

}