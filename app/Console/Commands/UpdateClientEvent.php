<?php

namespace App\Console\Commands;

use App\Interfaces\LeadTargetRepositoryInterface;
use App\Models\LeadTargetTracking;
use App\Repositories\ClientEventRepository;
use App\Repositories\ClientRepository;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateClientEvent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:client_event';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Switch client to parent client event';

    private ClientEventRepository $clientEventRepository;
    private ClientRepository $clientRepository;

    public function __construct(ClientEventRepository $clientEventRepository, ClientRepository $clientRepository)
    {
        parent::__construct();
        $this->clientEventRepository = $clientEventRepository;
        $this->clientRepository = $clientRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        # info if the cron working or not
        Log::info('Cron Update Client Event works fine.');


        DB::beginTransaction();
        try {

            $clientEvents = $this->clientEventRepository->getAllClientEvents();

            foreach($clientEvents as $clientEvent){
                if($clientEvent->client->register_as == 'parent'){

                    if($clientEvent->client->parents->count() > 0){
                        $clientEventDetails = [
                            'client_id' => $clientEvent->client->parents[0]->id,
                            'child_id' => $clientEvent->client_id
                        ];
                    }else if($clientEvent->client->childrens->count() > 0){
                        $clientEventDetails = [
                            'client_id' => $clientEvent->client_id,
                            'child_id' => $clientEvent->client->childrens[0]->id
                        ];
                    }

                    $updateClientEvent = $this->clientEventRepository->updateClientEvent($clientEvent->clientevent_id, $clientEventDetails);
                    if($updateClientEvent){
                        $this->info('Update client event success');
                    }

                    $parentDetails = [
                        'register_as' => 'parent'
                    ];

                    $this->clientRepository->updateClient($clientEventDetails['client_id'], $parentDetails);
                }
            }

            DB::commit();

        } catch (Exception $e) {
            
            DB::rollBack();
            Log::info('Cron Insert target tracking not working normal. Error : '. $e->getMessage() .' | Line '. $e->getCode());

        }

        return Command::SUCCESS;
    }
}

