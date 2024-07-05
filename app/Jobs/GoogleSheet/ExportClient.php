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

class ExportClient implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use SyncClientTrait, CreateCustomPrimaryKeyTrait, LoggingTrait, SyncClientTrait, StandardizePhoneNumberTrait;

    public $clientData;
    public $type;
    /**
     * Create a new job instance.
     */
    public function __construct($client, $type)
    {
        $this->clientData = $client;
        $this->type = $type;
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

        $childIds = $parentIds = $teacherIds = $data = [];

        $clients = $this->clientData;

        $dataJobBatches = JobBatches::find($this->batch()->id);

        if($dataJobBatches->total_imported == 0){
            $i = 0;
        }else{
            $i = $dataJobBatches->total_imported - 1;
        }
        foreach ($clients as $client) {

            # Get follow up status
            if ($latestId = $client->followupSchedule()->max('id')){
                $status = $client->followupSchedule()->where('id', $latestId)->first()->status;
    
                switch ($status) {
                    case 0:
                        $followup_status = 'Currently following up';
                        break;
                    case 1:
                        $followup_status = 'Awaiting response';
                        break;
                }
            }else{
                $followup_status = '-';
            }
            
 
            $data[] = [
                $this->replaceNullValue($client->status_lead_score), 
                $this->replaceNullValue($client->full_name),
                $this->replaceNullValue($followup_status),
                $this->replaceNullValue($client->interest_prog),
                $this->replaceNullValue($client->program_suggest),
                $this->replaceNullValue($client->status_lead),
                $this->replaceNullValue($client->pic_name),
                $this->replaceNullValue($client->mail),
                $this->replaceNullValue($client->phone),
                $this->replaceNullValue($client->parent_name),
                $this->replaceNullValue($client->parent_mail),
                $this->replaceNullValue($client->parent_phone),
                $this->replaceNullValue($client->school_name),
                $this->replaceNullValue($client->graduation_year_real),
                $this->replaceNullValue($client->grade_now),
                $this->replaceNullValue($client->insta),
                $this->replaceNullValue($client->state),
                $this->replaceNullValue($client->city),
                $this->replaceNullValue(strip_tags($client->address)),
                $this->replaceNullValue($client->lead_source),
                $this->replaceNullValue($client->referral_name),
                $this->replaceNullValue($client->st_levelinterest),
                $this->replaceNullValue($client->joined_event),
                $this->replaceNullValue($client->st_abryear),
                $this->replaceNullValue($client->abr_country),
                $this->replaceNullValue($client->dream_uni),
                $this->replaceNullValue($client->dream_major),
                $this->replaceNullValue($client->scholarship),
                $this->replaceNullValue($client->took_ia == 0 ? 'Not yet' : 'Filled In'),
                $this->replaceNullValue(date('M d, Y', strtotime($client->created_at))),
                $this->replaceNullValue(date('M d, Y', strtotime($client->updated_at))),
            ];
            $i++;
        }

        $sheetName = '';
        switch ($this->type) {
            case 'new-leads':
                $sheetName = 'New Leads';
                break;
            case 'potential':
                $sheetName = 'Potentials';
                break;
            case 'mentee':
                $sheetName = 'Mentees';
                break;
            case 'non-mentee':
                $sheetName = 'Non-Mentees';
                break;
            case 'all':
                $sheetName = 'All';
                break;
            case 'inactive':
                $sheetName = 'Inactive';
                break;
        }

        if($dataJobBatches->total_imported == 0){
            $index = 2;
        }else{
            $index = $dataJobBatches->total_imported + 2;
        }
        Sheets::spreadsheet(env('GOOGLE_SHEET_KEY_EXPORT_DATA'))->sheet($sheetName)->range('A'. $index)->update($data);
        JobBatches::where('id', $this->batch()->id)->update(['total_imported' => $dataJobBatches->total_imported + count($data), 'category' => 'Export', 'type' => 'Student']); 

    }
    
 
    private function replaceNullValue($value)
    {
        if($value == null){
            return '-';
        }else{
            return $value;
        }
    }
}