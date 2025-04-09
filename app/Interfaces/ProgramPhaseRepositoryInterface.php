<?php

namespace App\Interfaces;

use App\Models\ClientProgram;

interface ProgramPhaseRepositoryInterface
{
    public function getProgramPhase();
    public function rnDeleteProgramPhase(Array $program_phase_details);
    public function rnUpdateQuotaProgramPhase(int $clientprog_id, int $phase_detail_id, $phase_lib_id, int $quota);
    public function rnUpdateUseProgramPhase(ClientProgram $clientprogram, int $phase_detail_id, int $use);
    public function rnStoreProgramPhase(Array $program_phase_details);

}