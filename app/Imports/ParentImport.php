<?php

namespace App\Imports;

use App\Http\Traits\CheckExistingClientImport;
use App\Models\Lead;
use App\Models\UserClient;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\LoggingTrait;
use App\Http\Traits\SyncClientTrait;
use App\Jobs\RawClient\ProcessVerifyClient;
use App\Jobs\RawClient\ProcessVerifyClientParent;
use App\Models\Corporate;
use App\Models\EdufLead;
use App\Models\Event;
use App\Models\Program;
use App\Models\Role;
use App\Models\School;
use App\Models\Tag;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\ImportFailed;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ParentImport implements ToCollection, WithHeadingRow, WithValidation, WithMultipleSheets, WithChunkReading, ShouldQueue, WithEvents
{
    /**
     * @param Collection $collection
     */

    use Importable;
    use StandardizePhoneNumberTrait;
    use CreateCustomPrimaryKeyTrait;
    use CheckExistingClientImport;
    use LoggingTrait;
    use SyncClientTrait;

    public $importedBy;

    public function __construct($importedBy)
    {
        $this->importedBy = $importedBy;
    }

    public function sheets(): array
    {
        return [
            0 => $this,
        ];
    }

    public function collection(Collection $rows)
    {

        $logDetails = $parentIds = $childrenIds = [];

        DB::beginTransaction();
        try {

            foreach ($rows as $row) {
                $parent = null;
                $phoneNumber = $this->setPhoneNumber($row['phone_number']);

                $parentName = $this->explodeName($row['full_name']);

                $parent = $this->checkExistingClientImport($phoneNumber, $row['email']);

                if (!$parent['isExist']) {
                    $parentDetails = [
                        'first_name' => $parentName['firstname'],
                        'last_name' => isset($parentName['lastname']) ? $parentName['lastname'] : null,
                        'mail' => $row['email'],
                        'phone' => $phoneNumber,
                        'dob' => isset($row['date_of_birth']) ? $row['date_of_birth'] : null,
                        'insta' => isset($row['instagram']) ? $row['instagram'] : null,
                        'state' => isset($row['state']) ? $row['state'] : null,
                        'city' => isset($row['city']) ? $row['city'] : null,
                        'address' => isset($row['address']) ? $row['address'] : null,
                        'lead_id' => $row['lead'],
                        'event_id' => isset($row['event']) && $row['lead'] == 'LS004' ? $row['event'] : null,
                        'eduf_id' => isset($row['edufair'])  && $row['lead'] == 'LS018' ? $row['edufair'] : null,
                        'st_levelinterest' => $row['level_of_interest'],
                    ];

                    isset($row['joined_date']) ? $parentDetails['created_at'] = $row['joined_date'] : null;
                    
                    $roleId = Role::whereRaw('LOWER(role_name) = (?)', ['parent'])->first();

                    $parent = UserClient::create($parentDetails);
                    $parent->roles()->attach($roleId);
                } else {
                    $parent = UserClient::find($parent['id']);
                }

                $children = null;
                $checkExistChildren = null;
                if (isset($row['children_name'])) {
                    $checkExistChildren = $this->checkExistClientRelation('parent', $parent, $row['children_name']);
                    
                    if($checkExistChildren['isExist'] && $checkExistChildren['client'] != null){
                        $children = $checkExistChildren['client'];
                    }else if(!$checkExistChildren['isExist']){
                        $name = $this->explodeName($row['children_name']);
                        $school = School::where('sch_name', $row['school'])->first();

                        if (!isset($school)) {
                            $school = $this->createSchoolIfNotExists($row['school']);
                        }

                        $childrenDetails = [
                            'first_name' => $name['firstname'],
                            'last_name' => isset($name['lastname']) ? $name['lastname'] : null,
                            'sch_id' => $school->sch_id,
                            'graduation_year' => isset($row['graduation_year']) ? $row['graduation_year'] : null,
                            'lead_id' => $row['lead'],
                            'event_id' => isset($row['event']) && $row['lead'] == 'LS004' ? $row['event'] : null,
                            'eduf_id' => isset($row['edufair'])  && $row['lead'] == 'LS018' ? $row['edufair'] : null,
                        ];

                        isset($row['joined_date']) ? $childrenDetails['created_at'] = $row['joined_date'] : null;

                        $roleId = Role::whereRaw('LOWER(role_name) = (?)', ['student'])->first();

                        $children = UserClient::create($childrenDetails);
                        $children->roles()->attach($roleId);
                        $parent->childrens()->attach($children);
                    }

                    if (isset($row['interested_program'])) {
                        $this->syncInterestProgram($row['interested_program'], $parent);
                        $children != null ?  $this->syncInterestProgram($row['interested_program'], $children) : null;
                    }

                    // Sync country of study abroad
                    if (isset($row['destination_country'])) {
                        $children != null ?  $this->syncDestinationCountry($row['destination_country'], $children) : null;
                    }
                    $childrenIds[] = $children['id'];
                }

               
                $parentIds[] = $parent['id'];

                $logDetails[] = [
                    'client_id' => $parent['id']
                ];
            }
            # trigger to verifying parent
            count($parentIds) > 0 ? ProcessVerifyClientParent::dispatch($parentIds)->onQueue('verifying-client-parent') : null;
            
            # trigger to verifying children
            count($childrenIds) > 0 ? ProcessVerifyClient::dispatch($childrenIds)->onQueue('verifying-client') : null;

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Import parent failed : ' . $e->getMessage() . ' ' . $e->getLine());
        }

        $this->logSuccess('store', 'Import Parent', 'Parent', $this->importedBy->first_name . ' ' . $this->importedBy->last_name, $logDetails);
    }

    public function prepareForValidation($data)
    {

        DB::beginTransaction();
        try {

            if ($data['lead'] == 'School' || $data['lead'] == 'Counselor') {
                $data['lead'] = 'School/Counselor';
            }

            if ($data['lead'] == 'KOL') {
                $lead = 'KOL';
            } else {
                $lead = Lead::where('main_lead', $data['lead'])->get()->pluck('lead_id')->first();
            }

            $event = Event::where('event_title', $data['event'])->get()->pluck('event_id')->first();
            $getAllEduf = EdufLead::all();
            $edufair = $getAllEduf->where('organizerName', $data['edufair'])->pluck('id')->first();
            $partner = Corporate::where('corp_name', $data['partner'])->get()->pluck('corp_id')->first();
            $kol = Lead::where('main_lead', 'KOL')->where('sub_lead', $data['kol'])->get()->pluck('lead_id')->first();

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Import parent failed : ' . $e->getMessage());
        }

        $data = [
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'date_of_birth' => isset($data['date_of_birth']) ? Date::excelToDateTimeObject($data['date_of_birth'])
                ->format('Y-m-d') : null,
            'instagram' => $data['instagram'],
            'state' => $data['state'],
            'city' => $data['city'],
            'address' => $data['address'],
            'lead' => isset($lead) ? $lead : $data['lead'],
            'event' => isset($event) ? $event : $data['event'],
            'partner' => isset($partner) ? $partner : $data['partner'],
            'edufair' => isset($edufair) ? $edufair : $data['edufair'],
            'kol' => isset($kol) ? $kol : $data['kol'],
            'level_of_interest' => $data['level_of_interest'],
            'interested_program' => $data['interested_program'],
            'children_name' => $data['children_name'],
            'school' => $data['school'],
            'graduation_year' => $data['graduation_year'],
            'destination_country' => $data['destination_country'],
            'joined_date' => isset($data['joined_date']) ? Date::excelToDateTimeObject($data['joined_date'])->format('Y-m-d') : null,
        ];

        return $data;
    }

    public function rules(): array
    {
        return [
            '*.full_name' => ['required'],
            '*.email' => ['required', 'email'],
            '*.phone_number' => ['required', 'min:5', 'max:15'],
            '*.date_of_birth' => ['nullable', 'date'],
            '*.instagram' => ['nullable', 'unique:tbl_client,insta'],
            '*.state' => ['nullable'],
            '*.city' => ['nullable'],
            '*.address' => ['nullable'],
            '*.lead' => ['required'],
            '*.event' => ['required_if:lead,LS004', 'nullable', 'exists:tbl_events,event_id'],
            '*.partner' => ['required_if:lead,LS015', 'nullable', 'exists:tbl_corp,corp_id'],
            '*.edufair' => ['required_if:lead,LS018', 'nullable', 'exists:tbl_eduf_lead,id'],
            '*.kol' => ['required_if:lead,KOL', 'nullable', 'exists:tbl_lead,lead_id'],
            '*.level_of_interest' => ['nullable', 'in:High,Medium,Low'],
            '*.interested_program' => ['nullable'],
            '*.children_name' => ['nullable'],
            '*.school' => ['nullable'],
            '*.graduation_year' => ['nullable'],
            '*.destination_country' => ['nullable'],
            '*.joined_date' => ['nullable', 'date'],
        ];
    }

    private function explodeName($name)
    {

        $fullname = explode(' ', $name);
        $limit = count($fullname);

        $data = [];

        if ($limit > 1) {
            $data['lastname'] = $fullname[$limit - 1];
            unset($fullname[$limit - 1]);
            $data['firstname'] = implode(" ", $fullname);
        } else {
            $data['firstname'] = implode(" ", $fullname);
        }

        return $data;
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
