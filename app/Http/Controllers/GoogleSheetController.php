<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\v1\ExtClientController;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\LoggingTrait;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Http\Traits\SyncClientTrait;
use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Jobs\RawClient\ProcessVerifyClient;
use App\Jobs\RawClient\ProcessVerifyClientParent;
use App\Jobs\RawClient\ProcessVerifyClientTeacher;
use App\Models\ClientEvent;
use App\Models\ClientProgram;
use App\Models\Corporate;
use App\Models\EdufLead;
use App\Models\Event;
use App\Models\Lead;
use App\Models\Program;
use App\Models\Role;
use App\Models\School;
use App\Models\UserClient;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Revolution\Google\Sheets\Facades\Sheets;
use App\Models\JobBatches;
use App\Models\ViewProgram;
use App\Services\JobBatchService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;


class GoogleSheetController extends Controller
{
    use SyncClientTrait, CreateCustomPrimaryKeyTrait, LoggingTrait, SyncClientTrait, StandardizePhoneNumberTrait;

    private ClientRepositoryInterface $clientRepository;
    private ClientProgramRepositoryInterface $clientProgramRepository;

    public function __construct(ClientRepositoryInterface $clientRepository, ClientProgramRepositoryInterface $clientProgramRepository)
    {
        $this->clientRepository = $clientRepository;
        $this->clientProgramRepository = $clientProgramRepository;
    }

    public function storeParent(Request $request)
    {
        

            $range = $request->only('start', 'end');
            $start = $request->start;
            $end = $request->end;

            $validRange = $this->validationRange($range);
            if(!$validRange['is_valid']){
                return response()->json([
                    'success' => false,
                    'error' => $validRange['errors']
                ]);
            }

            $rawData = $this->setRawData('V', $start, $end, 'Parents');
        
            $response = [];
           
            if(count($rawData) > 0){

                $arrInputData = $this->setDataForValidation($rawData, 'parent');

                # validation
                $rules = [
                    '*.No' => ['required'],
                    '*.Full Name' => ['required'],
                    '*.Email' => ['required', 'email'],
                    '*.Phone Number' => ['required', 'min:5', 'max:18'],
                    '*.Date of Birth' => ['nullable', 'date'],
                    '*.Instagram' => ['nullable', 'unique:tbl_client,insta'],
                    '*.State' => ['nullable'],
                    '*.City' => ['nullable'],
                    '*.Address' => ['nullable'],
                    '*.Lead' => ['required'],
                    '*.Event' => ['required_if:lead,LS004', 'nullable', 'exists:tbl_events,event_id'],
                    '*.Partner' => ['required_if:lead,LS015', 'nullable', 'exists:tbl_corp,corp_id'],
                    '*.Edufair' => ['required_if:lead,LS018', 'nullable', 'exists:tbl_eduf_lead,id'],
                    '*.KOL' => ['required_if:lead,KOL', 'nullable', 'exists:tbl_lead,lead_id'],
                    '*.Level of Interest' => ['nullable', 'in:High,Medium,Low'],
                    '*.Interested Program' => ['nullable'],
                    '*.Children Name' => ['required'],
                    '*.School' => ['nullable'],
                    '*.Graduation Year' => ['nullable'],
                    '*.Destination Country' => ['nullable'],
                    '*.Joined Date' => ['nullable', 'date'],
                ];


                $validator = Validator::make($arrInputData, $rules);

                # threw error if validation fails
                if ($validator->fails()) {
                    Log::warning($validator->errors());

                    return response()->json([
                        'success' => false,
                        'error' => $validator->errors()
                    ]);
                }

                $batchID = (new JobBatchService())->jobBatchFromCollection(Collect($arrInputData), 'import', 'parent', 10);

                JobBatches::where('id', $batchID)->update(['total_data' => count($arrInputData)]);

                $response = [
                    'success' => true,
                    'batch_id' => $batchID,
                ];
                
            }else{
                $response = [
                    'success' => true, 
                    'total_imported' => 0,
                    'message' => 'Data parents is uptodate'
                ];
            }
        
        return response()->json($response);

    }

    public function storeStudent(Request $request)
    {

            $range = $request->only('start', 'end');
            $start = $request->start;
            $end = $request->end;

            $validRange = $this->validationRange($range);
            if(!$validRange['is_valid']){
                return response()->json([
                    'success' => false,
                    'error' => $validRange['errors']
                ]);
            }

            $rawData = $this->setRawData('Z', $start, $end, 'Students');
        
            $response = [];
           
            if(count($rawData) > 0){

                $arrInputData = $this->setDataForValidation($rawData, 'student');

                # validation
                $rules = [
                    '*.No' => ['required'],
                    '*.Full Name' => ['required'],
                    '*.Email' => ['required', 'email'],
                    '*.Phone Number' => ['nullable', 'min:5', 'max:18'],
                    '*.Date of Birth' => ['nullable', 'date'],
                    '*.Parents Name' => ['nullable', 'different:*.Full Name'],
                    '*.Parents Phone' => ['nullable', 'min:5', 'max:18', 'different:*.Phone_number'],
                    '*.School' => ['required'],
                    '*.Graduation Year' => ['nullable', 'integer'],
                    '*.Grade' => ['nullable', 'integer'],
                    '*.Instagram' => ['nullable'],
                    '*.State' => ['nullable'],
                    '*.City' => ['nullable'],
                    '*.Address' => ['nullable'],
                    '*.Lead' => ['required'],
                    '*.Event' => ['required_if:lead,LS003', 'nullable', 'exists:tbl_events,event_id'],
                    '*.Partner' => ['required_if:lead,LS010', 'nullable', 'exists:tbl_corp,corp_id'],
                    '*.Edufair' => ['required_if:lead,LS017', 'nullable', 'exists:tbl_eduf_lead,id'],
                    '*.KOL' => ['required_if:lead,KOL', 'nullable', 'exists:tbl_lead,lead_id'],
                    '*.Level of Interest' => ['nullable', 'in:High,Medium,Low'],
                    '*.Interested Program' => ['nullable'],
                    '*.Year of Study Abroad' => ['nullable', 'integer'],
                    '*.Country of Study Abroad' => ['nullable'],
                    '*.Interest Major' => ['nullable'],
                    '*.Joined Date' => ['nullable', 'date'],
                ];


                $validator = Validator::make($arrInputData, $rules);

                # threw error if validation fails
                if ($validator->fails()) {
                    Log::warning($validator->errors());

                    return response()->json([
                        'success' => false,
                        'error' => $validator->errors()
                    ]);
                }

                $batchID = (new JobBatchService())->jobBatchFromCollection(Collect($arrInputData), 'import', 'student', 10);

                JobBatches::where('id', $batchID)->update(['total_data' => count($arrInputData)]);

                $response = [
                    'success' => true,
                    'batch_id' => $batchID,
                ];
                
            }else{
                $response = [
                    'success' => true,
                    'total_imported' => 0,
                    'message' => 'Data students is uptodate'
                ];
            }
         
        return response()->json($response);
    }

    public function storeTeacher(Request $request)
    {

            $range = $request->only('start', 'end');
            $start = $request->start;
            $end = $request->end;

            $validRange = $this->validationRange($range);
            if(!$validRange['is_valid']){
                return response()->json([
                    'success' => false,
                    'error' => $validRange['errors']
                ]);
            }

            $rawData = $this->setRawData('R', $start, $end, 'Teachers');
        
            $response = [];
           
            if(count($rawData) > 0){

                $arrInputData = $this->setDataForValidation($rawData, 'teacher');

                # validation
                $rules = [
                    '*.No' => ['required'],
                    '*.Full Name' => ['required'],
                    '*.Email' => ['required', 'email'],
                    '*.Phone Number' => ['required', 'min:5', 'max:18'],
                    '*.Date of Birth' => ['nullable', 'date'],
                    '*.Instagram' => ['nullable', 'unique:tbl_client,insta'],
                    '*.State' => ['nullable'],
                    '*.City' => ['nullable'],
                    '*.Address' => ['nullable'],
                    '*.School' => ['required'],
                    '*.Lead' => ['required'],
                    '*.Event' => ['required_if:lead,LS003', 'nullable', 'exists:tbl_events,event_id'],
                    '*.Partner' => ['required_if:lead,LS010', 'nullable', 'exists:tbl_corp,corp_id'],
                    '*.Edufair' => ['required_if:lead,LS017', 'nullable', 'exists:tbl_eduf_lead,id'],
                    '*.KOL' => ['required_if:lead,KOL', 'nullable', 'exists:tbl_lead,lead_id'],
                    '*.Level of Interest' => ['nullable', 'in:High,Medium,Low'],
                    '*.Joined Date' => ['nullable', 'date'],
                ];


                $validator = Validator::make($arrInputData, $rules);

                # threw error if validation fails
                if ($validator->fails()) {
                    Log::warning($validator->errors());

                    return response()->json([
                        'success' => false,
                        'error' => $validator->errors()
                    ]);
                }

    
                $batchID = (new JobBatchService())->jobBatchFromCollection(Collect($arrInputData), 'import', 'teacher', 10);

                JobBatches::where('id', $batchID)->update(['total_data' => count($arrInputData)]);

                $response = [
                    'success' => true,
                    'batch_id' => $batchID,
                ];
                
            }else{
                $response = [
                    'success' => true,
                    'total_imported' => 0,
                    'message' => 'Data teachers is uptodate'
                ];
            }
         
        return response()->json($response);
    }

    public function storeClientEvent(Request $request)
    {

            $range = $request->only('start', 'end');
            $start = $request->start;
            $end = $request->end;

            $validRange = $this->validationRange($range);
            if(!$validRange['is_valid']){
                return response()->json([
                    'success' => false,
                    'error' => $validRange['errors']
                ]);
            }

            $rawData = $this->setRawData('Z', $start, $end, 'Client Events');
        
            $response = [];
           
            if(count($rawData) > 0){

                $arrInputData = $this->setDataForValidation($rawData, 'client-event');

                # validation
                $rules = [
                    '*.Event Name' => ['required', 'exists:tbl_events,event_id'],
                    '*.Date' => ['required', 'date'],
                    '*.Audience' => ['required', 'in:Student,Parent,Teacher/Counselor'],
                    '*.Name' => ['required'],
                    '*.Email' => ['required', 'email'],
                    '*.Phone Number' => ['nullable'],
                    '*.Child or Parent Name' => ['nullable', 'different:*.name'],
                    '*.Child or Parent Email' => ['nullable', 'different:*.email'],
                    '*.Child or Parent Phone Number' => ['nullable', 'different:*.phone_number'],
                    '*.Registration Type' => ['nullable', 'in:PR,OTS'],
                    // '*.Existing_new_leads' => ['required', 'in:Existing,New'],
                    // '*.mentee_non_mentee' => ['required', 'in:Mentee,Non-mentee'],
                    '*.School' => ['required'],
                    '*.Class Of' => ['nullable', 'integer'],
                    '*.Lead' => ['required'],
                    // '*.Event' => ['required_if:lead,LS003', 'nullable', 'exists:tbl_events,event_id'],
                    '*.Partner' => ['required_if:lead,LS010', 'nullable', 'exists:tbl_corp,corp_id'],
                    '*.Edufair' => ['required_if:lead,LS017', 'nullable', 'exists:tbl_eduf_lead,id'],
                    '*.KOL' => ['required_if:lead,KOL', 'nullable', 'exists:tbl_lead,lead_id'],
                    '*.Itended Major' => ['nullable'],
                    '*.Destination Country' => ['nullable'],
                    '*.Number Of Attend' => ['nullable'],
                    '*.Referral Code' => ['nullable'],
                    '*.Reason Join' => ['nullable'],
                    '*.Expectation Join' => ['nullable'],
                    '*.Status' => ['required', 'in:Join,Attend'],
                ];


                $validator = Validator::make($arrInputData, $rules);

                # threw error if validation fails
                if ($validator->fails()) {
                    Log::warning($validator->errors());

                    return response()->json([
                        'success' => false,
                        'error' => $validator->errors()
                    ]);
                }

                $batchID = (new JobBatchService())->jobBatchFromCollection(Collect($arrInputData), 'import', 'client-event', 10);

                JobBatches::where('id', $batchID)->update(['total_data' => count($arrInputData)]);

                $response = [
                    'success' => true,
                    'batch_id' => $batchID,
                ];
                
            }else{
                $response = [
                    'success' => true,
                    'total_imported' => 0,
                    'message' => 'Data client events is uptodate'
                ];
            }
         
        return response()->json($response);

    }

    public function storeClientProgram(Request $request)
    {

            $range = $request->only('start', 'end');
            $start = $request->start;
            $end = $request->end;

            $validRange = $this->validationRange($range);
            if(!$validRange['is_valid']){
                return response()->json([
                    'success' => false,
                    'error' => $validRange['errors']
                ]);
            }

            $rawData = $this->setRawData('W', $start, $end, 'Client Programs');
        
            $response = [];
           
            if(count($rawData) > 0){

                $arrInputData = $this->setDataForValidation($rawData, 'client-program');


                # validation
                $rules = [
                    '*.No' => ['required'],
                    '*.Program Name' => ['required', 'exists:tbl_prog,prog_id'],
                    '*.Date' => ['required', 'date'],
                    '*.Audience' => ['required', 'in:Student,Parent'],
                    '*.Name' => ['required'],
                    '*.Email' => ['required', 'email'],
                    '*.Phone Number' => ['nullable'],
                    '*.Child or Parent Name' => ['nullable', 'different:*.name'],
                    '*.Child or Parent Email' => ['nullable', 'different:*.email'],
                    '*.Child or Parent Phone Number' => ['nullable', 'different:*.phone_number'],
                    '*.School' => ['required'],
                    '*.Class Of' => ['nullable', 'integer'],
                    '*.Lead' => ['required'],
                    '*.Event' => ['required_if:lead,LS003', 'nullable', 'exists:tbl_events,event_id'],
                    '*.Partner' => ['required_if:lead,LS010', 'nullable', 'exists:tbl_corp,corp_id'],
                    '*.Edufair' => ['required_if:lead,LS017', 'nullable', 'exists:tbl_eduf_lead,id'],
                    '*.KOL' => ['required_if:lead,KOL', 'nullable', 'exists:tbl_lead,lead_id'],
                    '*.Itended Major' => ['nullable'],
                    '*.Destination Country' => ['nullable'],
                    '*.Referral Code' => ['nullable'],
                    '*.Reason Join' => ['nullable'],
                    '*.Expectation Join' => ['nullable'],
                ];


                $validator = Validator::make($arrInputData, $rules);

                # threw error if validation fails
                if ($validator->fails()) {
                    Log::warning($validator->errors());

                    return response()->json([
                        'success' => false,
                        'error' => $validator->errors()
                    ]);
                }

                $batchID = (new JobBatchService())->jobBatchFromCollection(Collect($arrInputData), 'import', 'client-program', 10);

                JobBatches::where('id', $batchID)->update(['total_data' => count($arrInputData)]);

                $response = [
                    'success' => true,
                    'batch_id' => $batchID,
                ];
                
            }else{
                $response = [
                    'success' => true,
                    'total_imported' => 0,
                    'message' => 'Data Client Programs is uptodate'
                ];
            }
         
        return response()->json($response);

    }

    public function createClient($row, $type, $role, $majorDetails, $destinationCountryDetails, $school, $mainClient=null)
    {
        $clientId = '';
        $checkExistClientRelation = [
            'isExist' => false,
            'client' => null,
        ];

        switch ($type) {
            case 'main':
                $phone = $this->setPhoneNumber($row['Phone Number']);
                $existClient = $this->checkExistingClientImport($phone, $row['Email']);
                $email = $row['Email'];
                $fullname = $row['Name'];
                break;
                
            case 'sub':
                $phone = isset($row['Child or Parent Phone Number']) ? $this->setPhoneNumber($row['Child or Parent Phone Number']) : null;
                $email = isset($row['Child or Parent Email']) ? $row['Child or Parent Email'] : null;
                $existClient = $this->checkExistingClientImport($phone, $email);
                $fullname = $row['Child or Parent Name'];

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
        
                    if ($row['Class Of'] != null || $row['Class Of'] != '') {
                        $st_grade = 12 - ($row['Class Of'] - date('Y'));
                    }
        
                    $dataClient = [
                        'sch_id' => $school->sch_id,
                        'st_id' => isset($studentId) ? $studentId : null,
                        'last_name' => $lastname,
                        'first_name' => $firstname,
                        'mail' => $email,
                        'phone' => $phone,
                        'graduation_year' => $row['Class Of'] != null || $row['Class Of'] != '' ? $row['Class Of'] : null,
                        'st_grade' => $row['Class Of'] != null || $row['Class Of'] != '' ? $st_grade : null,
                        'register_as' => $row['Audience'],
                        'lead_id' => isset($row['Lead']) ? $row['Lead'] : null,
                        'eduf_id' => isset($row['Edufair']) ? $row['Edufair'] : null,
                        'event_id' => isset($row['Lead']) && $row['Lead'] == 'LS003' ? $row['Event Name'] : null,
                    ];
        
                    if (!$newClient = UserClient::create($dataClient)) {
                        throw new Exception('Failed to store new client');
                        Log::error('Failed to store new client');
                    } else {
                        $clientId = $newClient->id;
                        $thisNewClient = UserClient::find($newClient->id);
        
                        $thisNewClient->roles()->attach($roleId);
                        isset($majorDetails) ? $this->syncInterestMajor($row['Itended Major'], $thisNewClient) : '';
                        isset($destinationCountryDetails) ? $this->syncDestinationCountry($row['Destination Country'], $thisNewClient) : null;
                    }

                } else if ($checkExistClientRelation['isExist'] && $checkExistClientRelation['client'] != null){
                    $existClientStudent = $checkExistClientRelation['client'];
                    $clientId = $existClientStudent->id;
                    isset($majorDetails) ? $this->syncInterestMajor($row['Itended Major'], $existClientStudent) : '';
                    isset($destinationCountryDetails) ? $this->syncDestinationCountry($row['Destination Country'], $existClientStudent) : null;
                
                } else if ($existClient['isExist']) {
                    $clientId = $existClient['id'];
                    $existClientStudent = UserClient::find($existClient['id']);
                    isset($majorDetails) ? $this->syncInterestMajor($row['Itended Major'], $existClientStudent) : '';
                    isset($destinationCountryDetails) ? $this->syncDestinationCountry($row['Destination Country'], $existClientStudent) : null;
                }
                
                break;

            case 'Parent':

                if (!$existClient['isExist'] && !$checkExistClientRelation['isExist']) {
                    $dataClient = [
                        'last_name' => $lastname,
                        'first_name' => $firstname,
                        'mail' => $email,
                        'phone' => $phone,
                        'register_as' => $row['Audience'],
                        'lead_id' => isset($row['Lead']) ? $row['Lead'] : null,
                        'eduf_id' => isset($row['Edufair']) ? $row['Edufair'] : null,
                        'event_id' => isset($row['Lead']) && $row['Lead'] == 'LS003' ? $row['Event Name'] : null,
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
                        'lead_id' => isset($row['Lead']) ? $row['Lead'] : null,
                        'eduf_id' => isset($row['Edufair']) ? $row['Edufair'] : null,
                        'event_id' => isset($row['Lead']) && $row['Lead'] == 'LS003' ? $row['Event Name'] : null,
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

    private function validationRange($request)
    {
        $rules = [
            'start' => ['required', 'numeric', 'min:2'],
            'end' => ['required', 'numeric', 'min:2'],
        ];

        $validator = Validator::make($request, $rules);

        # threw error if validation fails
        if ($validator->fails()) {
            Log::warning($validator->errors());
            return [
                'is_valid' => false,
                'errors' => $validator->errors()
            ];
        }else{
            return [
                'is_valid' => true,
                'errors' => null
            ];

        }
    }

    private function setRawData($colEnd, $rangeStart, $rangeEnd, $sheetName)
    { 
        $values = [];
        $header = Sheets::getService()->spreadsheets_values->get(env('GOOGLE_SHEET_KEY_IMPORT'), $sheetName . '!A1:' .$colEnd .'1')->getValues();
        $sheet = Sheets::getService()->spreadsheets_values->get(env('GOOGLE_SHEET_KEY_IMPORT'), $sheetName . '!A'.$rangeStart.':'. $colEnd . $rangeEnd, ['valueRenderOption' => 'FORMATTED_VALUE'])->getValues();

        $values = Sheets::collection($header[0], $sheet);
        
        return $values->where('Imported Date', null);
    }
    
    private function setDataForValidation($rawData, $category)
    {
        $arrInputData = [];
        $chunks = $rawData->chunk(50);
        foreach ($chunks as $chunk) {
            foreach ($chunk as $data) {
                switch ($category) {
                    case 'client-event':
                        $event_name = Event::where('event_title', $data['Event Name'])->get()->pluck('event_id')->first();
                        isset($event_name) ? $data['Event Name'] = $event_name : null;
                        
                        // $data['Date'] = str_replace('/', '-', $data['Date']);
                        $data['Date'] = Carbon::parse($data['Date'])->format('Y-m-d');
    
                        break;
    
                    case 'client-program':
                        $program_name = ViewProgram::where('program_name', $data['Program Name'])->pluck('prog_id')->first();
                        isset($program_name) ? $data['Program Name'] = $program_name : null;
    
                        $event = Event::where('event_title', $data['Event'])->get()->pluck('event_id')->first();
                        isset($event) ? $data['Event'] = $event : null;
    
                        // $data['Date'] = str_replace('/', '-', $data['Date']);
                        $data['Date'] = Carbon::parse($data['Date'])->format('Y-m-d');
    
                        break;
                    
                    default:
                        $event = Event::where('event_title', $data['Event'])->get()->pluck('event_id')->first();
                        isset($event) ? $data['Event'] = $event : null;
    
                        // $data['Joined Date'] = str_replace('/', '-', $data['Joined Date']);
                        $data['Joined Date'] = Carbon::parse($data['Joined Date'])->format('Y-m-d');        
                        break;
                }
    
                if ($data['Lead'] == 'School' || $data['Lead'] == 'Counselor') {
                    $data['Lead'] = 'School/Counselor';
                }else if($data['Lead'] == 'KOL'){
                    $data['Lead'] = 'KOL';
                }else{
                    $lead = Lead::where('main_lead', $data['Lead'])->get()->pluck('lead_id')->first();
                    isset($lead) ? $data['Lead'] = $lead : null;
                }
    
                $getAllEduf = EdufLead::all();
                $edufair = $getAllEduf->where('organizerName', $data['Edufair'])->pluck('id')->first();
                $partner = Corporate::where('corp_name', $data['Partner'])->get()->pluck('corp_id')->first();
                $kol = Lead::where('main_lead', 'KOL')->where('sub_lead', $data['KOL'])->get()->pluck('lead_id')->first();
                
                isset($edufair) ? $data['Edufair'] = $edufair : null;
                isset($partner) ? $data['Partner'] = $partner : null;
                isset($kol) ? $data['KOL'] = $kol : null;
    
    
                $arrInputData[$data['No']] = array_map(fn($v) => $v == '' ? null : $v, $data->toArray()); # Replace value "" to null
            }
        }
        return $arrInputData;
    }

    public function sync(Request $request)
    {
        $type = $request->route('type');

        try {
            Artisan::call('sync:data', ['type' => $type]);
        }catch (Exception $e) {
            Log::error($e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Something went wrong. Please try again'
            ], 500);
        }

        return response()->json([
            'success' => true,
        ]);
    }

    public function exportData(Request $request)
    {
        $type = $request->route('type');
        $from = $request->route('from'); # From mean type data {collection or model}
        $data = [];
        DB::beginTransaction();
        try {
            switch ($type) {
                case 'new-leads':
                    Sheets::spreadsheet(env('GOOGLE_SHEET_KEY_EXPORT_DATA'))->sheet('New Leads')->range('A2:AE1000')->clear();
                    $data = $this->clientRepository->getNewLeads(true, null, []);
                    break;
                case 'potential':
                    Sheets::spreadsheet(env('GOOGLE_SHEET_KEY_EXPORT_DATA'))->sheet('Potentials')->range('A2:AE1000')->clear();
                    $data = $this->clientRepository->getPotentialClients(true, null, []);
                    break;
                case 'mentee':
                    Sheets::spreadsheet(env('GOOGLE_SHEET_KEY_EXPORT_DATA'))->sheet('Mentees')->range('A2:AE1000')->clear();
                    $data = $this->clientRepository->getExistingMentees(true, null, []);
                    break;
                case 'non-mentee':
                    Sheets::spreadsheet(env('GOOGLE_SHEET_KEY_EXPORT_DATA'))->sheet('Non-Mentees')->range('A2:AE1000')->clear();                    
                    $data = $this->clientRepository->getExistingNonMentees(true, null, []);
                    break;
                case 'all':
                    Sheets::spreadsheet(env('GOOGLE_SHEET_KEY_EXPORT_DATA'))->sheet('All')->range('A2:AE3000')->clear();                    
                    $data = $this->clientRepository->getAllClientStudent([], true);
                    break;
                case 'inactive':
                    Sheets::spreadsheet(env('GOOGLE_SHEET_KEY_EXPORT_DATA'))->sheet('Inactive')->range('A2:AE1000')->clear();
                    $data = $this->clientRepository->getInactiveStudent(true ,null, []);
                    break;
                case 'client-program':
                    $data = $this->clientProgramRepository->getAllClientProgramDataTables([] , false);
                    break;
                
                default:
                    return response()->json([
                        'success' => false,
                        'error' => 'Invalid client category!'
                    ], 500);
                    break;
            }

            switch ($from) {
                case 'collection':
                    $count = $data->count();
                    $batchID = (new JobBatchService())->jobBatchFromCollection($data, 'export', $type, 100);
                    break;

                case 'model':
                    $count = $data->get()->count();
                    $batchID = (new JobBatchService())->jobBatchFromModel($data, 'export', $type, 100);
                    break;
            }

            JobBatches::where('id', $batchID)->update(['total_data' => $count]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Failed to export data: ' . $e->getMessage() . '| on line: ' . $e->getLine());
            return response()->json([
                'success' => false,
                'error' => 'Something went wrong. Please try again'
            ], 500);
        }

        $response = [
            'success' => true,
            'batch_id' => $batchID,
        ];
        return response()->json($response);
    }

    public function findBatch(Request $request)
    {
        $batchId = $request->route('batchId');
        $data = new Collection();

        try {
            $batch = Bus::findBatch($batchId);
            $jobBatches = JobBatches::find($batchId);
            $data = Collect($batch);
            
            $data->put('total_data', $jobBatches->total_data);
            $data->put('total_imported', $jobBatches->total_imported);
    
            if($jobBatches->finished_at != null){
                if($jobBatches->category != null & $jobBatches->category == 'Import'){
                    $this->logSuccess('store', 'Import '. $jobBatches->type, $jobBatches->type, auth()->guard('api')->user()->first_name . ' ' . auth()->guard('api')->user()->last_name, Collect(json_decode($jobBatches->log_details, true)));
                }else{
                    Log::notice('Successfully exported data '. $jobBatches->type);
                }
            } 
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Something went wrong. Please try again'
            ], 500);
        }
        return $data;
    }

}
