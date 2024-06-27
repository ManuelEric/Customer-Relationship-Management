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

        foreach ($clients as $client) {

            # Get follow up status
            if (!$latestId = $client->followupSchedule()->max('id'))
                    $followup_status = '-';
                
                $status = $client->followupSchedule()->where('id', $latestId)->first()->status;

                switch ($status) {
                    case 0:
                        $followup_status = 'Currently following up';
                        break;
                    case 1:
                        $followup_status = 'Awaiting response';
                        break;
                }


            $data[] = [
                $client->status_lead_score, 
                $client->full_name,
                $followup_status,
                $client->interest_prog,
                $client->program_suggest,
                $client->status_lead,
                $client->pic_name,
                $client->mail,
                $client->phone,
                $client->parent_name,
                $client->parent_mail,
                $client->parent_phone,
                $client->school_name,
                $client->graduation_year_real,
                $client->grade_now,
                $client->insta,
                $client->state,
                $client->city,
                $client->address,
                $client->lead_source,
                $client->referral_name,
                $client->st_levelinterest,
                $client->st_abryear,
                $client->abr_country,
                $client->dream_uni,
                $client->dream_major,
                $client->scholarship,
                $client->took_ia == 0 ? 'Not yet' : 'Filled In',
                $client->created_at,
                $client->updated_at,                
            ];
        }


        $sheetName = '';
        switch ($this->type) {
            case 'new-leads':
                $sheetName = 'New Leads';
                break;
            case 'potentials':
                $sheetName = 'Potentials';
                break;
            
            default:
                # code...
                break;
        }
        $dataJobBatches = JobBatches::find($this->batch()->id);

        if($dataJobBatches->total_imported == 0){
            $index = 2;
        }else{
            $index = $dataJobBatches->total_imported + 2;
        }
        Sheets::spreadsheet(env('GOOGLE_SHEET_KEY_EXPORT_DATA'))->sheet($sheetName)->range('A'. $index)->update($data);
        JobBatches::where('id', $this->batch()->id)->update(['total_imported' => $dataJobBatches->total_imported + count($data)]); 

    }
   
}