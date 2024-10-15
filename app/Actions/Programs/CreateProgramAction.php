<?php

namespace App\Actions\Programs;

use App\Interfaces\ProgramRepositoryInterface;

class CreateProgramAction
{
    private ProgramRepositoryInterface $programRepository;

    public function __construct(ProgramRepositoryInterface $programRepository)
    {
        $this->programRepository = $programRepository;
    }

    public function execute(
        Array $new_program_details
    )
    {
        # prog sub can be null
        if (isset($request->prog_sub))
            $new_program_details['prog_sub'] = $request->prog_sub;

        # store new program
        $new_program = $this->programRepository->createProgram($new_program_details);

        return $new_program;
    }
}