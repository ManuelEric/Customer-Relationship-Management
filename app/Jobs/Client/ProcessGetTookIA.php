<?php
 
namespace App\Jobs\Client;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\LoggingTrait;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Http\Traits\SyncClientTrait;
use App\Jobs\RawClient\ProcessVerifyClient;
use App\Jobs\RawClient\ProcessVerifyClientParent;
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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Revolution\Google\Sheets\Facades\Sheets;

class ProcessGetTookIA implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use SyncClientTrait, CreateCustomPrimaryKeyTrait, LoggingTrait, SyncClientTrait, StandardizePhoneNumberTrait;

    public $clientData;
    /**
     * Create a new job instance.
     */
    public function __construct($client)
    {
        $this->clientData = $client;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
    
        $response = Http::post(env('EDUALL_ASSESSMENT_URL') . 'api/get/took-ia-bulk', ['uuid_crm' => $this->clientData]);

        $clientUUIDS = [];

        if ($response->failed()){
            Log::warning('Fetch took ia failed');
            return;
        }

        if ($response->ok()){
            if(count($response['data']) > 0){
                foreach ($response['data'] as $val) {
                    $clientUUIDS[] = $val['uuid'];
                    $userClient = UserClient::where('uuid', $val['uuid']);
                    $userClient->timestamp = false;
                    $userClient->update(['took_ia' => $val['took_ia']]);
                    $userClient->timestamp = true;
                }
            }

            Log::info('Successfully updated took IA');
        }
       

    }
   
}