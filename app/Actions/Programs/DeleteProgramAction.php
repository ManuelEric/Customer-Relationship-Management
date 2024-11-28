<?php

namespace App\Actions\Programs;

use App\Interfaces\ProgramRepositoryInterface;

class DeleteProgramAction
{
    private ProgramRepositoryInterface $programRepository;

    public function __construct(ProgramRepositoryInterface $programRepository)
    {
        $this->programRepository = $programRepository;
    }

    public function execute(
        $program_id
    )
    {
        # delete program
        $deleted_program = $this->programRepository->deleteProgram($program_id);

        return $deleted_program;
    }
}