<?php
 
namespace App\Jobs\GoogleSheet;

use App\Http\Controllers\Api\v1\ExtClientController;
use App\Http\Controllers\GoogleSheetController;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\LoggingTrait;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Http\Traits\SyncClientTrait;
use App\Jobs\Client\ProcessDefineCategory;
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
use App\Models\ViewClientRefCode;
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

class ExportClientProgram implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use SyncClientTrait, CreateCustomPrimaryKeyTrait, LoggingTrait, SyncClientTrait, StandardizePhoneNumberTrait;
    use IsMonitored;

    public $clientProgData;
    public $type;
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

        $data = [];

        $clientProgs = $this->clientProgData;

        $dataJobBatches = JobBatches::find($this->batch()->id);

        if($dataJobBatches->total_imported == 0){
            $i = 0;
        }else{
            $i = $dataJobBatches->total_imported - 1;
        }

        foreach ($clientProgs as $clientProg) {
            $customClientProgId = 'CP-' . $clientProg->clientprog_id;

            # Set referral name
            $referral_name = '';
            if (isset($clientProg->referral_code)){
                $referral = ViewClientRefCode::where('id', (int) filter_var($clientProg->referral_code, FILTER_SANITIZE_NUMBER_INT))->first();
                $referral_name = isset($referral) ? $referral->full_name : '';

            }

            # Set status
            $status = '';
            if(isset($clientProg->status)){
                switch ($clientProg->status) {
                    case 0:
                        $status = 'Pending';
                        break;
                        
                    case 1:
                        $status = 'Success';
                        break;

                    case 2:
                        $status = 'Failed';
                        break;

                    case 3:
                        $status = 'Refund';
                        break;
                }
            }

            # Set prog running status
            $prog_running_status = '';
            if(isset($clientProg->prog_running_status)){
                switch ($clientProg->prog_running_status) {
                    case 0:
                        $prog_running_status = 'Not Yet';
                        break;
                        
                    case 1:
                        $prog_running_status = 'Ongoing';
                        break;

                    case 2:
                        $prog_running_status = 'Done';
                        break;
                }
            }

            $data[] = [
                $customClientProgId, 
                $this->replaceNullValue($clientProg['fullname']),
                $this->replaceNullValue($clientProg['student_mail']),
                $this->replaceNullValue($clientProg['student_phone']),
                $this->replaceNullValue($clientProg['school_name']),
                $this->replaceNullValue($clientProg['grade_now']),
                $this->replaceNullValue($clientProg['program_names']),
                $this->replaceNullValue($clientProg['register_as']),
                $this->replaceNullValue($clientProg['parent_fullname']),
                $this->replaceNullValue($clientProg['parent_phone']),
                $this->replaceNullValue($clientProg['parent_mail']),
                $this->replaceNullValue($clientProg['mentor_tutor_name']),
                $this->replaceNullValue($clientProg['prog_end_date']),
                $this->replaceNullValue($clientProg['lead_source']),
                $this->replaceNullValue($clientProg['conversion_lead']),
                $referral_name,
                $this->replaceNullValue(strip_tags($clientProg['notes'])),
                $status,
                $prog_running_status,
                $this->replaceNullValue($clientProg['reason_name']),
                $this->replaceNullValue($clientProg['pic_name']),
                $this->replaceNullValue($clientProg['initconsult_date']) != '-' ? date('M d, Y', strtotime($clientProg['initconsult_date'])) : '-',
                $this->replaceNullValue($clientProg['assessmentsent_date']) != '-' ? date('M d, Y', strtotime($clientProg['assessmentsent_date'])) : '-',
                $this->replaceNullValue($clientProg['first_discuss_date']) != '-' ? date('M d, Y', strtotime($clientProg['first_discuss_date'])) : '-',
                $this->replaceNullValue($clientProg['failed_date']) != '-' ? date('M d, Y', strtotime($clientProg['failed_date'])) : '-',
                $this->replaceNullValue($clientProg['success_date']) != '-' ? date('M d, Y', strtotime($clientProg['success_date'])) : '-',
                $this->replaceNullValue($clientProg['created_at']) != '-' ? date('M d, Y', strtotime($clientProg['created_at'])) : '-',
            ];
            $i++;
        }


        if($dataJobBatches->total_imported == 0){
            $index = 2;
        }else{
            $index = $dataJobBatches->total_imported + 2;
        }
        Sheets::spreadsheet(env('GOOGLE_SHEET_KEY_EXPORT_DATA'))->sheet('Client Programs')->range('A'. $index)->update($data);
        JobBatches::where('id', $this->batch()->id)->update(['total_imported' => $dataJobBatches->total_imported + count($data), 'category' => 'Export', 'type' => 'client-program']); 

    }
    
 
    private function replaceNullValue($value)
    {
        if(isset($value)){
            return $value;
        }else{
            return '-';
        }
    }
}