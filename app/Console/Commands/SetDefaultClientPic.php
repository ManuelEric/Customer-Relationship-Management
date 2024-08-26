<?php

namespace App\Console\Commands;

use App\Interfaces\ClientRepositoryInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SetDefaultClientPic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'automate:set_default_client_pic';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'A function to set old client that doesn\'t have a PIC, using his/her program PIC';

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
        Log::info('Cron set default client pic works fine');
        $clients = $this->clientRepository->getClientWithNoPicAndHaveProgram();
        $progressBar = $this->output->createProgressBar($clients->count());
        $progressBar->start();

        DB::beginTransaction();
        try {

            foreach ($clients as $client) {
                $progressBar->advance();
    
                $clientId = $client->id;

                # only take the first client program 
                $clientProgram = $client->clientProgram[0];
                $latest_program_pic = $clientProgram->internalPic()->first()->id;
    
                $this->clientRepository->updateClient($clientId, ['pic' => $latest_program_pic]);
                
                Log::debug('Client Id: ' .$clientId .' has pic: ' . $latest_program_pic );
            }
            DB::commit();

        } catch (Exception $e) {
            
            DB::rollBack();
            $err_messages = 'Failed to set default pic for client id: '.$clientId.' | error: '.$e->getMessage(). ' on line '.$e->getLine();
            Log::error($err_messages);
            $this->error($err_messages);
            return Command::FAILURE;
            
        }

        $progressBar->finish();

        return Command::SUCCESS;
    }
}
