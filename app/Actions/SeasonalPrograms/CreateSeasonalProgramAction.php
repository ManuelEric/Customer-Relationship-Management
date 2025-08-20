<?php

namespace App\Actions\SeasonalPrograms;

use App\Interfaces\SeasonalProgramRepositoryInterface;

class CreateSeasonalProgramAction
{
    private SeasonalProgramRepositoryInterface $seasonalProgramRepository;

    public function __construct(SeasonalProgramRepositoryInterface $seasonalProgramRepository)
    {
        $this->seasonalProgramRepository = $seasonalProgramRepository;
    }

    public function execute(
        array $seasonal_program_details
    ) {
        // store new sales target
        $new_seasonal_program = $this->seasonalProgramRepository->storeSeasonalProgram($seasonal_program_details);

        return $new_seasonal_program;
    }
}
