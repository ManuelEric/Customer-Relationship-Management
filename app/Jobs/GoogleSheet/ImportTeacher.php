<?php
 
namespace App\Jobs\GoogleSheet;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\LoggingTrait;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Http\Traits\SyncClientTrait;
use App\Jobs\RawClient\ProcessVerifyClient;
use App\Jobs\RawClient\ProcessVerifyClientParent;
use App\Jobs\RawClient\ProcessVerifyClientTeacher;
use App\Models\Client;
use App\Models\JobBatches;
use App\Models\Role;
use App\Models\School;
use App\Models\UserClient;
use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Revolution\Google\Sheets\Facades\Sheets;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class ImportTeacher implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use SyncClientTrait, CreateCustomPrimaryKeyTrait, LoggingTrait, SyncClientTrait, StandardizePhoneNumberTrait;
    use IsMonitored;

    public $teacherData;
    /**
     * Create a new job instance.
     */
    public function __construct($teacher)
    {
        $this->teacherData = $teacher;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        if ($this->batch()->cancelled()) {
            // Determine if the batch has been cancelled...
 
            return;
        }

        foreach ($this->teacherData as $key => $val) {
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
                isset($val['Joined Date']) ? $teacherDetails['created_at'] = Carbon::parse($val['Joined Date'] . ' ' . date('H:i:s')) : null;
                isset($val['Joined Date']) ? $teacherDetails['updated_at'] = Carbon::parse($val['Joined Date'] . ' ' . date('H:i:s')) : null;

                $roleId = Role::whereRaw('LOWER(role_name) = (?)', ['teacher/counselor'])->first();

                $teacher = UserClient::create($teacherDetails);
                $teacher->roles()->attach($roleId);

            }

            $logDetails[] = [
                'client_id' => $teacher['id']
            ];

            $teacherIds[] = $teacher['id'];

            $imported_date[] = [Carbon::now()->format('d-m-Y H:i:s')];
            // $totalImported += $imported->totalUpdatedRows;
        }

        # trigger to verifying parent
        count($teacherIds) > 0 ? ProcessVerifyClientTeacher::dispatch($teacherIds)->onQueue('verifying-client-teacher') : null;

        Sheets::spreadsheet(env('GOOGLE_SHEET_KEY_IMPORT'))->sheet('Teachers')->range('R'. $this->teacherData->first()['No'] + 1)->update($imported_date);
        $dataJobBatches = JobBatches::find($this->batch()->id);
        
        $logDetailsCollection = Collect($logDetails);
        $logDetailsMerge = $logDetailsCollection->merge(json_decode($dataJobBatches->log_details));
        JobBatches::where('id', $this->batch()->id)->update(['total_imported' => $dataJobBatches->total_imported + count($imported_date), 'log_details' => json_encode($logDetailsMerge), 'type' => 'teacher', 'category' => 'Import']); 
        

    }
   
}