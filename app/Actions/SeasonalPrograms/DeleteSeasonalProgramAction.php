<?php

namespace App\Actions\SeasonalPrograms;

use App\Interfaces\SeasonalProgramRepositoryInterface;

class DeleteSeasonalProgramAction
{
    private SeasonalProgramRepositoryInterface $seasonalProgramRepository;

    public function __construct(SeasonalProgramRepositoryInterface $seasonalProgramRepository)
    {
        $this->seasonalProgramRepository = $seasonalProgramRepository;
    }

    public function execute(
        $seasonal_program_id
    )
    {
        # delete seasonal program
        $deleted_seasonal_program = $this->seasonalProgramRepository->deleteSeasonalProgram($seasonal_program_id);

        return $deleted_seasonal_program;
    }
}