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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\ImportFailed;
use PhpOffice\PhpSpreadsheet\Shared\Date;


class SchoolImport implements ToCollection, WithHeadingRow, WithValidation, WithMultipleSheets, WithChunkReading, ShouldQueue, WithEvents
{
    /**
     * @param Collection $collection
     */

    use Importable;
    use StandardizePhoneNumberTrait;
    use CheckExistingClientImport;
    use CreateCustomPrimaryKeyTrait;
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

        $logDetails = $childIds = $parentIds = []; 

        DB::beginTransaction();
        try {


            foreach ($rows as $row) {


                $schoolDetails = [
                    'sch_id' => $row['sch_id'] ?? null,
                    'sch_name' => $row['sch_name'],
                    'sch_type' => $row['sch_type'] ?? null,
                    'sch_mail' => $row['sch_mail'] ?? null,
                    'sch_phone' => $row['sch_phone'] ?? null,
                    'sch_insta' => $row['sch_insta'] ?? null,
                    'sch_city' => $row['sch_city'] ?? null,
                    'sch_location' => $row['sch_location'] ?? null,
                    'sch_score' => $row['sch_score'] ?? null,
                    'status' => $row['status'] ?? null,
                    'is_verified' => $row['is_verified'] ?? null,
                ];


                if(isset($row['sch_id'])){
                    School::whereSchoolId($row['sch_id'])->update($schoolDetails);
                }else{
                    // Check existing school
                    $school = School::where('sch_name', $row['sch_name'])->get()->pluck('sch_id')->first();
                    if (!isset($school)) {
                            $last_id = School::withTrashed()->max('sch_id');
                            $school_id_without_label = $this->remove_primarykey_label($last_id, 4);
                            $school_id_with_label = 'SCH-' . $this->add_digit($school_id_without_label + 1, 4);
                
                            $schoolDetails['sch_id'] = $school_id_with_label;
                            School::create($schoolDetails);
                    }
                }
            }

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Import school failed : ' . $e->getMessage() . $e->getLine());
        }

        $this->logSuccess('store', 'Import School', 'School', $this->importedBy->first_name . ' ' . $this->importedBy->last_name, $logDetails);

          
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
            'sch_id' => $data['sch_id'],
            'sch_name' => $data['sch_name'],
            'sch_type' => $data['sch_type'],
            'sch_mail' => $data['sch_mail'],
            'sch_phone' => $data['sch_phone'],
            'sch_insta' => $data['sch_insta'],
            'sch_city' => $data['sch_city'],
            'sch_location' => $data['sch_location'],
            'sch_score' => $data['sch_score'],
            'status' => $data['status'],
            'is_verified' => $data['is_verified'],
        ];
        return $data;
    }

    public function rules(): array
    {
        return [
            '*.sch_id' => ['nullable'],
            '*.sch_name' => ['required'],
            '*.sch_type' => ['nullable'],
            '*.sch_mail' => ['nullable', 'email'],
            '*.sch_phone' => ['nullable'],
            '*.sch_insta' => ['nullable'],
            '*.sch_city' => ['nullable'],
            '*.sch_location' => ['required'],
            '*.sch_score' => ['nullable'],
            '*.status' => ['nullable'],
            '*.is_verified' => ['nullable'],
        ];
    }

    public function registerEvents(): array
    {
        return [
            ImportFailed::class => function(ImportFailed $event) {
                foreach($event->getException() as $exception){
                    $validation[] = $exception !== null && gettype($exception) == "object" ? $exception->errors()->toArray() : null;
                }
                $validation['user_id'] = $this->importedBy->id;
                // event(new \App\Events\MessageSent($validation, 'validation-import'));
            },
        ];
    }


    public function chunkSize(): int
    {
        return 50;
    }
}
