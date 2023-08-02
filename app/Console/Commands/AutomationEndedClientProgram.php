<?php

namespace App\Console\Commands;

use App\Interfaces\ClientProgramRepositoryInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AutomationEndedClientProgram extends Command
{

    private ClientProgramRepositoryInterface $clientProgramRepository;

    public function __construct(ClientProgramRepositoryInterface $clientProgramRepository)
    {
        parent::__construct();
        $this->clientProgramRepository = $clientProgramRepository;
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'automation:ended_client_program';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically ended the client program after passing the program end date';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $clientprograms = $this->clientProgramRepository->getActiveClientProgramAfterProgramEnd();
        $progressBar = $this->output->createProgressBar($clientprograms->count());
        $progressBar->start();
        
        DB::beginTransaction();
        try {

            foreach ($clientprograms as $clientprogram)
            {
                $clientprog_id = $clientprogram->clientprog_id;
                $newDetails = [
                    'prog_running_status' => 2
                ];

                $this->clientProgramRepository->endedClientProgram($clientprog_id, $newDetails);
                $progressBar->advance();
            }
            
            $progressBar->finish();
            DB::commit();
        
        } catch (Exception $e) {

            $this->info($e->getMessage());
            DB::rollBack();

        }

        return Command::SUCCESS;
    }
}
