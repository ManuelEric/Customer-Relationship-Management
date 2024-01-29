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
use App\Models\Role;
use App\Models\School;
use Maatwebsite\Excel\Concerns\Importable;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\LoggingTrait;
use App\Jobs\RawClient\ProcessVerifyClientTeacher;
use App\Models\Corporate;
use App\Models\EdufLead;
use App\Models\Event;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\ImportFailed;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class TeacherImport extends ToCollectionImport implements SkipsOnFailure
, SkipsOnError
, SkipsEmptyRows
, WithValidation
, WithStartRow
, WithHeadingRow
, WithChunkReading
, WithBatchInserts
, ShouldQueue
, WithEvents
, WithMultipleSheets
{
    /**
     * @param Collection $collection
     */

    use Importable, SkipsErrors, SkipsFailures, RegistersEventListeners, CreateCustomPrimaryKeyTrait, LoggingTrait, StandardizePhoneNumberTrait, CheckExistingClientImport;

    public static function beforeImport(BeforeImport $event)
    {
        $totalRow = $event->getReader()->getTotalRows();
        Cache::put('isStartImport', true);
        
        $progress = [
            'import_id' => Cache::get('import_id'),
            'import_name' => 'teacher import',
            'user_id' => Cache::get('auth')->id,
            'isStart' => true,
            'isFinish' => false,
            'total_row' => reset($totalRow) - 1
        ];

        event(new \App\Events\MessageSent($progress, 'progress-import'));

    }

    public static function afterImport(AfterImport $event)
    {
        $auth = Cache::has('auth') ? Cache::pull('auth')->id : null;
        $import_id = Cache::has('import_id') ? Cache::pull('import_id') : null;
        Cache::forget('isStartImport');
        $totalRow = $event->getReader()->getTotalRows();

        $progress = [
            'import_id' => $import_id,
            'import_name' => 'teacher import',
            'user_id' => $auth,
            'isStart' => false,
            'isFinish' => true,
            'total_row' => reset($totalRow) - 1,
            'total_error' => !empty(self::$allFailures) ? count(self::$allFailures) : 0
        ];
        
        $info = [];
        if (!empty(self::$allFailures)) {
            foreach (self::$allFailures->toArray() as $row => $error) {
                foreach ($error as $e) {
                    Log::warning($e);
                    $info[] = [
                        'message' => $e
                    ];
                }
            }
            $info['user_id'] = $auth;
        
        }

        $info['progress'] = $progress; 
        event(new \App\Events\MessageSent($info, 'validation-import'));
    }

    public function sheets(): array
    {
        return [
            0 => $this,
        ];
    }

     /**
     * skip heading row and start next row.
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }

    public function processImport(Collection $rows)
    {
        $logDetails = $teacherIds = [];

        DB::beginTransaction();
        try {

            foreach ($rows as $row) {
                $teacher = null;
                $phoneNumber = $this->setPhoneNumber($row['phone_number']);

                $teacherName = $this->explodeName($row['full_name']);

                // Check existing school
                $school = School::where('sch_name', $row['school'])->get()->pluck('sch_id')->first();

                if (!isset($school)) {
                    $newSchool = $this->createSchoolIfNotExists($row['school']);
                }

                $teacher = $this->checkExistingClientImport($phoneNumber, $row['email']);

                if (!$teacher['isExist']) {
                    $teacherDetails = [
                        'first_name' => $teacherName['firstname'],
                        'last_name' => isset($teacherName['lastname']) ? $teacherName['lastname'] : null,
                        'mail' => $row['email'],
                        'phone' => $phoneNumber,
                        'dob' => isset($row['date_of_birth']) ? $row['date_of_birth'] : null,
                        'insta' => isset($row['instagram']) ? $row['instagram'] : null,
                        'state' => isset($row['state']) ? $row['state'] : null,
                        'city' => isset($row['city']) ? $row['city'] : null,
                        'address' => isset($row['address']) ? $row['address'] : null,
                        'sch_id' => isset($school) ? $school : $newSchool->sch_id,
                        'lead_id' => $row['lead'],
                        'event_id' => isset($row['event']) && $row['lead'] == 'LS004' ? $row['event'] : null,
                        'eduf_id' => isset($row['edufair'])  && $row['lead'] == 'LS018' ? $row['edufair'] : null,
                        'st_levelinterest' => $row['level_of_interest'],
                    ];
                    $roleId = Role::whereRaw('LOWER(role_name) = (?)', ['teacher/counselor'])->first();

                    $teacher = UserClient::create($teacherDetails);
                    $teacher->roles()->attach($roleId);

                }

                $logDetails[] = [
                    'client_id' => $teacher['id']
                ];

                $teacherIds[] = $teacher['id'];
            }

            # trigger to verifying parent
            count($teacherIds) > 0 ? ProcessVerifyClientTeacher::dispatch($teacherIds)->onQueue('verifying-client-teacher') : null;

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Import teacher failed : ' . $e->getMessage());
        }

        $auth = Cache::has('auth') ? Cache::get('auth')->first_name . ' ' . Cache::get('auth')->last_name : 'unknown';

        $this->logSuccess('store', 'Import Teacher', 'Parent', $auth, $teacher);

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
            Log::error('Import teacher failed : ' . $e->getMessage());
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
            'school' => $data['school'],
            'lead' => isset($lead) ? $lead : $data['lead'],
            'event' => isset($event) ? $event : $data['event'],
            'partner' => isset($partner) ? $partner : $data['partner'],
            'edufair' => isset($edufair) ? $edufair : $data['edufair'],
            'kol' => isset($kol) ? $kol : $data['kol'],
            'level_of_interest' => $data['level_of_interest'],
        ];

        return $data;
    }

    public function rules(): array
    {
        return [
            '*.full_name' => ['required'],
            '*.email' => ['required', 'email', 'unique:tbl_client,mail'],
            '*.phone_number' => ['required', 'min:5', 'max:15'],
            '*.date_of_birth' => ['nullable', 'date'],
            '*.instagram' => ['nullable', 'unique:tbl_client,insta'],
            '*.state' => ['nullable'],
            '*.city' => ['nullable'],
            '*.address' => ['nullable'],
            '*.school' => ['required'],
            '*.lead' => ['required'],
            '*.event' => ['required_if:lead,LS004', 'nullable', 'exists:tbl_events,event_id'],
            '*.partner' => ['required_if:lead,LS015', 'nullable', 'exists:tbl_corp,corp_id'],
            '*.edufair' => ['required_if:lead,LS018', 'nullable', 'exists:tbl_eduf_lead,id'],
            '*.kol' => ['required_if:lead,KOL', 'nullable', 'exists:tbl_lead,lead_id'],
            '*.level_of_interest' => ['nullable', 'in:High,Medium,Low'],
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

    private function createSchoolIfNotExists($sch_name)
    {
        $last_id = School::max('sch_id');
        $school_id_without_label = $this->remove_primarykey_label($last_id, 4);
        $school_id_with_label = 'SCH-' . $this->add_digit($school_id_without_label + 1, 4);

        $newSchool = School::create(['sch_id' => $school_id_with_label, 'sch_name' => $sch_name]);

        return $newSchool;
    }
    
    public function chunkSize(): int
    {
        return 50;
    }

    public function batchSize(): int
    {
        return 1000;
    }

}
