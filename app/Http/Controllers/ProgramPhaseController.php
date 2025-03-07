<?php

namespace App\Http\Controllers;

use App\Enum\LogModule;
use App\Http\Requests\StoreProgramPhaseRequest;
use App\Http\Requests\UpdateProgramPhaseRequest;
use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\ProgramPhaseRepositoryInterface;
use App\Services\Log\LogService;
use Exception;
use Illuminate\Support\Facades\DB;

class ProgramPhaseController extends Controller
{
   
    private ProgramPhaseRepositoryInterface $programPhaseRepository;
    private ClientProgramRepositoryInterface $clientProgramRepository;

    public function __construct(ProgramPhaseRepositoryInterface $programPhaseRepository, ClientProgramRepositoryInterface $clientProgramRepository)
    {
        $this->programPhaseRepository = $programPhaseRepository;
        $this->clientProgramRepository = $clientProgramRepository;
    }


    public function fnRemoveProgramPhase(StoreProgramPhaseRequest $request, LogService $log_service)
    {
        $program_phase_details = $request->safe()->only(['clientprog_id', 'phase_detail_id', 'phase_lib_id']);
       
        DB::beginTransaction();
        try {
            $deleted_program_phase = $this->programPhaseRepository->rnDeleteProgramPhase($program_phase_details);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            $log_service->createErrorLog(LogModule::DELETE_PROGRAM_PHASE, $e->getMessage(), $e->getLine(), $e->getFile(), $program_phase_details);

            return response()->json([
                'success' => false,
                'error' => 'Something went wrong. Please try again'
            ], 500);
        }

        # create log success
        $log_service->createSuccessLog(LogModule::DELETE_PROGRAM_PHASE, 'Program phase has been deleted', $deleted_program_phase->toArray());

        return response()->json([
            'success' => true,
            'data' => $deleted_program_phase
        ]);
    }

    public function fnStoreProgramPhase(StoreProgramPhaseRequest $request, LogService $log_service)
    {
        $program_phase_details = $request->safe()->only(['clientprog_id', 'phase_detail_id', 'phase_lib_id']);

        DB::beginTransaction();
        try {
            $clientprogram = $this->clientProgramRepository->getClientProgramById($program_phase_details['clientprog_id']);
            
            # add new attribute 
            $program_phase_details['grade'] = $clientprogram->client->grade_now ?? null;
            $program_phase_details['quota'] = 0;


            $created_program_phase = $this->programPhaseRepository->rnStoreProgramPhase($program_phase_details);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            $log_service->createErrorLog(LogModule::STORE_PROGRAM_PHASE, $e->getMessage(), $e->getLine(), $e->getFile(), $program_phase_details);

            return response()->json([
                'success' => false,
                'error' => 'Something went wrong. Please try again'
            ], 500);
        }

        # create log success

        $log_service->createSuccessLog(LogModule::STORE_PROGRAM_PHASE, 'Successfully Add Item Program phase', $created_program_phase->toArray());

        return response()->json([
            'success' => true,
            'data' => $created_program_phase
        ]);
    }

    public function fnUpdateQuotaProgramPhase(UpdateProgramPhaseRequest $request, LogService $log_service)
    {
        $program_phase_details = $request->safe()->only(['clientprog_id', 'phase_detail_id', 'phase_lib_id', 'quota']);

        DB::beginTransaction();
        try {
            $updated_clientprogram_detail = $this->programPhaseRepository->rnUpdateQuotaProgramPhase($program_phase_details['clientprog_id'], $program_phase_details['phase_detail_id'], $program_phase_details['phase_lib_id'], $program_phase_details['quota']);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            $log_service->createErrorLog(LogModule::UPDATE_PROGRAM_PHASE, $e->getMessage(), $e->getLine(), $e->getFile(), $program_phase_details);

            return response()->json([
                'success' => false,
                'error' => 'Something went wrong. Please try again'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'data' => $updated_clientprogram_detail
        ]);
    
    }
}
