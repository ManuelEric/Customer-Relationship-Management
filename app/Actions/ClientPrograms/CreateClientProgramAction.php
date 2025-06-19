<?php

namespace App\Actions\ClientPrograms;

use App\Http\Requests\StoreClientProgramRequest;
use App\Interfaces\ClientLeadTrackingRepositoryInterface;
use App\Interfaces\ClientProgramRepositoryInterface;
use App\Jobs\Client\ProcessDefineCategory;
use App\Jobs\Client\ProcessInsertLogClient;
use App\Models\Program;
use App\Services\Program\ClientProgramService;

class CreateClientProgramAction
{
    private ClientProgramRepositoryInterface $clientProgramRepository;
    private ClientProgramService $clientProgramService;
    private ClientLeadTrackingRepositoryInterface $clientLeadTrackingRepository;
    private $admission_prog_list;

    public function __construct(ClientProgramRepositoryInterface $clientProgramRepository, ClientProgramService $clientProgramService, ClientLeadTrackingRepositoryInterface $clientLeadTrackingRepository)
    {
        $this->clientProgramRepository = $clientProgramRepository;
        $this->clientProgramService = $clientProgramService;
        $this->clientLeadTrackingRepository = $clientLeadTrackingRepository;
        $this->admission_prog_list = Program::admissionProgList()->pluck('prog_id')->toArray();
    }

    public function execute(
        StoreClientProgramRequest $request,
        array $client_program_details,
        $student
    ) {
        $file_path = null;

        # p stands for program and its used to get program name from embed form and its gonna be stored as interested_program
        $query = $request->queryP !== NULL ? "?p=" . $request->queryP : null;

        $prog_id = $request->prog_id;

        $client_program_details = $this->clientProgramService->snSetAttributeLead($client_program_details);
        $additional_attributes = $this->clientProgramService->snSetAdditionalAttributes($request, $student, $client_program_details);
        dd($additional_attributes);
        $client_program_details = $additional_attributes['client_program_details'];
        $file_path = $additional_attributes['file_path'];

        $new_client_program = $this->clientProgramRepository->createClientProgram(['client_id' => $student->id] + $client_program_details);
        $new_client_program_id = $new_client_program->clientprog_id;

        # add or remove role mentee
        # add role mentee when program is mentoring and status success then add role mentee
        # remove role mentee Only for method update
        $this->clientProgramService->snAddOrRemoveRoleMentee($prog_id, $student->id, $this->admission_prog_list, $client_program_details['status']);

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
            'inputted_from' => 'create-client-program',
            'clientprog_id' => $new_client_program_id,
            'status_program' => $client_program_details['status'],
        ];

        # trigger to insert log client
        ProcessInsertLogClient::dispatch($client_data_for_log_client)->onQueue('insert-log-client');
        # trigger to define category child
        // ProcessDefineCategory::dispatch([$student->id])->onQueue('define-category-client');

        return ['file_path' => $file_path, 'new_client_program' => $new_client_program];
    }
}
