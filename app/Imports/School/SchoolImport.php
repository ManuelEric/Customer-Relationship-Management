<?php

namespace App\Imports\School;

use App\Exports\FailureExport;
use App\Exports\UsersFailuresImportedExport;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\LoggingTrait;
use App\Http\Traits\SyncClientTrait;
use App\Models\Organization;
use App\Models\School;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\AfterImport;
use Spatie\Permission\Models\Role;

class SchoolImport extends ToCollectionImport implements SkipsOnFailure
    , SkipsOnError
    , SkipsEmptyRows
    , WithValidation
    , WithStartRow
    , WithHeadingRow
    , WithChunkReading
    , WithBatchInserts
    , ShouldQueue
    , WithEvents
{
    use Importable, SkipsErrors, SkipsFailures, RegistersEventListeners, CreateCustomPrimaryKeyTrait, LoggingTrait,SyncClientTrait;
    
    public static function afterImport(AfterImport $event)
    {
        if (!empty(self::$allFailures)) {
            // $path = "excels/userFailures/import_failures_" . now()->format('Y-m-d H:i:s') . '.xlsx';
            $errors = [];
            foreach (self::$allFailures->toArray() as $row => $error) {
                foreach ($error as $e) {
                    // $errors = [
                    //     'row' => $e['row'][0],
                    //     'error' => $e['errors'][0]
                    // ];
                    Log::warning($e);
                    $errors[] = [
                        'message' => $e
                    ];
                }
            }
            $errors['user_id'] = 24;
        
            event(new \App\Events\MessageSent($errors, 'validation-import'));
            // (new FailureExport(self::$allFailures))->store('uploaded_file/exports/failure.xlsx');
        }
    }

    /**
     * skip heading row and start next row.
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }

    /**
     * @return string[][]
     */
    public function rules(): array
    {
        return [
            '*.sch_id' => ['nullable'],
            '*.sch_name' => ['required'],
            '*.sch_type' => ['nullable'],
            '*.sch_mail' => ['nullable'],
            '*.sch_phone' => ['nullable'],
            '*.sch_insta' => ['nullable'],
            '*.sch_city' => ['nullable'],
            '*.sch_location' => ['nullable'],
            '*.sch_score' => ['nullable'],
            '*.status' => ['nullable'],
            '*.is_verified' => ['nullable'],
        ];
    }


    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 50;
    }

    public function batchSize(): int
    {
        return 1000;
    }


    /**
     * @param Collection $rows
     */
    public function processImport(Collection $rows)
    {
        $logDetails = $updateSchools = $newSchools = []; 

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
                    $updateSchools[] = $row['sch_id'];
                    School::where('sch_id', $row['sch_id'])->update($schoolDetails);
                    // $schoolDetails['type'] = 'old';
                    // $schoolDetails['isExist'] = true;
                }else{
                    // Check existing school
                    $school = School::where('sch_name', $row['sch_name'])->get()->pluck('sch_id')->first();
                    // $schoolDetails['type'] = 'new';
                    unset($schoolDetails['sch_score']);
                    unset($schoolDetails['status']);

                    if (!isset($school)) {
                            $last_id = School::withTrashed()->max('sch_id');
                            $school_id_without_label = $this->remove_primarykey_label($last_id, 4);
                            $school_id_with_label = 'SCH-' . $this->add_digit($school_id_without_label + 1, 4);
                
                            $schoolDetails['sch_id'] = $school_id_with_label;
                            School::create($schoolDetails);
                            $newSchools[] = $schoolDetails['sch_id'];
                            // $schoolDetails['isExist'] = false;
                    }
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Import school failed : ' . $e->getMessage() . $e->getLine());
        }

        $logDetails = [
            'updateSchools' => $updateSchools,
            'newSchools' => $newSchools
        ];

        $this->logSuccess('store', 'Import School', 'School', 'unknown', $logDetails);

          
    }

}