<?php

namespace App\Jobs\GoogleSheet;

use App\Http\Controllers\Api\v1\ExtClientController;
use App\Http\Controllers\GoogleSheetController;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\LoggingTrait;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Http\Traits\SyncClientTrait;
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
use Illuminate\Support\Facades\Log;
use Revolution\Google\Sheets\Facades\Sheets;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class ImportClientEvent implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use CreateCustomPrimaryKeyTrait, LoggingTrait, StandardizePhoneNumberTrait, SyncClientTrait, SyncClientTrait;
    use IsMonitored;

    public $clientEventData;

    public $is_many_request;

    /**
     * Create a new job instance.
     */
    public function __construct($clientEvent, ?bool $is_many_request = false)
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
            /**
             * there are variables from https://docs.google.com/spreadsheets/d/1xam159C7dirHCH9txq1g9xp98mDbktCBvg_clc4hgxI/edit?gid=2000315887#gid=2000315887 `Client Events`
             *
             * event name
             * date: when client join the event
             * audience: what is the audience (parent, student, teacher)
             * name: primary name
             * email: primary email
             * phone number: primary phone
             * child or parent name: secondary name
             * child or parent email: secondary email
             * child or parent phone number: secondary phone
             * registration type: PR (PR for Pre-Registration) / OTS (OTS for On the Spot)
             * school: child's school
             * class of: meaning child's graduation year
             * lead
             * partner: mandatory if lead is partner
             * edufair: mandatory if lead is edufair
             * kol: mandatory if lead is kol
             * status: 0 mean join, 1 mean attend
             */
            $status = $val['Status'] == 'Join' ? 0 : 1;

            // 1. Process the school. If the school does not exists in the database, then create a new one.
            if (! $school = School::where('sch_name', $val['School'])->first()) {
                $school = $this->createSchoolIfNotExists($val['School'], true);
            }

            // 2. Determine the secondary role based on what the audience is.
            switch ($val['Audience']) {
                case 'Student':
                    // if the audience is a student then the secondary data will be a parent
                    $roleSub = 'Parent';
                    break;
                case 'Parent':
                    // if the audience is a parent then the secondary data will be a student
                    $roleSub = 'Student';
                    break;
                case 'Teacher/Counselor':
                    // if the audience is teacher, there should not be a secondary data
                    $roleSub = null;
                    break;
            }

            // 3. Create a main client using GoogleSheetController::createClient
            $created_main_client_id = app(GoogleSheetController::class)->createClient($val, 'main', $val['Audience'], $val['Itended Major'], $val['Destination Country'], $school);

            // 4. Retrieve main_client data using created_main_client_id
            $main_client = UserClient::withTrashed()->where('id', $created_main_client_id)->first();

            // 5. Create a secondary client if the audience is Student or Parent and the `child or parent name` column is filled
            $created_sub_client_id = ($val['Audience'] == 'Student' || $val['Audience'] == 'Parent') && isset($val['Child or Parent Name']) ? app(GoogleSheetController::class)->createClient($val, 'sub', $roleSub, $val['Itended Major'], $val['Destination Country'], $school, $main_client) : null;

            // 6. Retrieve sub_client data using created_sub_client_id
            $sub_client = UserClient::withTrashed()->where('id', $created_sub_client_id)->first();

            // 7. The default variable of main client would be a student.
            $student_fullname = $val['Name'];
            $child_name = $this->split($student_fullname);

            // 8. Create relation parent and student
            $relation = [];
            if (in_array($val['Audience'], ['Parent', 'Student']) && $created_sub_client_id !== null) {
                $relation = null;
                switch ($val['Audience']) {
                    case 'Parent':
                        $parent = $main_client;
                        $student = $sub_client;
                        $relation = $this->checkExistClientRelation('parent', $parent, $student->full_name);
                        if (! $relation['isExist']) {
                            $parent->childrens()->attach($student->id);
                        }

                        // Overwrite $student_fullname with value from `child or parent name`
                        $student_fullname = isset($val['Child or Parent Name']) ? $val['Child or Parent Name'] : null;

                        if ($student_fullname !== null) {
                            $child_name = $this->split($student_fullname);
                        }

                        break;

                    case 'Student':
                        $parent = $sub_client;
                        $student = $main_client;
                        $relation = $this->checkExistClientRelation('parent', $parent, $student->full_name);
                        if (! $relation['isExist']) {
                            $parent->childrens()->attach($created_main_client_id);
                        }

                        break;
                }
            } else {
                $student = $main_client;
                $child_name = [
                    'first_name' => $student->first_name,
                    'last_name' => $student->last_name,
                ];
            }

            // Insert client event
            $data = [
                'event_id' => $val['Event Name'],
                'joined_date' => isset($val['Date']) ? $val['Date'] : null,
                'client_id' => $created_main_client_id,
                'lead_id' => $val['Lead'],
                'status' => $status,
                'registration_type' => isset($val['Registration Type']) ? $val['Registration Type'] : null,
                'number_of_attend' => isset($val['Number Of Attend']) ? $val['Number Of Attend'] : 1,
                'referral_code' => isset($val['Referral Code']) ? $val['Referral Code'] : null,
                'is_many_request' => true,
            ];

            // Generate ticket id (if event offline)
            // $event = Event::where('event_id', $val['Event Name'])->first();
            // Updated ticket id for all events
            // if(!str_contains($event->event_location, 'online')){
            $data['ticket_id'] = app(ExtClientController::class)->generateTicketID();
            // }

            // add additional identification
            if ($val['Audience'] == 'Parent') {
                $parentIds[] = $created_main_client_id;

                if (isset($created_sub_client_id)) {
                    $data['child_id'] = $created_sub_client_id;
                    $childIds[] = $created_sub_client_id;
                }

            } elseif ($val['Audience'] == 'Student') {
                $childIds[] = $created_main_client_id;

                if (isset($created_sub_client_id)) {
                    $data['parent_id'] = $created_sub_client_id;
                    $parentIds[] = $created_sub_client_id;
                }
            } else {
                $teacherIds[] = $created_main_client_id;
            }

            $existClientEvent = ClientEvent::where('event_id', $data['event_id'])
                ->where('client_id', $created_main_client_id)
                // ->where('joined_date', $data['joined_date'])
                ->first();

            if (! isset($existClientEvent)) {
                $insertedClientEvent = ClientEvent::create($data);

                // add to log client event
                // to trigger the cron for send the qr email
                // ClientEventLogMail::create([
                //     'clientevent_id' => $insertedClientEvent->clientevent_id,
                //     'event_id' => $val['Event Name'],
                //     'sent_status' => 0,
                //     'category' => 'qrcode-mail'
                // ]);

            }

            $logDetails[] = [
                'clientevent_id' => isset($insertedClientEvent->clientevent_id) ? $insertedClientEvent->clientevent_id : null,
            ];

            $imported_date[] = [Carbon::now()->format('d-m-Y H:i:s')];

            $childs_data_for_log_client[$key] = [
                'client_id' => $student->id,
                'first_name' => count($relation) > 0 && $relation['isExist'] ? $student->first_name : $child_name['first_name'],
                'last_name' => count($relation) > 0 && $relation['isExist'] ? $student->last_name : $child_name['last_name'],
                'lead_source' => $val['Lead'],
                'inputted_from' => 'import-client-event',
                'clientprog_id' => null,
            ];
        }

        // trigger to verifying client
        // count($childIds) > 0 ? ProcessVerifyClient::dispatch($childIds, true)->onQueue('verifying-client') : null;
        // count($parentIds) > 0 ? ProcessVerifyClientParent::dispatch($parentIds, true)->onQueue('verifying-client-parent') : null;
        // count($teacherIds) > 0 ? ProcessVerifyClientTeacher::dispatch($teacherIds, true)->onQueue('verifying-client-teacher') : null;

        // trigger to insert log children
        count($childIds) > 0 ? ProcessInsertLogClient::dispatch($childs_data_for_log_client, true)->onQueue('insert-log-client') : null;

        Sheets::spreadsheet(env('GOOGLE_SHEET_KEY_IMPORT'))->sheet(env('APP_ENV') == 'local' ? 'test client event' : 'Client Events')->range('Z'.$this->clientEventData->first()['No'] + 1)->update($imported_date);
        $dataJobBatches = JobBatches::find($this->batch()->id);

        $logDetailsCollection = Collect($logDetails);
        $logDetailsMerge = $logDetailsCollection->merge(json_decode($dataJobBatches->log_details));
        JobBatches::where('id', $this->batch()->id)->update(['total_imported' => $dataJobBatches->total_imported + count($imported_date), 'log_details' => json_encode($logDetailsMerge), 'type' => 'client-event', 'category' => 'Import']);

    }
}
