<?php

namespace App\Interfaces;

interface SeasonalProgramRepositoryInterface
{
    public function getDataTables($model);
    public function getSeasonalPrograms($dataTables);
    public function getSeasonalProgramById($id);
    public function storeSeasonalProgram(array $details);
    public function updateSeasonalProgram($id, array $newDetails);
    public function deleteSeasonalProgram($id);
}
