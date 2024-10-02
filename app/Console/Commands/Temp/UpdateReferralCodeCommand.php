<?php

namespace App\Console\Commands\Temp;

use App\Interfaces\ClientRepositoryInterface;
use App\Models\ClientEvent;
use App\Models\ClientProgram;
use App\Models\UserClient;
use App\Models\Unclean\ClientProgram as ClientProgramUnclean;
use App\Models\Unclean\ClientEvent as ClientEventUnclean;
use App\Models\Unclean\UserClient as UserClientUnclean;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateReferralCodeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:referral_code';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically update referral from 3 digit name plus id to secondary_id';


    private ClientRepositoryInterface $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepository)
    {
        parent::__construct();
        $this->clientRepository = $clientRepository;
    }

    # ===== NOTE =====
    # this cron only running after up production crm adjustment
    # after that this cron will be removed
    
    # Purpose:
    # Update old referral code from 4 digit id + 3 letter first name to secondary_id
    public function handle()
    {
        $clients_unclean = UserClientUnclean::all();
        $progress_bar = $this->output->createProgressBar($clients_unclean->count());
        $progress_bar->start();

        DB::beginTransaction();
        try {

            $clients_unclean_with_refcode = UserClientUnclean::where('referral_code','!=', null)->get();
            $client_programs_unclean_with_refcode = ClientProgramUnclean::where('referral_code','!=', null)->get();
            $client_events_unclean_with_refcode = ClientEventUnclean::where('referral_code','!=', null)->get();

            $this->cnChangeReferralCodetoSecondaryId('client', $clients_unclean_with_refcode, $clients_unclean);
            $this->cnChangeReferralCodetoSecondaryId('client_prog', $client_programs_unclean_with_refcode, $clients_unclean);
            $this->cnChangeReferralCodetoSecondaryId('client_event', $client_events_unclean_with_refcode, $clients_unclean);

            DB::commit();
            $progress_bar->finish();
        } catch (Exception $e) {

            DB::rollBack();
            Log::info('Failed to update referral code : ' . $e->getMessage() . ' on line ' . $e->getLine());
        }

        return Command::SUCCESS;
    }

    protected function cnChangeReferralCodetoSecondaryId($type, $unclean_data_with_refcode, $clients_unclean){
        foreach ($unclean_data_with_refcode as $unclean_with_refcode) {                
            $get_referral_client = $clients_unclean->where('id', substr($unclean_with_refcode->referral_code,3))->first();
            
            if(isset($get_referral_client)){

                $new_referralcode = UserClient::withTrashed()->where('id', $get_referral_client->uuid)->first();
                switch ($type) {
                    case 'client':
                        $main_table = UserClient::withTrashed()->where('id', $unclean_with_refcode->uuid)->first();
                        break;
                    case 'client_prog':
                        $main_table = ClientProgram::where('clientprog_id', $unclean_with_refcode->clientprog_id)->first();
                        break;
                    case 'client_event':
                        $main_table = ClientEvent::where('clientevent_id', $unclean_with_refcode->clientevent_id)->first();
                        break;
                }

                if($new_referralcode != null && $main_table != null){
                    $main_table->update(['referral_code' => $new_referralcode->secondary_id]);
                }
            }

        }
    }
}
