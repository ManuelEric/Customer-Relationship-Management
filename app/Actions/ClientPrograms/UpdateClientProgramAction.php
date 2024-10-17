<?php

namespace App\Actions\ClientPrograms;

use App\Http\Requests\StoreClientProgramRequest;
use App\Interfaces\ClientLeadTrackingRepositoryInterface;
use App\Interfaces\ClientProgramRepositoryInterface;
use App\Jobs\Client\ProcessDefineCategory;
use App\Services\Program\ClientProgramService;

class UpdateClientProgramAction
{
    private ClientProgramRepositoryInterface $clientProgramRepository;
    private ClientProgramService $clientProgramService;
    private ClientLeadTrackingRepositoryInterface $clientLeadTrackingRepository;

    public function __construct(ClientProgramRepositoryInterface $clientProgramRepository, ClientProgramService $clientProgramService, ClientLeadTrackingRepositoryInterface $clientLeadTrackingRepository)
    {
        $this->clientProgramRepository = $clientProgramRepository;
        $this->clientProgramService = $clientProgramService;
        $this->clientLeadTrackingRepository = $clientLeadTrackingRepository;
    }

    public function execute(
        StoreClientProgramRequest $request,
        $clientprogram_id,
        array $client_program_details,
        $student,
        $admission_prog_list,
        $tutoring_prog_list,
        $satact_prog_list
    ) {

        $status = $request->status;
        $progId = $request->prog_id;

        $client_program_details = $this->clientProgramService->snSetAttributeLead($client_program_details);

        $additional_attributes = $this->clientProgramService->snSetAdditionalAttributes($request, ['admission' => $admission_prog_list, 'tutoring' => $tutoring_prog_list, 'satact' => $satact_prog_list], $student, $client_program_details, true);
        $client_program_details = $additional_attributes['client_program_details'];
        $file_path = $additional_attributes['file_path'];

        $updated_client_program = $this->clientProgramRepository->updateClientProgram($clientprogram_id, ['client_id' => $student->id] + $client_program_details);
        $updated_client_program_id = $updated_client_program->clientprog_id;
        # update the path into clientprogram table
        $this->clientProgramRepository->updateFewField($updated_client_program_id, ['agreement' => $file_path]);
        
        $this->clientProgramService->snAddOrRemoveRoleMentee($progId, $student->id, $admission_prog_list, $status, true);

        $leads_tracking = $this->clientLeadTrackingRepository->getCurrentClientLead($student->id);

        //! perlu nunggu 1 menit dlu sampai ada client lead tracking status yg 1
        # update status client lead tracking
        if($leads_tracking->count() > 0){
            foreach($leads_tracking as $lead_tracking){
                $this->clientLeadTrackingRepository->updateClientLeadTrackingById($lead_tracking->id, ['status' => 0]);
            }
        }

        # trigger to define category child
        ProcessDefineCategory::dispatch([$student->id])->onQueue('define-category-client');


        return $updated_client_program;
    }
}
