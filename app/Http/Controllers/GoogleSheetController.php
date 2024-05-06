<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\v1\ExtClientController;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\LoggingTrait;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Http\Traits\SyncClientTrait;
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Revolution\Google\Sheets\Facades\Sheets;

class GoogleSheetController extends Controller
{
    use SyncClientTrait, CreateCustomPrimaryKeyTrait, LoggingTrait, SyncClientTrait, StandardizePhoneNumberTrait;

    public function storeParent(Request $request)
    {
        $logDetails = [];
        
        DB::beginTransaction();
        try {

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
                    '*.Phone Number' => ['required', 'min:5', 'max:15'],
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
                    '*.Children Name' => ['nullable'],
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

                $totalImported = 0;

                foreach ($arrInputData as $key => $val) {
                    $parent = null;
                    $phoneNumber = $this->setPhoneNumber($val['Phone Number']);
    
                    $parent = $this->checkExistingClientImport($phoneNumber, $val['Email']);
    
                    $joinedDate = isset($val['Joined Date']) ? $val['Joined Date'] : null;
    
                    $parentName = $this->explodeName($val['Full Name']);

                    if (!$parent['isExist']) {
                        $parentDetails = [
                            'first_name' => $parentName['firstname'],
                            'last_name' => isset($parentName['lastname']) ? $parentName['lastname'] : null,
                            'mail' => $val['Email'],
                            'phone' => $phoneNumber,
                            'dob' => isset($val['Date of Birth']) ? $val['Date of Birth'] : null,
                            'insta' => isset($val['Instagram']) ? $val['Instagram'] : null,
                            'state' => isset($val['State']) ? $val['State'] : null,
                            'city' => isset($val['City']) ? $val['City'] : null,
                            'address' => isset($val['Address']) ? $val['Address'] : null,
                            'lead_id' => $val['Lead'],
                            'event_id' => isset($val['Event']) && $val['Lead'] == 'LS003' ? $val['Event'] : null,
                            'eduf_id' => isset($val['Edufair'])  && $val['Lead'] == 'LS017' ? $val['Edufair'] : null,
                            'st_levelinterest' => $val['Level of Interest'],
                        ];

    
                        isset($val['Joined Date']) ? $parentDetails['created_at'] = $val['Joined Date'] : null;
                        
                        $roleId = Role::whereRaw('LOWER(role_name) = (?)', ['parent'])->first();
    
                        $parent = UserClient::create($parentDetails);
                        $parent->roles()->attach($roleId);
                    } else {
                        $parent = UserClient::find($parent['id']);
                    }


                    $children = null;
                    $checkExistChildren = null;
                    if (isset($val['Children Name'])) {
                        $checkExistChildren = $this->checkExistClientRelation('parent', $parent, $val['Children Name']);
                        
                        if($checkExistChildren['isExist'] && $checkExistChildren['client'] != null){
                            $children = $checkExistChildren['client'];
                        }else if(!$checkExistChildren['isExist']){
                            $name = $this->explodeName($val['Children Name']);
                            $school = School::where('sch_name', $val['School'])->first();

                            if (!isset($school)) {
                                $school = $this->createSchoolIfNotExists($val['School']);
                            }

                            $childrenDetails = [
                                'first_name' => $name['firstname'],
                                'last_name' => isset($name['lastname']) ? $name['lastname'] : null,
                                'sch_id' => $school->sch_id,
                                'graduation_year' => isset($val['Graduation Year']) ? $val['Graduation Year'] : null,
                                'lead_id' => $val['Lead'],
                                'event_id' => isset($val['Event']) && $val['Lead'] == 'LS003' ? $val['Event'] : null,
                                'eduf_id' => isset($val['Edufair'])  && $val['Lead'] == 'LS017' ? $val['Edufair'] : null,
                            ];

                            isset($val['Joined Date']) ? $childrenDetails['created_at'] = $val['Joined Date'] : null;

                            $roleId = Role::whereRaw('LOWER(role_name) = (?)', ['student'])->first();

                            $children = UserClient::create($childrenDetails);
                            $children->roles()->attach($roleId);
                            $parent->childrens()->attach($children);
                        }

                        $childrenIds[] = $children['id'];
                    }

                    if (isset($val['Interested Program'])) {
                        $this->syncInterestProgram($val['Interested Program'], $parent, $joinedDate);
                        $children != null ?  $this->syncInterestProgram($val['Interested Program'], $children, $joinedDate) : null;
                    }

                    // Sync country of study abroad
                    if (isset($val['Destination Country'])) {
                        $this->syncDestinationCountry($val['Destination Country'], $parent);
                        $children != null ?  $this->syncDestinationCountry($val['Destination Country'], $children) : null;
                    }
                
                    $parentIds[] = $parent['id'];

                    $logDetails[] = [
                        'client_id' => $parent['id']
                    ];
    
                    $imported = Sheets::spreadsheet(env('GOOGLE_SHEET_KEY_IMPORT'))->sheet('Parents')->range('V'.$val['No'] + 1)->update([[Carbon::now()->format('d-m-Y H:i:s')]]);
                    $totalImported += $imported->totalUpdatedRows;
                }

                # trigger to verifying parent
                count($parentIds) > 0 ? ProcessVerifyClientParent::dispatch($parentIds)->onQueue('verifying-client-parent') : null;
                
                # trigger to verifying children
                count($childrenIds) > 0 ? ProcessVerifyClient::dispatch($childrenIds)->onQueue('verifying-client') : null;


                $response = [
                    'total_imported' => $totalImported,
                    'message' => null,
                ];

                
            }else{
                $response = [
                    'total_imported' => 0,
                    'message' => 'Data parents is uptodate'
                ];
            }
         
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Something went wrong. Please try again'
            ], 500);
        }

        $this->logSuccess('store', 'Import Parent', 'Parent', auth()->guard('api')->user()->first_name . ' ' . auth()->guard('api')->user()->last_name, $logDetails);
      
        return response()->json([
            'success' => true,
            'data' => $response
        ]);
    }

    public function storeStudent(Request $request)
    {
        $logDetails = [];

        DB::beginTransaction();
        try {

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
                    '*.Phone Number' => ['nullable', 'min:5', 'max:15'],
                    '*.Date of Birth' => ['nullable', 'date'],
                    '*.Parents Name' => ['required', 'different:*.Full Name'],
                    '*.Parents Phone' => ['nullable', 'min:5', 'max:15', 'different:*.Phone_number'],
                    '*.School' => ['required'],
                    '*.Graduation Year' => ['nullable', 'integer'],
                    '*.Grade' => ['required', 'integer'],
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

                $totalImported = 0;

                foreach ($arrInputData as $key => $val) {
                    $student = null;
                    $phoneNumber = isset($val['Phone Number']) ? $this->setPhoneNumber($val['Phone Number']) : null;
                    isset($val['Parents Phone']) ? $parentPhone = $this->setPhoneNumber($val['Parents Phone']) : $parentPhone = null;

                    $studentName = $val['Full Name'] != null ? $this->explodeName($val['Full Name']) : null;
                    $parentName = $val['Parents Name'] != null ? $this->explodeName($val['Parents Name']) : null;

                    $joinedDate = isset($val['Joined Date']) ? $val['Joined Date'] : null;


                    // $last_id = UserClient::max('st_id');
                    // $student_id_without_label = $this->remove_primarykey_label($last_id, 3);
                    // $studentId = 'ST-' . $this->add_digit((int) $student_id_without_label + 1, 4);

                    // Check existing school
                    $school = School::where('sch_name', $val['School'])->get()->pluck('sch_id')->first();

                    if (!isset($school)) {
                        $newSchool = $this->createSchoolIfNotExists($val['School']);
                    }

                    $mail = isset($val['Email']) ? $val['Email'] : null;
                    $student = $this->checkExistingClientImport($phoneNumber, $mail);

                    if (!$student['isExist']) {
                        $studentDetails = [
                            // 'st_id' => $studentId,
                            'first_name' => $studentName != null ? $studentName['firstname'] : ($parentName != null ? $parentName['firstname'] . ' ' . $parentName['lastname'] : null),
                            'last_name' =>  $studentName != null && isset($studentName['lastname']) ? $studentName['lastname'] : ($parentName != null ? 'Child' : null),
                            'mail' => $mail,
                            'phone' => $phoneNumber,
                            'dob' => isset($val['Date of Birth']) ? $val['Date of Birth'] : null,
                            'insta' => isset($val['Instagram']) ? $val['Instagram'] : null,
                            'state' => isset($val['State']) ? $val['State'] : null,
                            'city' => isset($val['City']) ? $val['City'] : null,
                            'address' => isset($val['Address']) ? $val['Address'] : null,
                            'sch_id' => isset($school) ? $school : $newSchool->sch_id,
                            'st_grade' => $val['Grade'],
                            'lead_id' => $val['Lead'] == 'KOL' ? $val['kol'] : $val['Lead'],
                            'event_id' => isset($val['Event']) && $val['Lead'] == 'LS003' ? $val['Event'] : null,
                            // 'partner_id' => isset($val['partner']) && $val['Lead'] == 'LS015' ? $val['partner'] : null,
                            'eduf_id' => isset($val['Edufair'])  && $val['Lead'] == 'LS017' ? $val['Edufair'] : null,
                            'st_levelinterest' => $val['Level of Interest'],
                            'graduation_year' => isset($val['Graduation Year']) ? $val['Graduation Year'] : null,
                            'st_abryear' => isset($val['Year of Study Abroad']) ? $val['Year of Study Abroad'] : null,
                        ];

                        isset($val['Joined Date']) ? $studentDetails['created_at'] = $val['Joined Date'] : null;
                        isset($val['Joined Date']) ? $studentDetails['updated_at'] = $val['Joined Date'] : null;
                        
                        $roleId = Role::whereRaw('LOWER(role_name) = (?)', ['student'])->first();

                        $student = UserClient::create($studentDetails);
                        $student->roles()->attach($roleId);

                    } else {
                        $student = UserClient::find($student['id']);

                    }

                    // Connecting student with parent
                    $checkExistParent = null;
                    $parent = null;
                    if (isset($val['Parents Name'])) {
                        // $this->createParentsIfNotExists($val['Parents Name'], $parentPhone, $student);
                        $checkExistParent = $this->checkExistClientRelation('student', $student, $val['Parents Name']);
                        if($checkExistParent['isExist'] && $checkExistParent['client'] != null){
                            $parent = $checkExistParent['client'];
                        }else if(!$checkExistParent['isExist']){
                            $name = $this->explodeName($val['Parents Name']);

                            if(isset($parentPhone)){
                                $checkParent = $this->checkExistingClientImport($parentPhone, null);
                                
                                if(!$checkParent['isExist']){

                                    $parentDetails = [
                                        'first_name' => $name['firstname'],
                                        'last_name' => isset($name['lastname']) ? $name['lastname'] : null,
                                        'phone' => isset($parentPhone) ? $parentPhone : null,
                                    ];
        
                                    $roleId = Role::whereRaw('LOWER(role_name) = (?)', ['parent'])->first();
        
                                    $parent = UserClient::create($parentDetails);
                                    $parent->roles()->attach($roleId);
                                }else{
                                    $parent = UserClient::find($checkParent['id']);
                                }
                            }else{
                                
                                $parentDetails = [
                                    'first_name' => $name['firstname'],
                                    'last_name' => isset($name['lastname']) ? $name['lastname'] : null,
                                    'phone' => isset($parentPhone) ? $parentPhone : null,
                                ];
    
                                $roleId = Role::whereRaw('LOWER(role_name) = (?)', ['parent'])->first();
    
                                $parent = UserClient::create($parentDetails);
                                $parent->roles()->attach($roleId);
                            }

                            $student->parents()->attach($parent);

                        }
                        $parentIds[] = $parent['id'];
                    }

                    // Sync interest program
                    if (isset($val['Interested Program'])) {
                        $this->syncInterestProgram($val['Interested Program'], $student, $joinedDate);
                    }

                    // Sync country of study abroad
                    if (isset($val['Country of Study Abroad'])) {
                        $this->syncDestinationCountry($val['Country of Study Abroad'], $student);
                    }

                    // Sync interest major
                    if (isset($val['Interest Major'])) {
                        $this->syncInterestMajor($val['Interest Major'], $student);
                    }

                    $logDetails[] = [
                        'client_id' => $student['id']
                    ];

                    $childIds[] = $student['id'];
        
                    $imported = Sheets::spreadsheet(env('GOOGLE_SHEET_KEY_IMPORT'))->sheet('Students')->range('Z'.$val['No'] + 1)->update([[Carbon::now()->format('d-m-Y H:i:s')]]);
                    $totalImported += $imported->totalUpdatedRows;
                }

                # trigger to verifying children
                count($childIds) > 0 ? ProcessVerifyClient::dispatch($childIds)->onQueue('verifying-client') : null;

                # trigger to verifying parent
                count($parentIds) > 0 ? ProcessVerifyClientParent::dispatch($parentIds)->onQueue('verifying-client-parent') : null;


                $response = [
                    'total_imported' => $totalImported,
                    'message' => null,
                ];
                
            }else{
                $response = [
                    'total_imported' => 0,
                    'message' => 'Data students is uptodate'
                ];
            }
         
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Something went wrong. Please try again'
            ], 500);
        }

        $this->logSuccess('store', 'Import Student', 'Student', auth()->guard('api')->user()->first_name . ' ' . auth()->guard('api')->user()->last_name, $logDetails);
      
        return response()->json([
            'success' => true,
            'data' => $response
        ]);
    }

    public function storeTeacher(Request $request)
    {
        $logDetails = [];

        DB::beginTransaction();
        try {

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

                $arrInputData = $this->setDataForValidation($rawData, 'parent');

                # validation
                $rules = [
                    '*.No' => ['required'],
                    '*.Full Name' => ['required'],
                    '*.Email' => ['required', 'email'],
                    '*.Phone Number' => ['required', 'min:5', 'max:15'],
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

                $totalImported = 0;

                foreach ($arrInputData as $key => $val) {
                    $teacher = null;
                    $phoneNumber = $this->setPhoneNumber($val['Phone Number']);
    
                    $teacherName = $this->explodeName($val['Full Name']);
    
                    // Check existing school
                    $school = School::where('sch_name', $val['School'])->get()->pluck('sch_id')->first();
    
                    if (!isset($school)) {
                        $newSchool = $this->createSchoolIfNotExists($val['School']);
                    }
    
                    $teacher = $this->checkExistingClientImport($phoneNumber, $val['Email']);
    
                    if (!$teacher['isExist']) {
                        $teacherDetails = [
                            'first_name' => $teacherName['firstname'],
                            'last_name' => isset($teacherName['lastname']) ? $teacherName['lastname'] : null,
                            'mail' => $val['Email'],
                            'phone' => $phoneNumber,
                            'dob' => isset($val['Date of Birth']) ? $val['Date of Birth'] : null,
                            'insta' => isset($val['Instagram']) ? $val['Instagram'] : null,
                            'state' => isset($val['State']) ? $val['State'] : null,
                            'city' => isset($val['City']) ? $val['City'] : null,
                            'address' => isset($val['Address']) ? $val['Address'] : null,
                            'sch_id' => isset($school) ? $school : $newSchool->sch_id,
                            'lead_id' => $val['Lead'],
                            'event_id' => isset($val['Event']) && $val['Lead'] == 'LS003' ? $val['Event'] : null,
                            'eduf_id' => isset($val['Edufair'])  && $val['Lead'] == 'LS017' ? $val['Edufair'] : null,
                            'st_levelinterest' => $val['Level of Interest'],
                        ];
                        isset($val['Joined Date']) ? $teacherDetails['created_at'] = $val['Joined Date'] : null;
                        isset($val['Joined Date']) ? $teacherDetails['updated_at'] = $val['Joined Date'] : null;

                        $roleId = Role::whereRaw('LOWER(role_name) = (?)', ['teacher/counselor'])->first();
    
                        $teacher = UserClient::create($teacherDetails);
                        $teacher->roles()->attach($roleId);
    
                    }
    
                    $logDetails[] = [
                        'client_id' => $teacher['id']
                    ];
    
                    $teacherIds[] = $teacher['id'];
        
                    $imported = Sheets::spreadsheet(env('GOOGLE_SHEET_KEY_IMPORT'))->sheet('Teachers')->range('R'.$val['No'] + 1)->update([[Carbon::now()->format('d-m-Y H:i:s')]]);
                    $totalImported += $imported->totalUpdatedRows;
                }

                # trigger to verifying parent
                count($teacherIds) > 0 ? ProcessVerifyClientTeacher::dispatch($teacherIds)->onQueue('verifying-client-teacher') : null;

                $response = [
                    'total_imported' => $totalImported,
                    'message' => null,
                ];
                
            }else{
                $response = [
                    'total_imported' => 0,
                    'message' => 'Data teachers is uptodate'
                ];
            }
         
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Something went wrong. Please try again'
            ], 500);
        }

        $this->logSuccess('store', 'Import Teacher', 'Parent', auth()->guard('api')->user()->first_name . ' ' . auth()->guard('api')->user()->last_name, $teacher);
      
        return response()->json([
            'success' => true,
            'data' => $response
        ]);
    }

    public function storeClientEvent(Request $request)
    {
        $logDetails = [];

        DB::beginTransaction();
        try {

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

                $totalImported = 0;

                $logDetails = $childIds = $parentIds = $teacherIds = $data = [];

                foreach ($arrInputData as $key => $val) {
                    # initiate variables
                    $status = $val['Status'] == 'Join' ? 0 : 1;

                    // Check existing school
                    if (!$school = School::where('sch_name', $val['School'])->first())
                        $school = $this->createSchoolIfNotExists($val['School']);

                    $roleSub = null;
                    switch ($val['Audience']) {
                        case 'Student':
                            $roleSub = 'Parent';
                            break;
                        case 'Parent':
                            $roleSub = 'Student';
                            break;
                    }
    
                    $createdMainClient = $this->createClient($val, 'main', $val['Audience'], $val['Itended Major'], $val['Destination Country'], $school);

                    $mainClient = UserClient::find($createdMainClient);
                    $createdSubClient = ($val['Audience'] == 'Student' || $val['Audience'] == 'Parent') && isset($val['Child or Parent Name']) ? $this->createClient($val, 'sub', $roleSub, $val['Itended Major'], $val['Destination Country'], $school, $mainClient) : null;

                    // Create relation parent and student
                    if(($val['Audience'] == 'Parent' || $val['Audience'] == 'Student') && isset($createdSubClient)){
                        $checkExistChildren = null;
                        switch ($val['Audience']) {
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
                        'event_id' => $val['Event Name'],
                        'joined_date' => isset($val['Date']) ? $val['Date'] : null,
                        'client_id' => $createdMainClient,
                        'lead_id' => $val['Lead'],
                        'status' => $status,
                        'registration_type' => isset($val['Registration Type']) ? $val['Registration Type'] : null,
                        'number_of_attend' => isset($val['Number Of Attend']) ? $val['Number Of Attend'] : 1,
                        'referral_code' => isset($val['Referral Code']) ? $val['Referral Code'] : null,
                    ];

                    // Generate ticket id (if event offline)
                    $event = Event::where('event_id', $val['Event Name'])->first();
                    if(!str_contains($event->event_location, 'online')){
                        $data['ticket_id'] = app(ExtClientController::class)->generateTicketID();
                    }
                    
                    # add additional identification
                    if ($val['Audience'] == "Parent"){
                        $parentIds[] = $createdMainClient;
                        if(isset($createdSubClient))
                            $data['child_id'] = $createdSubClient;
                            $childIds[] = $createdSubClient;
                        
                    }elseif ($val['Audience'] == "Student"){
                        $childIds[] = $createdMainClient;
                        if(isset($createdSubClient))
                            $data['parent_id'] = $createdSubClient;
                            $parentIds[] = $createdSubClient;
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
                        // ClientEventLogMail::create([
                        //     'clientevent_id' => $insertedClientEvent->clientevent_id,
                        //     'event_id' => $val['Event Name'],
                        //     'sent_status' => 0,
                        //     'category' => 'qrcode-mail'
                        // ]);

                    }

                    $logDetails[] = [
                        'clientevent_id' => isset($insertedClientEvent->clientevent_id) ? $insertedClientEvent->clientevent_id : null
                    ];
    
                    $imported = Sheets::spreadsheet(env('GOOGLE_SHEET_KEY_IMPORT'))->sheet('Client Events')->range('Z'.$val['No'] + 1)->update([[Carbon::now()->format('d-m-Y H:i:s')]]);
                    $totalImported += $imported->totalUpdatedRows;
                }

                # trigger to verifying client
                count($childIds) > 0 ? ProcessVerifyClient::dispatch($childIds)->onQueue('verifying-client') : null;
                count($parentIds) > 0 ? ProcessVerifyClientParent::dispatch($parentIds)->onQueue('verifying-client-parent') : null;
                count($teacherIds) > 0 ? ProcessVerifyClientTeacher::dispatch($teacherIds)->onQueue('verifying-client-teacher') : null;

                $response = [
                    'total_imported' => $totalImported,
                    'message' => null,
                ];
                
            }else{
                $response = [
                    'total_imported' => 0,
                    'message' => 'Data parents is uptodate'
                ];
            }
         
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Something went wrong. Please try again'
            ], 500);
        }
      
        $this->logSuccess('store', 'Import Client Event', 'Client Event', auth()->guard('api')->user()->first_name . ' ' . auth()->guard('api')->user()->last_name, $logDetails);

        return response()->json([
            'success' => true,
            'data' => $response
        ]);
    }

    public function storeClientProgram(Request $request)
    {
        $logDetails = [];

        DB::beginTransaction();
        try {

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

                $totalImported = 0;

                $logDetails = $childIds = $parentIds = $data = [];

                foreach ($arrInputData as $key => $val) {
                    # initiate variables
                    // Check existing school
                    if (!$school = School::where('sch_name', $val['School'])->first())
                        $school = $this->createSchoolIfNotExists($val['School']);

                    $roleSub = null;
                    switch ($val['Audience']) {
                        case 'Student':
                            $roleSub = 'Parent';
                            break;
                        case 'Parent':
                            $roleSub = 'Student';
                            break;
                    }
    
                    $createdMainClient = $this->createClient($val, 'main', $val['Audience'], $val['Itended Major'], $val['Destination Country'], $school);
                    $mainClient = UserClient::find($createdMainClient);
                    $createdSubClient = ($val['Audience'] == 'Student' || $val['Audience'] == 'Parent') && isset($val['Child or Parent Name']) ? $this->createClient($val, 'sub', $roleSub, $val['Itended Major'], $val['Destination Country'], $school, $mainClient) : null;

                    // Create relation parent and student
                    if(($val['Audience'] == 'Parent' || $val['Audience'] == 'Student') && isset($createdSubClient)){
                        $checkExistChildren = null;
                        switch ($val['Audience']) {
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
                        'prog_id' => $val['Program Name'],
                        'lead_id' => $val['Lead'],
                        'first_discuss_date' => Carbon::now(),
                        'status' => 0,
                        'registration_type' => 'I',
                        'referral_code' => isset($val['Referral Code']) ? $val['Referral Code'] : null,
                    ];

                    # add additional identification
                    if ($val['Audience'] == "Parent"){
                        $parentIds[] = $createdMainClient;
                        $data['client_id'] = $createdMainClient;
                        if(isset($createdSubClient))
                            $data['client_id'] = $createdSubClient;
                            $childIds[] = $createdSubClient;
                        
                    }elseif ($val['Audience'] == "Student"){
                        $childIds[] = $createdMainClient;
                        $data['client_id'] = $createdMainClient;
                        if(isset($createdSubClient))
                            $data['client_id'] = $createdSubClient;
                            $parentIds[] = $createdSubClient;
                    }


                    $existClientProgram = ClientProgram::where('prog_id', $data['prog_id'])
                        ->where('client_id', $data['client_id'])
                        ->first();

                    if (!isset($existClientProgram)) {
                        $insertedClientProgram = ClientProgram::create($data);

                        # add to log client event 
                        # to trigger the cron for send the qr email
                        // ClientEventLogMail::create([
                        //     'clientevent_id' => $insertedClientEvent->clientevent_id,
                        //     'event_id' => $val['Event Name'],
                        //     'sent_status' => 0,
                        //     'category' => 'qrcode-mail'
                        // ]);

                    }

                    $logDetails[] = [
                        'clientprog_id' => isset($insertedClientProgram->clientprog_id) ? $insertedClientProgram->clientprog_id : null
                    ];
    
                    $imported = Sheets::spreadsheet(env('GOOGLE_SHEET_KEY_IMPORT'))->sheet('Client Programs')->range('W'.$val['No'] + 1)->update([[Carbon::now()->format('d-m-Y H:i:s')]]);
                    $totalImported += $imported->totalUpdatedRows;
                }

                # trigger to verifying client
                count($childIds) > 0 ? ProcessVerifyClient::dispatch($childIds)->onQueue('verifying-client') : null;
                count($parentIds) > 0 ? ProcessVerifyClientParent::dispatch($parentIds)->onQueue('verifying-client-parent') : null;

                $response = [
                    'total_imported' => $totalImported,
                    'message' => null,
                ];
                
            }else{
                $response = [
                    'total_imported' => 0,
                    'message' => 'Data Client Programs is uptodate'
                ];
            }
         
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Something went wrong. Please try again'
            ], 500);
        }
      
        $this->logSuccess('store', 'Import Client Program', 'Client Program', auth()->guard('api')->user()->first_name . ' ' . auth()->guard('api')->user()->last_name, $logDetails);

        return response()->json([
            'success' => true,
            'data' => $response
        ]);
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
        foreach ($rawData as $data) {
            switch ($category) {
                case 'client-event':
                    $event_name = Event::where('event_title', $data['Event Name'])->get()->pluck('event_id')->first();
                    isset($event_name) ? $data['Event Name'] = $event_name : null;
                    
                    // $data['Date'] = str_replace('/', '-', $data['Date']);
                    $data['Date'] = Carbon::parse($data['Date'])->format('Y-m-d');

                    break;

                case 'client-program':
                    $programs = Program::all();
                    $program_name = $programs->where('program_name', $data['Program Name'])->pluck('prog_id')->first(); 
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
        return $arrInputData;
    }

}
