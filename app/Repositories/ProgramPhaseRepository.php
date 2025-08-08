<?php

namespace App\Repositories;

use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\ProgramPhaseRepositoryInterface;
use App\Models\ClientProgram;
use App\Models\Phase;
use App\Models\PhaseDetail;
use App\Models\PhaseLibrary;
use App\Models\pivot\ClientProgramDetail;
use Illuminate\Support\Carbon;
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
        $phases = Phase::with(['phase_detail.client_program', 'phase_detail.phase_libraries.client_program'])->get();
        return $phases;
    }

    public function rnDeleteProgramPhase(Array $program_phase_details)
    {
        $clientprog_id = $program_phase_details['clientprog_id'];
        $phase_detail_id = $program_phase_details['phase_detail_id'];
        $phase_lib_id = $program_phase_details['phase_lib_id'];

        /**
         * because there are 2 scenes
         * scene 1: if all of packages (program bought) inserted by Sales team then function will be fine
         * scene 2: if all of packages (program bought) inserted by system then function will break, since system doesn't add value to `phase_lib` so everytime query run, they'll find nothing because in checkPackage() > they will carry the phase-lib and inside the data itself doesn't have a phase-lib
         * so in order to fix that..
         * I added condition that will check if the phase_detail is exists.
         * meaning, if user carry phase_lib_id but the phase_detail exists without the phase_lib_id. query still gonna executed
         */
        $client_program_details = ClientProgramDetail::where('clientprog_id', $clientprog_id)->where('phase_detail_id', $phase_detail_id);
        if ($client_program_details->count() == 1 && $client_program_details->first()->phase_lib_id == null) 
        {
            // scene 2
            $client_program_details->delete();
            return $client_program_details;
        } 
        else 
        {
            // scene 1
            if ($program_phase_details['phase_lib_id'] != null){
                $phase_library = PhaseLibrary::find($program_phase_details['phase_lib_id']);
                ClientProgramDetail::where('clientprog_id', $program_phase_details['clientprog_id'])->where('phase_lib_id', $program_phase_details['phase_lib_id'])->delete();
                return $phase_library;
            }
    
            $phase_detail = PhaseDetail::find($program_phase_details['phase_detail_id']);
            ClientProgramDetail::where('clientprog_id', $program_phase_details['clientprog_id'])->where('phase_detail_id', $program_phase_details['phase_detail_id'])->delete();
            return $phase_detail;  
        }
    }

    public function rnIncrementUseProgramPhase(ClientProgram $clientprogram, int $phase_detail_id, $use)
    {      
        /**
         * Legacy
         *  
         */  
        // DB::table('client_program_details')->where('clientprog_id', $clientprogram->clientprog_id)->where('phase_detail_id', $phase_detail_id)->increment('use', $use, ['updated_at' => Carbon::now()]);
        // return DB::table('client_program_details')->where('clientprog_id', $clientprogram->clientprog_id)->where('phase_detail_id', $phase_detail_id)->first();

        $details = ClientProgramDetail::where('clientprog_id', $clientprogram->clientprog_id)->where('phase_detail_id', $phase_detail_id)->first();
        $details->use = $details->use == null ? 0 + $use : $details->use + $use;
        $details->save();
        return $details;

    }

    public function rnDecrementUseProgramPhase(ClientProgram $clientprogram, int $phase_detail_id, $use)
    {    
        // \Illuminate\Support\Facades\Log::debug('Get use quota', $clientprogram->phase_detail()->wherePivot('phase_detail_id', $phase_detail_id)->first()->toArray());    
        # prevent to decrement if the value is already at 0
        if ( $clientprogram->phase_detail()->wherePivot('phase_detail_id', $phase_detail_id)->first()->pivot->use > 0 )
        {
            DB::table('client_program_details')->where('clientprog_id', $clientprogram->clientprog_id)->where('phase_detail_id', $phase_detail_id)->decrement('use', $use, ['updated_at' => Carbon::now()]);
        }

        return DB::table('client_program_details')->where('clientprog_id', $clientprogram->clientprog_id)->where('phase_detail_id', $phase_detail_id)->first();
    }

    public function rnUpdateUseProgramPhase(ClientProgram $clientprogram, int $phase_detail_id, $use)
    {
        DB::table('client_program_details')->where('clientprog_id', $clientprogram->clientprog_id)->where('phase_detail_id', $phase_detail_id)->update(['use' => $use, 'updated_at' => Carbon::now()]);
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

    public function rnStoreBulkProgramPhase(Array $client_program_details)
    {
        return ClientProgramDetail::insert($client_program_details);
    }

    
    public function rnGetClientProgramDetailsByClientprogId(int $clientprog_id, int $phase_detail_id)
    {
        return ClientProgramDetail::where('clientprog_id', $clientprog_id)->where('phase_detail_id', $phase_detail_id)->first();
    }

    public function rnGetPhaseDetails()
    {
        return PhaseDetail::orderBy('id', 'asc')->get();
    }
}   
