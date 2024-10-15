<?php

namespace App\Actions\Programs;

use App\Interfaces\ProgramRepositoryInterface;

class UpdateProgramAction
{
    private ProgramRepositoryInterface $programRepository;

    public function __construct(ProgramRepositoryInterface $programRepository)
    {
        $this->programRepository = $programRepository;
    }

    public function execute(
        $old_prog_id,
        Array $new_program_details
    )
    {

        $updated_program = $this->programRepository->updateProgram($old_prog_id, $new_program_details);

        return $updated_program;
    }
}