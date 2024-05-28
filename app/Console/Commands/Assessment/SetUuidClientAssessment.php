<?php

namespace App\Console\Commands\Assessment;

use App\Interfaces\ClientRepositoryInterface;
use App\Models\ClientEvent;
use App\Models\Assessment\UserClientAssessment;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SetUuidClientAssessment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set:uuid_assessment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically set UUID CRM to Assessment';


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
        $clientAssessment = UserClientAssessment::where('uuid_crm', null)->get();
        $ticketIds = [];

        foreach ($clientAssessment as $cm) {
            $ticketIds[] = $cm->ticket_id;
        }

        
        $clientEvents = ClientEvent::with(['client'])->whereIn('ticket_id', $ticketIds)->get();
        
        $progressBar = $this->output->createProgressBar($clientEvents->count());
        $progressBar->start();

        DB::beginTransaction();
        try {

            foreach ($clientEvents as $clientEvent) {
                $progressBar->advance();

                $child = null;

                 if ($clientEvent->child_id === NULL && !$clientEvent->client->roles()->whereIn('role_name', ['student'])->exists())
                    continue;
                

                # when the client that joined clientEvent, registering a children as well
                # then get the children info
                if ($clientEvent->child_id !== NULL)
                    $child = $clientEvent->children;


                # when the client that joined clientEvent, is already a student
                if ($clientEvent->client->roles()->whereIn('role_name', ['student'])->exists())
                    $child = $clientEvent->client;

                Log::debug(json_encode($child));

                if (isset($child))
                    UserClientAssessment::where('ticket_id', $clientEvent->ticket_id)->update(['uuid_crm' => $child->uuid]);
                
                $progressBar->advance();
            }
            DB::commit();
            $progressBar->finish();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Failed to set uuid crm : ' . $e->getMessage() . ' on line ' . $e->getLine());
        }

        return Command::SUCCESS;
    }
}
