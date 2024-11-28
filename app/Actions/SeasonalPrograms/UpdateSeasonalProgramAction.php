<?php

namespace App\Actions\SeasonalPrograms;

use App\Interfaces\SeasonalProgramRepositoryInterface;
use Illuminate\Support\Facades\Artisan;

class UpdateSeasonalProgramAction
{
    private SeasonalProgramRepositoryInterface $seasonalProgramRepository;

    public function __construct(SeasonalProgramRepositoryInterface $seasonalProgramRepository)
    {
        $this->seasonalProgramRepository = $seasonalProgramRepository;
    }

    public function execute(
        $seasonal_program_id,
        Array $new_seasonal_program_details
    )
    {

        $updated_sales_target = $this->seasonalProgramRepository->updateSeasonalProgram($seasonal_program_id, $new_seasonal_program_details);

        return $updated_sales_target;
    }
}