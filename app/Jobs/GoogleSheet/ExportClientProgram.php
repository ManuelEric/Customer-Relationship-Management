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

class ExportClientProgram implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use SyncClientTrait, CreateCustomPrimaryKeyTrait, LoggingTrait, SyncClientTrait, StandardizePhoneNumberTrait;

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
            $i == 1 ? Log::debug($clientProg) : null;
            $customClientProgId = 'CP-' . $clientProg->clientprog_id;
            $referral_name = '';
            if (isset($clientProg->referral_code)){
                $referral = ViewClientRefCode::where('id', (int) filter_var($clientProg->referral_code, FILTER_SANITIZE_NUMBER_INT))->first();
                $referral_name = isset($referral) ? $referral->full_name : '';

            }

            $data[] = [
                $customClientProgId, 
                $this->replaceNullValue($clientProg['fullname']),
                $this->replaceNullValue($clientProg['student_mail']),
                $this->replaceNullValue($clientProg['student_phone']),
                $this->replaceNullValue($clientProg['school_name']),
                $this->replaceNullValue($clientProg['grade_now']),
                $this->replaceNullValue($clientProg['program_name']),
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
                $this->replaceNullValue($clientProg['status']),
                $this->replaceNullValue($clientProg['prog_running_status']),
                $this->replaceNullValue($clientProg['reason_name']),
                $this->replaceNullValue($clientProg['pic_name']),
                $this->replaceNullValue(date('M d, Y', strtotime($clientProg['initconsult_date']))),
                $this->replaceNullValue(date('M d, Y', strtotime($clientProg['assessmentsent_date']))),
                $this->replaceNullValue(date('M d, Y', strtotime($clientProg['first_discuss_date']))),
                $this->replaceNullValue(date('M d, Y', strtotime($clientProg['failed_date']))),
                $this->replaceNullValue(date('M d, Y', strtotime($clientProg['success_date']))),
                $this->replaceNullValue(date('M d, Y', strtotime($clientProg['created_at']))),
            ];
            $i++;
        }


        if($dataJobBatches->total_imported == 0){
            $index = 2;
        }else{
            $index = $dataJobBatches->total_imported + 2;
        }
        Sheets::spreadsheet(env('GOOGLE_SHEET_KEY_EXPORT_DATA'))->sheet('Client Programs')->range('A'. $index)->update($data);
        JobBatches::where('id', $this->batch()->id)->update(['total_imported' => $dataJobBatches->total_imported + count($data)]); 

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