<?php

namespace App\Actions\ClientPrograms;

use App\Interfaces\ClientProgramRepositoryInterface;
use App\Jobs\Client\ProcessDefineCategory;

class DeleteClientProgramAction
{
    private ClientProgramRepositoryInterface $clientProgramRepository;

    public function __construct(ClientProgramRepositoryInterface $clientProgramRepository)
    {
        $this->clientProgramRepository = $clientProgramRepository;
    }

    public function execute(
        $client_program_id,
        $student_id
    )
    {
        # delete client program
        $deleted_client_program = $this->clientProgramRepository->deleteClientProgram($client_program_id);
        
        # trigger to define category client
        ProcessDefineCategory::dispatch([$student_id])->onQueue('define-category-client');

        return $deleted_client_program;
    }
}