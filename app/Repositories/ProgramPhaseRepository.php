<?php

namespace App\Repositories;

use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\ProgramPhaseRepositoryInterface;
use App\Models\ClientProgram;
use App\Models\Phase;
use App\Models\PhaseDetail;
use App\Models\PhaseLibrary;
use App\Models\pivot\ClientProgramDetail;
use Illuminate\Support\Facades\DB;

class ProgramPhaseRepository implements ProgramPhaseRepositoryInterface
{
    private ClientProgramRepositoryInterface $clientProgramRepository;

    public function __construct(ClientProgramRepositoryInterface $clientProgramRepository)
    {
        $this->clientProgramRepository = $clientProgramRepository;
    }

    public function getProgramPhase()
    {
        $phases = Phase::with(['phase_detail.client_program', 'phase_detail.phase_libraries.client_program'])
                        ->get();
        
        return $phases;
    }

    public function rnDeleteProgramPhase(Array $program_phase_details)
    {
        if ($program_phase_details['phase_lib_id'] != null){
            $phase_library = PhaseLibrary::find($program_phase_details['phase_lib_id']);
            ClientProgramDetail::where('clientprog_id', $program_phase_details['clientprog_id'])->where('phase_lib_id', $program_phase_details['phase_lib_id'])->delete();
            return $phase_library;
        }

        $phase_detail = PhaseDetail::find($program_phase_details['phase_detail_id']);
        ClientProgramDetail::where('clientprog_id', $program_phase_details['clientprog_id'])->where('phase_detail_id', $program_phase_details['phase_detail_id'])->delete();
        return $phase_detail;  
    }

    public function rnUpdateUseProgramPhase(ClientProgram $clientprogram, int $phase_detail_id, int $use)
    {
        // $clientprogram->phase_detail()->updateExistingPivot($phase_detail_id, ['use' => $use]);
        
        // return $clientprogram->phase_detail()->wherePivot('phase_detail_id', $phase_detail_id)->first();
        
        DB::table('client_program_details')->where('clientprog_id', $clientprogram->clientprog_id)->where('phase_detail_id', $phase_detail_id)->increment('use', $use);

        return DB::table('client_program_details')->where('clientprog_id', $clientprogram->clientprog_id)->where('phase_detail_id', $phase_detail_id)->first();
    }

    public function rnUpdateQuotaProgramPhase(int $clientprog_id, int $phase_detail_id, $phase_lib_id, int $quota)
    {
        $clientprog = $this->clientProgramRepository->getClientProgramById($clientprog_id);

        if ($phase_lib_id != 'null'){
            $clientprog->phase_library()->updateExistingPivot($phase_lib_id, ['quota' => $quota]);
            
            return $clientprog->phase_library()->wherePivot('phase_lib_id', $phase_lib_id)->first();
        }
        $clientprog->phase_detail()->updateExistingPivot($phase_detail_id, ['quota' => $quota]);
              
        return $clientprog->phase_detail()->wherePivot('phase_detail_id', $phase_detail_id)->first();
    }

    public function rnStoreProgramPhase(Array $program_phase_details)
    {
        $created_client_program_detail = ClientProgramDetail::create($program_phase_details);
        
        return $created_client_program_detail;
    }
}   
