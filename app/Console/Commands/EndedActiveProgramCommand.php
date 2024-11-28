<?php

namespace App\Console\Commands;

use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\ReasonRepositoryInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EndedActiveProgramCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ended:client_program';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ended all client program when graduation year less than or equal to the current year';

    private ClientRepositoryInterface $clientRepository;
    private ClientProgramRepositoryInterface $clientProgramRepository;
    private ReasonRepositoryInterface $reasonRepository;

    public function __construct(ClientRepositoryInterface $clientRepository, ClientProgramRepositoryInterface $clientProgramRepository, ReasonRepositoryInterface $reasonRepository)
    {
        parent::__construct();
        $this->clientRepository = $clientRepository;
        $this->clientProgramRepository = $clientProgramRepository;
        $this->reasonRepository = $reasonRepository;
    }
    
    # Purpose 
    # Get all data client 
    # Update prog_running_status client program to 2 (done) when success program has ended
    # Update status client program to 2 (failed) when created at < current year
    public function handle()
    {
        $ids = $this->clientRepository->getAllClients(['id'])->pluck('id')->toArray();

        $progress_bar = $this->output->createProgressBar(count($ids));
        if (count($ids) == 0 ) {
            Log::notice('No client existing mentee found');
            return Command::SUCCESS;
        }

        DB::beginTransaction();
        foreach ($ids as $key => $value) {

            try {

                if (!$client_program = $this->clientProgramRepository->getClientProgramByClientId($value))
                    continue;
    
                # get client program with status success and prog_end_date < today
                $active_client_program = $client_program->where('status', 1 /* success */)->where('prog_end_date', '<', date('Y-m-d'))->where('prog_running_status', '!=', 2)->pluck('clientprog_id')->toArray();
                # get client program with status pending and created_at < current year
                $pending_client_program = $client_program->where('status', 0)->where('created_at', '<', date('Y').'-01-01 00:00:00')->pluck('clientprog_id')->toArray();

                # update the active client program to done
                $this->clientProgramRepository->endedClientPrograms($active_client_program, ['prog_running_status' => 2 /* done */]);
                
                ##
                # find the reason in order to update to failed client program
                $reason_id = $this->reasonRepository->getReasonByReasonName('Ended by system for the reason that clients has been graduated')->reason_id;
                # update the pending client program to failed
                $this->clientProgramRepository->endedClientPrograms($pending_client_program, ['status' => 2 /* failed */, 'reason_id' => $reason_id]);

                DB::commit();

            } catch (Exception $e) {

                DB::rollBack();
                Log::error('Failed to ended the existing mentee\'s client program. Error : '.$e->getMessage(). ' | Line '.$e->getLine());
                continue;

            }

            $progress_bar->advance();
        }

        
        $progress_bar->finish();

        return Command::SUCCESS;
    }
}
