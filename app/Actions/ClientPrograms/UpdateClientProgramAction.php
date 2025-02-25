<?php

namespace App\Actions\ClientPrograms;

use App\Http\Requests\StoreClientProgramRequest;
use App\Interfaces\ClientLeadTrackingRepositoryInterface;
use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Jobs\Client\ProcessDefineCategory;
use App\Jobs\Client\ProcessInsertLogClient;
use App\Services\Program\ClientProgramService;

class UpdateClientProgramAction
{
    private ClientProgramRepositoryInterface $clientProgramRepository;
    private ClientProgramService $clientProgramService;
    private ClientLeadTrackingRepositoryInterface $clientLeadTrackingRepository;
    private ClientRepositoryInterface $clientRepository;

    public function __construct(ClientProgramRepositoryInterface $clientProgramRepository, ClientProgramService $clientProgramService, ClientLeadTrackingRepositoryInterface $clientLeadTrackingRepository, ClientRepositoryInterface $clientRepository)
    {
        $this->clientProgramRepository = $clientProgramRepository;
        $this->clientProgramService = $clientProgramService;
        $this->clientLeadTrackingRepository = $clientLeadTrackingRepository;
        $this->clientRepository = $clientRepository;
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
        $prog_id = $request->prog_id;

        $old_client_program = $this->clientProgramRepository->getClientProgramById($clientprogram_id);

        $client_program_details = $this->clientProgramService->snSetAttributeLead($client_program_details);

        $additional_attributes = $this->clientProgramService->snSetAdditionalAttributes($request, ['admission' => $admission_prog_list, 'tutoring' => $tutoring_prog_list, 'satact' => $satact_prog_list], $student, $client_program_details, true);
        $client_program_details = $additional_attributes['client_program_details'];
        $file_path = $additional_attributes['file_path'];

        $updated_client_program = $this->clientProgramRepository->updateClientProgram($clientprogram_id, ['client_id' => $student->id] + $client_program_details);
        $updated_client_program_id = $updated_client_program->clientprog_id;
        
        # when status == 5 ('stop') Update client blacklist true
        if($request->status == 5){
            $this->clientRepository->updateClient($student->id, ['blacklist' => 1]);
        }

        # update the path into clientprogram table
        $this->clientProgramRepository->updateFewField($updated_client_program_id, ['agreement' => $file_path]);
        
        $this->clientProgramService->snAddOrRemoveRoleMentee($prog_id, $student->id, $admission_prog_list, $status, true);

        $leads_tracking = $this->clientLeadTrackingRepository->getCurrentClientLead($student->id);

        //! perlu nunggu 1 menit dlu sampai ada client lead tracking status yg 1
        # update status client lead tracking
        if($leads_tracking->count() > 0){
            foreach($leads_tracking as $lead_tracking){
                $this->clientLeadTrackingRepository->updateClientLeadTrackingById($lead_tracking->id, ['status' => 0]);
            }
        }

        $client_data_for_log_client[] = [
            'client_id' => $student->id,
            'first_name' => $student->first_name,
            'last_name' => $student->last_name,
            'inputted_from' => 'update-client-program',
            'clientprog_id' => $clientprogram_id,
            'status_program' => $client_program_details['status'],
            'old_status_program' => $old_client_program->status,
            'running_status_program' => isset($client_program_details['prog_running_status']) ? $client_program_details['prog_running_status'] : null,
            'old_running_status_program' => isset($old_client_program->prog_running_status) ? $old_client_program->prog_running_status : null
        ];

        # trigger to insert log client
        ProcessInsertLogClient::dispatch($client_data_for_log_client)->onQueue('insert-log-client');

        return $updated_client_program;
    }
}
