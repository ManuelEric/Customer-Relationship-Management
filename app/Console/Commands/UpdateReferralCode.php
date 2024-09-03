<?php

namespace App\Console\Commands;

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

class UpdateReferralCode extends Command
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

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $clientsUnclean = UserClientUnclean::all();
        $progressBar = $this->output->createProgressBar($clientsUnclean->count());
        $progressBar->start();

        DB::beginTransaction();
        try {

            $clientsUncleanWithRefCode = UserClientUnclean::where('referral_code','!=', null)->get();
            $clientProgramsUncleanWithRefCode = ClientProgramUnclean::where('referral_code','!=', null)->get();
            $clientEventsUncleanWithRefCode = ClientEventUnclean::where('referral_code','!=', null)->get();

            $this->changeReferralCodetoSecondaryId('client', $clientsUncleanWithRefCode, $clientsUnclean);
            $this->changeReferralCodetoSecondaryId('client_prog', $clientProgramsUncleanWithRefCode, $clientsUnclean);
            $this->changeReferralCodetoSecondaryId('client_event', $clientEventsUncleanWithRefCode, $clientsUnclean);

            DB::commit();
            $progressBar->finish();
        } catch (Exception $e) {

            DB::rollBack();
            Log::info('Failed to update referral code : ' . $e->getMessage() . ' on line ' . $e->getLine());
        }

        return Command::SUCCESS;
    }

    protected function changeReferralCodetoSecondaryId($type, $uncleanDataWithRefCode, $clientsUnclean){
        foreach ($uncleanDataWithRefCode as $uncleanWithRefCode) {                
            $getReferralClient = $clientsUnclean->where('id', substr($uncleanWithRefCode->referral_code,3))->first();
            
            if(isset($getReferralClient)){

                $newReferralCode = UserClient::withTrashed()->where('id', $getReferralClient->uuid)->first();
                switch ($type) {
                    case 'client':
                        $mainTable = UserClient::withTrashed()->where('id', $uncleanWithRefCode->uuid)->first();
                        break;
                    case 'client_prog':
                        $mainTable = ClientProgram::where('clientprog_id', $uncleanWithRefCode->clientprog_id)->first();
                        break;
                    case 'client_event':
                        $mainTable = ClientEvent::where('clientevent_id', $uncleanWithRefCode->clientevent_id)->first();
                        break;
                }

                if($newReferralCode != null && $mainTable != null){
                    $mainTable->update(['referral_code' => $newReferralCode->secondary_id]);
                }
            }

        }
    }
}
