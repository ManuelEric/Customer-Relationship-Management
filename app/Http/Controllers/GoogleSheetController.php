<?php

namespace App\Http\Controllers;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\LoggingTrait;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Http\Traits\SyncClientTrait;
use App\Jobs\RawClient\ProcessVerifyClient;
use App\Jobs\RawClient\ProcessVerifyClientParent;
use App\Models\Corporate;
use App\Models\EdufLead;
use App\Models\Event;
use App\Models\Lead;
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

class GoogleSheetController extends Controller
{
    use SyncClientTrait, CreateCustomPrimaryKeyTrait, LoggingTrait, SyncClientTrait, StandardizePhoneNumberTrait;

    public function storeParent(Request $request)
    {
        DB::beginTransaction();
        try {

            $start = $request->start;
            $end = $request->end;

            $header = Sheets::getService()->spreadsheets_values->get(env('GOOGLE_SHEET_KEY_IMPORT'), 'Parents!A1:V1')->getValues();
            $sheet = Sheets::getService()->spreadsheets_values->get(env('GOOGLE_SHEET_KEY_IMPORT'), 'Parents!A'.$start.':V'.$end, ['valueRenderOption' => 'FORMATTED_VALUE'])->getValues();

            $values = Sheets::collection($header[0], $sheet);
        
            $rawData = $values->where('Imported Date', null);
        
            $response = [];
           
            if(count($rawData) > 0){

                foreach ($rawData as $data) {
                    if ($data['Lead'] == 'School' || $data['Lead'] == 'Counselor') {
                        $data['Lead'] = 'School/Counselor';
                    }else if($data['Lead'] == 'KOL'){
                        $data['Lead'] = 'KOL';
                    }else{
                        $lead = Lead::where('main_lead', $data['Lead'])->get()->pluck('lead_id')->first();
                        isset($lead) ? $data['Lead'] = $lead : null;
                    }

                    $event = Event::where('event_title', $data['Event'])->get()->pluck('event_id')->first();
                    $getAllEduf = EdufLead::all();
                    $edufair = $getAllEduf->where('organizerName', $data['Edufair'])->pluck('id')->first();
                    $partner = Corporate::where('corp_name', $data['Partner'])->get()->pluck('corp_id')->first();
                    $kol = Lead::where('main_lead', 'KOL')->where('sub_lead', $data['KOL'])->get()->pluck('lead_id')->first();
                    
                    isset($event) ? $data['Event'] = $event : null;
                    isset($edufair) ? $data['Edufair'] = $edufair : null;
                    isset($partner) ? $data['Partner'] = $partner : null;
                    isset($kol) ? $data['KOL'] = $kol : null;
        
                    $data['Joined Date'] = str_replace('/', '-', $data['Joined Date']);
                    $data['Joined Date'] = Carbon::parse($data['Joined Date'])->format('Y-m-d');

                    $arrInputData[$data['No']] = $data->toArray();
                }


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
      
        return response()->json([
            'success' => true,
            'data' => $response
        ]);
    }


}
