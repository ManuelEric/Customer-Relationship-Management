<?php
 
namespace App\Jobs\GoogleSheet;

use App\Http\Controllers\GoogleSheetController;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\LoggingTrait;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Http\Traits\SyncClientTrait;
use App\Jobs\Client\ProcessDefineCategory;
use App\Jobs\Client\ProcessInsertLogClient;
use App\Jobs\RawClient\ProcessVerifyClient;
use App\Jobs\RawClient\ProcessVerifyClientParent;
use App\Models\Client;
use App\Models\ClientProgram;
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

class ImportClientProgram implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use SyncClientTrait, CreateCustomPrimaryKeyTrait, LoggingTrait, SyncClientTrait, StandardizePhoneNumberTrait;
    use IsMonitored;

    public $clientProgData;
    /**
     * Create a new job instance.
     */
    public function __construct($clientProg)
    {
        $this->clientProgData = $clientProg;
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

        foreach ($this->clientProgData as $key => $val) {
            # initiate variables
            // Check existing school
            if (!$school = School::where('sch_name', $val['School'])->first())
                $school = $this->createSchoolIfNotExists($val['School'], true);

            $roleSub = null;
            switch ($val['Audience']) {
                case 'Student':
                    $roleSub = 'Parent';
                    break;
                case 'Parent':
                    $roleSub = 'Student';
                    break;
            }

            $createdMainClient = app(GoogleSheetController::class)->createClient($val, 'main', $val['Audience'], $val['Itended Major'], $val['Destination Country'], $school);
            $mainClient = UserClient::withTrashed()->where('id', $createdMainClient)->first();
            $createdSubClient = ($val['Audience'] == 'Student' || $val['Audience'] == 'Parent') && isset($val['Child or Parent Name']) ? app(GoogleSheetController::class)->createClient($val, 'sub', $roleSub, $val['Itended Major'], $val['Destination Country'], $school, $mainClient) : null;

            $student_fullname = $val['Name'];
            $child_name['first_name'] = $this->split($student_fullname)['first_name'];
            $child_name['last_name'] = $this->split($student_fullname)['last_name'];


            // Create relation parent and student
            if(($val['Audience'] == 'Parent' || $val['Audience'] == 'Student') && isset($createdSubClient)){
                $checkExistChildren = null;
                switch ($val['Audience']) {
                    case 'Parent':
                        $parent = UserClient::withTrashed()->where('id', $createdMainClient)->first();
                        $student = UserClient::withTrashed()->where('id', $createdSubClient)->first();
                        $checkExistChildren = $this->checkExistClientRelation('parent', $parent, $student->fullName);
                        !$checkExistChildren['isExist'] ? $parent->childrens()->attach($createdSubClient) : null;

                        $student_fullname = isset($val['Child or Parent Name']) ? $val['Child or Parent Name'] : null;
                        if ($student_fullname != null)
                        {
                            $child_name['first_name'] = $this->split($student_fullname)['first_name'];
                            $child_name['last_name'] = $this->split($student_fullname)['last_name'];
                        }
                        break;

                    case 'Student':
                        $parent = UserClient::withTrashed()->where('id', $createdSubClient)->first();
                        $student = UserClient::withTrashed()->where('id', $createdMainClient)->first();
                        $checkExistChildren = $this->checkExistClientRelation('parent', $parent, $student->fullName);
                        !$checkExistChildren['isExist'] ? $parent->childrens()->attach($createdMainClient) : null;
                        break;
                }
            }

            // Insert client program
            $data = [
                'prog_id' => $val['Program Name'],
                'lead_id' => $val['Lead'],
                'first_discuss_date' => Carbon::now(),
                'status' => 0,
                'registration_type' => 'I',
                'referral_code' => isset($val['Referral Code']) ? $val['Referral Code'] : null,
                'is_many_request' => true
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
                $clientprog_id = $insertedClientProgram->clientprog_id;

                # add to log client event 
                # to trigger the cron for send the qr email
                // ClientEventLogMail::create([
                //     'clientevent_id' => $insertedClientEvent->clientevent_id,
                //     'event_id' => $val['Event Name'],
                //     'sent_status' => 0,
                //     'category' => 'qrcode-mail'
                // ]);

            }else{
                $clientprog_id = $existClientProgram->clientprog_id;
            }

            $logDetails[] = [
                'clientprog_id' => $clientprog_id
            ];

            $imported_date[] = [Carbon::now()->format('d-m-Y H:i:s')];
            
            $childs_data_for_log_client[$key] = [
                'client_id' => isset($createdSubClient) ? $student->id : $mainClient->id,
                'first_name' => $checkExistChildren['isExist'] ? $student->first_name : $child_name['first_name'],
                'last_name' => $checkExistChildren['isExist'] ? $student->last_name : $child_name['last_name'],
                'lead_source' => $val['Lead'],
                'inputted_from' => 'import-client-program',
                'clientprog_id' => $clientprog_id
            ];
        }

        # trigger to verifying client
        // count($childIds) > 0 ? ProcessVerifyClient::dispatch($childIds, true)->onQueue('verifying-client') : null;
        // count($parentIds) > 0 ? ProcessVerifyClientParent::dispatch($parentIds, true)->onQueue('verifying-client-parent') : null;

        # trigger to define category children
        count($childIds) > 0 ? ProcessInsertLogClient::dispatch($childs_data_for_log_client, true)->onQueue('insert-log-client') : null;

        Sheets::spreadsheet(env('GOOGLE_SHEET_KEY_IMPORT'))->sheet(env('APP_ENV') == 'local' ? 'test client program' : 'Client Programs')->range('W'. $this->clientProgData->first()['No'] + 1)->update($imported_date);
        $dataJobBatches = JobBatches::find($this->batch()->id);
        
        $logDetailsCollection = Collect($logDetails);
        $logDetailsMerge = $logDetailsCollection->merge(json_decode($dataJobBatches->log_details));
        JobBatches::where('id', $this->batch()->id)->update(['total_imported' => $dataJobBatches->total_imported + count($imported_date), 'log_details' => json_encode($logDetailsMerge), 'type' => 'client-program', 'category' => 'Import']);
        

    }
   
}