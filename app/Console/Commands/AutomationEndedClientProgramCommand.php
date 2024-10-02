<?php

namespace App\Console\Commands;

use App\Interfaces\ClientProgramRepositoryInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutomationEndedClientProgramCommand extends Command
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
    protected $signature = 'automate:ended_client_program';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically ended the running client program after passing the program end date';

    # Purpose:
    # This function getting data active client program when program has ended.
    # update prog running status to 2 (failed) from tbl client prog
    public function handle()
    {
        $client_programs = $this->clientProgramRepository->getActiveClientProgramAfterProgramEnd();
        $progress_bar = $this->output->createProgressBar($client_programs->count());
        $progress_bar->start();
        
        DB::beginTransaction();
        try {

            foreach ($client_programs as $client_program)
            {
                $client_prog_id = $client_program->client_prog_id;
                $new_details = [
                    'prog_running_status' => 2
                ];

                $this->clientProgramRepository->endedClientProgram($client_prog_id, $new_details);
                $progress_bar->advance();
            }
            
            $progress_bar->finish();
            DB::commit();
        
        } catch (Exception $e) {

            Log::error('Automation Ended Client Program Failed: '. $e->getMessage() . ' OnLine: ' . $e->getLine());
           
            $this->info($e->getMessage());
            DB::rollBack();
        }

        return Command::SUCCESS;
    }
}
