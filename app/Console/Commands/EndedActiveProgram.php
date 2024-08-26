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

class EndedActiveProgram extends Command
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
    
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $ids = $this->clientRepository->getAllClients(['id'])->pluck('id')->toArray();

        # add condition
        // $ids = $existing->where('graduation_year_real', '<=', Carbon::now()->format('Y'))->pluck('id')->toArray();

        $progressBar = $this->output->createProgressBar(count($ids));
        if (count($ids) == 0 ) {
            Log::notice('No client existing mentee found');
            return Command::SUCCESS;
        }

        DB::beginTransaction();
        foreach ($ids as $key => $value) {

            try {

                if (!$clientProgram = $this->clientProgramRepository->getClientProgramByClientId($value))
                    continue;
    
                // $activeClientProgram = $clientProgram->where('status', 1 /* success */)->whereIn('prog_running_status', [0 /*not yet */, 1 /* ongoing */])->pluck('clientprog_id')->toArray();
                // $activeClientProgram = $clientProgram->where('status', 1 /* success */)->where('prog_end_date', '<', date('Y-m-d'))->where('prog_running_status', '!=', 2)->pluck('clientprog_id')->toArray();
                $activeClientProgram = $clientProgram->where('status', 1 /* success */)->where('prog_end_date', '<', date('Y-m-d'))->where('prog_running_status', '!=', 2)->pluck('clientprog_id')->toArray();
                $pendingClientProgram = $clientProgram->where('status', 0)->where('created_at', '<', date('Y').'-01-01 00:00:00')->pluck('clientprog_id')->toArray();

                # update the active client program to done
                $this->clientProgramRepository->endedClientPrograms($activeClientProgram, ['prog_running_status' => 2 /* done */]);
                
                ##
                # find the reason in order to update to failed client program
                $reasonId = $this->reasonRepository->getReasonByReasonName('Ended by system for the reason that clients has been graduated')->reason_id;
                # update the pending client program to failed
                $this->clientProgramRepository->endedClientPrograms($pendingClientProgram, ['status' => 2 /* failed */, 'reason_id' => $reasonId]);

                DB::commit();

            } catch (Exception $e) {

                DB::rollBack();
                Log::error('Failed to ended the existing mentee\'s client program. Error : '.$e->getMessage(). ' | Line '.$e->getLine());
                continue;

            }

            $progressBar->advance();
        }

        
        $progressBar->finish();

        return Command::SUCCESS;
    }
}
