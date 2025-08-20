<?php

namespace App\Interfaces;

use App\Models\ClientProgram;

interface ProgramPhaseRepositoryInterface
{
    public function getProgramPhase();

    public function rnDeleteProgramPhase(array $program_phase_details);

    public function rnUpdateQuotaProgramPhase(int $clientprog_id, int $phase_detail_id, $phase_lib_id, int $quota);

    public function rnIncrementUseProgramPhase(ClientProgram $clientprogram, int $phase_detail_id, $use);

    public function rnDecrementUseProgramPhase(ClientProgram $clientprogram, int $phase_detail_id, $use);

    public function rnUpdateUseProgramPhase(ClientProgram $clientprogram, int $phase_detail_id, $use);

    public function rnStoreProgramPhase(array $program_phase_details);

    public function rnStoreBulkProgramPhase(array $client_program_details);

    public function rnGetClientProgramDetailsByClientprogId(int $clientprog_id, int $phase_detail_id);

    public function rnGetPhaseDetails();
}
