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
        $old_client_program,
        $client_program_id,
        $student_id
    )
    {
        $client_data_for_log_client[] = [
            'client_id' => $old_client_program->client->id,
            'first_name' => $old_client_program->client->first_name,
            'last_name' => $old_client_program->client->last_name,
            'inputted_from' => 'delete-client-program',
            'clientprog_id' => $client_program_id,
        ];

        # delete client program
        $deleted_client_program = $this->clientProgramRepository->deleteClientProgram($client_program_id);

        # trigger to define category client
        ProcessDefineCategory::dispatch([$student_id])->onQueue('define-category-client');

        return $deleted_client_program;
    }
}