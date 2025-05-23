<?php
 
namespace App\Jobs\GoogleSheet;

use App\Http\Controllers\Api\v1\ExtClientController;
use App\Http\Controllers\GoogleSheetController;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\LoggingTrait;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Http\Traits\SyncClientTrait;
use App\Jobs\Client\ProcessDefineCategory;
use App\Jobs\Client\ProcessInsertLogClient;
use App\Jobs\RawClient\ProcessVerifyClient;
use App\Jobs\RawClient\ProcessVerifyClientParent;
use App\Jobs\RawClient\ProcessVerifyClientTeacher;
use App\Models\Client;
use App\Models\ClientEvent;
use App\Models\Event;
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

class ImportClientEvent implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use SyncClientTrait, CreateCustomPrimaryKeyTrait, LoggingTrait, SyncClientTrait, StandardizePhoneNumberTrait;
    use IsMonitored;

    public $clientEventData;
    public $is_many_request;
    /**
     * Create a new job instance.
     */
    public function __construct($clientEvent, $is_many_request = false)
    {
        $this->clientEventData = $clientEvent;
        $this->is_many_request = $is_many_request;
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

        $childIds = $parentIds = $teacherIds = $child_name = [];

        foreach ($this->clientEventData as $key => $val) {
            # initiate variables
            $status = $val['Status'] == 'Join' ? 0 : 1;

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
            $checkExistChildren = [];
            if(($val['Audience'] == 'Parent' || $val['Audience'] == 'Student') && isset($createdSubClient)){
                $checkExistChildren = null;
                switch ($val['Audience']) {
                    case 'Parent':
                        $parent = UserClient::withTrashed()->where('id', $createdMainClient)->first();
                        $student = UserClient::withTrashed()->where('id', $createdSubClient)->first();
                        $checkExistChildren = $this->checkExistClientRelation('parent', $parent, $student->full_name);
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
                        $checkExistChildren = $this->checkExistClientRelation('parent', $parent, $student->full_name);
                        !$checkExistChildren['isExist'] ? $parent->childrens()->attach($createdMainClient) : null;
                        break;
                }
            }else{
                $student = UserClient::withTrashed()->where('id', $createdMainClient)->first();
                $child_name['first_name'] = $student->first_name;
                $child_name['last_name'] = $student->last_name;
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
                'is_many_request' => true
            ];

            // Generate ticket id (if event offline)
            $event = Event::where('event_id', $val['Event Name'])->first();
            # Updated ticket id for all events
            // if(!str_contains($event->event_location, 'online')){
                $data['ticket_id'] = app(ExtClientController::class)->generateTicketID();
            // }
            
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
                // ->where('joined_date', $data['joined_date'])
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

            $imported_date[] = [Carbon::now()->format('d-m-Y H:i:s')];
            
            $childs_data_for_log_client[$key] = [
                'client_id' => $student->id,
                'first_name' => count($checkExistChildren) > 0 && $checkExistChildren['isExist'] ? $student->first_name : $child_name['first_name'],
                'last_name' => count($checkExistChildren) > 0 && $checkExistChildren['isExist'] ? $student->last_name : $child_name['last_name'],
                'lead_source' => $val['Lead'],
                'inputted_from' => 'import-client-event',
                'clientprog_id' => null
            ];
        }

        # trigger to verifying client
        // count($childIds) > 0 ? ProcessVerifyClient::dispatch($childIds, true)->onQueue('verifying-client') : null;
        // count($parentIds) > 0 ? ProcessVerifyClientParent::dispatch($parentIds, true)->onQueue('verifying-client-parent') : null;
        // count($teacherIds) > 0 ? ProcessVerifyClientTeacher::dispatch($teacherIds, true)->onQueue('verifying-client-teacher') : null;
               
        # trigger to insert log children
        count($childIds) > 0 ? ProcessInsertLogClient::dispatch($childs_data_for_log_client, true)->onQueue('insert-log-client') : null;

        Sheets::spreadsheet(env('GOOGLE_SHEET_KEY_IMPORT'))->sheet(env('APP_ENV') == 'local' ? 'test client event' : 'Client Events')->range('Z'. $this->clientEventData->first()['No'] + 1)->update($imported_date);
        $dataJobBatches = JobBatches::find($this->batch()->id);
        
        $logDetailsCollection = Collect($logDetails);
        $logDetailsMerge = $logDetailsCollection->merge(json_decode($dataJobBatches->log_details));
        JobBatches::where('id', $this->batch()->id)->update(['total_imported' => $dataJobBatches->total_imported + count($imported_date), 'log_details' => json_encode($logDetailsMerge), 'type' => 'client-event', 'category' => 'Import']); 
        

    }
   
}