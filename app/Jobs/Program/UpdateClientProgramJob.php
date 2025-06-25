<?php

namespace App\Jobs\Program;

use App\Actions\ClientPrograms\UpdateClientProgramAction;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateClientProgramJob implements ShouldQueue
{
    use Queueable;

    protected $request;
    protected $client_program_id;
    protected $client_program_details;
    protected $student;

    /**
     * Create a new job instance.
     */
    public function __construct($request, $client_program_id, $client_program_details, $student)
    {
        $this->request = $request;
        $this->client_program_id = $client_program_id;
        $this->client_program_details = $client_program_details;
        $this->student = $student;
    }

    /**
     * Execute the job.
     */
    public function handle(
        UpdateClientProgramAction $updateClientProgramAction
    )
    {
        Log::debug('Dispatch Job Update Client Program');
        DB::beginTransaction();
        try {
            $updateClientProgramAction->execute(
                $this->request, 
                $this->client_program_id, 
                $this->client_program_details, 
                $this->student
            );
            DB::commit();
        } catch (Exception $err) {
            DB::rollBack();
            throw new Exception($err->getMessage());
        }
    }
}
