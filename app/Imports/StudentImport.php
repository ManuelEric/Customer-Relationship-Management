<?php

namespace App\Imports;

use App\Http\Traits\CheckExistingClientImport;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
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
use App\Models\Lead;
use App\Models\Major;
use App\Models\Program;
use App\Models\Role;
use App\Models\School;
use App\Models\Tag;
use App\Models\University;
use App\Models\UserClient;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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


class StudentImport extends ToCollectionImport implements SkipsOnFailure
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

    use Importable, SkipsErrors, SkipsFailures, RegistersEventListeners, CreateCustomPrimaryKeyTrait, LoggingTrait, SyncClientTrait, StandardizePhoneNumberTrait, CheckExistingClientImport;

    public static function beforeImport(BeforeImport $event)
    {
        $totalRow = $event->getReader()->getTotalRows();
        Cache::put('isStartImport', true);
        
        $progress = [
            'import_id' => Cache::get('import_id'),
            'import_name' => 'student import',
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
            'import_name' => 'student import',
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

        $logDetails = $childIds = $parentIds = []; 

        DB::beginTransaction();
        try {


            foreach ($rows as $row) {
                // $interestPrograms = '';
                $student = null;
                $phoneNumber = isset($row['phone_number']) ? $this->setPhoneNumber($row['phone_number']) : null;
                isset($row['parents_phone']) ? $parentPhone = $this->setPhoneNumber($row['parents_phone']) : $parentPhone = null;

                $studentName = $row['full_name'] != null ? $this->explodeName($row['full_name']) : null;
                $parentName = $row['parents_name'] != null ? $this->explodeName($row['parents_name']) : null;

                // $last_id = UserClient::max('st_id');
                // $student_id_without_label = $this->remove_primarykey_label($last_id, 3);
                // $studentId = 'ST-' . $this->add_digit((int) $student_id_without_label + 1, 4);

                // Check existing school
                $school = School::where('sch_name', $row['school'])->get()->pluck('sch_id')->first();

                if (!isset($school)) {
                    $newSchool = $this->createSchoolIfNotExists($row['school']);
                }

                $mail = isset($row['email']) ? $row['email'] : null;
                $student = $this->checkExistingClientImport($phoneNumber, $mail);

                if (!$student['isExist']) {
                    $studentDetails = [
                        // 'st_id' => $studentId,
                        'first_name' => $studentName != null ? $studentName['firstname'] : ($parentName != null ? $parentName['firstname'] . ' ' . $parentName['lastname'] : null),
                        'last_name' =>  $studentName != null && isset($studentName['lastname']) ? $studentName['lastname'] : ($parentName != null ? 'Child' : null),
                        'mail' => $mail,
                        'phone' => $phoneNumber,
                        'dob' => isset($row['date_of_birth']) ? $row['date_of_birth'] : null,
                        'insta' => isset($row['instagram']) ? $row['instagram'] : null,
                        'state' => isset($row['state']) ? $row['state'] : null,
                        'city' => isset($row['city']) ? $row['city'] : null,
                        'address' => isset($row['address']) ? $row['address'] : null,
                        'sch_id' => isset($school) ? $school : $newSchool->sch_id,
                        'st_grade' => $row['grade'],
                        'lead_id' => $row['lead'] == 'KOL' ? $row['kol'] : $row['lead'],
                        'event_id' => isset($row['event']) && $row['lead'] == 'LS004' ? $row['event'] : null,
                        // 'partner_id' => isset($row['partner']) && $row['lead'] == 'LS015' ? $row['partner'] : null,
                        'eduf_id' => isset($row['edufair'])  && $row['lead'] == 'LS018' ? $row['edufair'] : null,
                        'st_levelinterest' => $row['level_of_interest'],
                        'graduation_year' => isset($row['graduation_year']) ? $row['graduation_year'] : null,
                        'st_abryear' => isset($row['year_of_study_abroad']) ? $row['year_of_study_abroad'] : null,
                    ];

                    isset($row['joined_date']) ? $studentDetails['created_at'] = $row['joined_date'] : null;
                    isset($row['joined_date']) ? $studentDetails['updated_at'] = $row['joined_date'] : null;
                    
                    $roleId = Role::whereRaw('LOWER(role_name) = (?)', ['student'])->first();

                    $student = UserClient::create($studentDetails);
                    $student->roles()->attach($roleId);

                } else {
                    $student = UserClient::find($student['id']);

                }

                // Connecting student with parent
                $checkExistParent = null;
                $parent = null;
                if (isset($row['parents_name'])) {
                    // $this->createParentsIfNotExists($row['parents_name'], $parentPhone, $student);
                    $checkExistParent = $this->checkExistClientRelation('student', $student, $row['parents_name']);
                    if($checkExistParent['isExist'] && $checkExistParent['client'] != null){
                        $parent = $checkExistParent['client'];
                    }else if(!$checkExistParent['isExist']){
                        $name = $this->explodeName($row['parents_name']);

                        $parentDetails = [
                            'first_name' => $name['firstname'],
                            'last_name' => isset($name['lastname']) ? $name['lastname'] : null,
                            'phone' => isset($parentPhone) ? $parentPhone : null,
                        ];

                        $roleId = Role::whereRaw('LOWER(role_name) = (?)', ['parent'])->first();

                        $parent = UserClient::create($parentDetails);
                        $parent->roles()->attach($roleId);
                        $student->parents()->attach($parent);
                    }
                    $parentIds[] = $parent['id'];
                }

                // Sync interest program
                if (isset($row['interested_program'])) {
                    $this->syncInterestProgram($row['interested_program'], $student);
                }

                // Sync country of study abroad
                if (isset($row['country_of_study_abroad'])) {
                    $this->syncDestinationCountry($row['country_of_study_abroad'], $student);
                }

                // Sync interest major
                if (isset($row['interest_major'])) {
                    $this->syncInterestMajor($row['interest_major'], $student);
                }

                $logDetails[] = [
                    'client_id' => $student['id']
                ];

                $childIds[] = $student['id'];
            }

            # trigger to verifying children
            count($childIds) > 0 ? ProcessVerifyClient::dispatch($childIds)->onQueue('verifying-client') : null;

            # trigger to verifying parent
            count($parentIds) > 0 ? ProcessVerifyClientParent::dispatch($parentIds)->onQueue('verifying-client-parent') : null;

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Import student failed : ' . $e->getMessage() . $e->getLine());
        }

        $auth = Cache::has('auth') ? Cache::get('auth')->first_name . ' ' . Cache::get('auth')->last_name : 'unknown';

        $this->logSuccess('store', 'Import Student', 'Student', $auth, $logDetails);

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

            // $parentId = UserClient::where(DB::raw('CONCAT(first_name, " ", COALESCE(last_name))'), $data['parents_name'])->get()->pluck('id')->first();
            $event = Event::where('event_title', $data['event'])->get()->pluck('event_id')->first();
            $getAllEduf = EdufLead::all();
            $edufair = $getAllEduf->where('organizerName', $data['edufair'])->pluck('id')->first();
            $partner = Corporate::where('corp_name', $data['partner'])->get()->pluck('corp_id')->first();
            $kol = Lead::where('main_lead', 'KOL')->where('sub_lead', $data['kol'])->get()->pluck('lead_id')->first();

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Import student failed : ' . $e->getMessage());
        }

        $data = [
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'date_of_birth' => isset($data['date_of_birth']) ? Date::excelToDateTimeObject($data['date_of_birth'])
                ->format('Y-m-d') : null,
            'parents_name' => $data['parents_name'],
            'parents_phone' => $data['parents_phone'],
            'school' => $data['school'],
            'graduation_year' => $data['graduation_year'],
            'grade' => $data['grade'],
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
            'year_of_study_abroad' => $data['year_of_study_abroad'],
            'country_of_study_abroad' => $data['country_of_study_abroad'],
            // 'university_destination' => $data['university_destination'],
            'interest_major' => $data['interest_major'],
            'joined_date' => isset($data['joined_date']) ? Date::excelToDateTimeObject($data['joined_date'])->format('Y-m-d') : null,
        ];
        return $data;
    }

    public function rules(): array
    {
        return [
            '*.full_name' => ['required'],
            '*.email' => ['required', 'email'],
            '*.phone_number' => ['nullable', 'min:5', 'max:15'],
            '*.date_of_birth' => ['nullable', 'date'],
            '*.parents_name' => ['required', 'different:*.full_name'],
            '*.parents_phone' => ['nullable', 'min:5', 'max:15', 'diffrent:*.phone_number'],
            '*.school' => ['required'],
            '*.graduation_year' => ['nullable', 'integer'],
            '*.grade' => ['required', 'integer'],
            '*.instagram' => ['nullable'],
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
            '*.year_of_study_abroad' => ['nullable', 'integer'],
            '*.country_of_study_abroad' => ['nullable'],
            '*.interest_major' => ['nullable'],
            '*.joined_date' => ['nullable', 'date'],
        ];
    }

    private function createParentsIfNotExists($parentName, $parentPhone, $student)
    {

        $parent = UserClient::all();
        $mapParent = $parent->map(
            function ($item, int $key) {
                return [
                    'id' => $item->id,
                    'full_name' => $item->fullName,
                ];
            }
        );

        $existParent = $mapParent->where('full_name', $parentName)->first();

        if (!isset($existParent)) {
            $name = $this->explodeName($parentName);

            $parentDetails = [
                'first_name' => $name['firstname'],
                'last_name' => isset($name['lastname']) ? $name['lastname'] : null,
                'phone' => isset($parentPhone) ? $parentPhone : null,
            ];

            $roleId = Role::whereRaw('LOWER(role_name) = (?)', ['parent'])->first();

            $parent = UserClient::create($parentDetails);
            $parent->roles()->attach($roleId);
            $student->parents()->sync($parent->id);

        } else {

            $student->parents()->sync($existParent['id']);
        }
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

    public function chunkSize(): int
    {
        return 50;
    }

    public function batchSize(): int
    {
        return 1000;
    }

}
