<?php

namespace App\Jobs\Client;

use App\Interfaces\ClientEventRepositoryInterface;
use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Repositories\ClientProgramRepository;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class ProcessUpdateClientProgramRawStudent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use IsMonitored;

    protected ClientProgramRepositoryInterface $clientProgramRepository;
    protected ClientRepositoryInterface $clientRepository;
    protected $raw_client_id;
    protected $selected_exist_client_id;


    /**
     * Create a new job instance.
     *
     * @return void
     */


    public function __construct($raw_client_id, $selected_exist_client_id)
    {
        $this->raw_client_id = $raw_client_id;
        $this->selected_exist_client_id = $selected_exist_client_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ClientProgramRepositoryInterface $clientProgramRepository, ClientRepositoryInterface $clientRepository)
    {
        DB::beginTransaction();
        try {

            $raw_student = $clientRepository->getClientById($this->raw_client_id);
            Log::debug($this->raw_client_id);

            # Check if raw student have join client_program
            # than update client_id from client_prog with selected exist student
            if (count($raw_student->clientProgram) > 0) {
                $clientprog_ids = $raw_student->clientProgram->pluck('clientprog_id')->toArray();
                Log::debug($clientprog_ids);
                $as = $clientProgramRepository->updateClientPrograms($clientprog_ids, ['client_id' => $this->selected_exist_client_id]);
            }

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Failed to update client_program raw student : ' . $e->getMessage() . ' on line ' . $e->getLine());
        }

        Log::notice('Successfully update client_program raw student  : ', $raw_student->toArray());
    }
}
