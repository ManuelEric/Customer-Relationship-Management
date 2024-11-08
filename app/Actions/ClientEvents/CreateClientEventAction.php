<?php

namespace App\Actions\ClientEvents;

use App\Http\Requests\StoreClientEventRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\GenerateTicketIdTrait;
use App\Interfaces\ClientEventRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\SchoolCurriculumRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Models\School;
use App\Services\Program\ClientEventService;
use Exception;

class CreateClientEventAction
{
    use CreateCustomPrimaryKeyTrait, GenerateTicketIdTrait;
    private ClientEventRepositoryInterface $clientEventRepository;
    private ClientEventService $clientEventService;
    private SchoolRepositoryInterface $schoolRepository;
    private ClientRepositoryInterface $clientRepository;
    private SchoolCurriculumRepositoryInterface $schoolCurriculumRepository;

    public function __construct(ClientEventRepositoryInterface $clientEventRepository, ClientEventService $clientEventService, SchoolRepositoryInterface $schoolRepository, ClientRepositoryInterface $clientRepository, SchoolCurriculumRepositoryInterface $schoolCurriculumRepository)
    {
        $this->clientEventRepository = $clientEventRepository;
        $this->clientEventService = $clientEventService;
        $this->schoolRepository = $schoolRepository;
        $this->clientRepository = $clientRepository;
        $this->schoolCurriculumRepository = $schoolCurriculumRepository;
    }

    public function execute(
        StoreClientEventRequest $request,
        Array $new_client_details,
        Array $new_client_event_details,
        Array $new_school_details
    )
    {
        # case 1
            # create new school
            # when sch_id is "add-new"
            if ($request->sch_id == "add-new") {

                $new_school_details = $request->safe()->only([
                    'sch_name',
                    'sch_type',
                    'sch_score',
                ]);

                $last_id = School::max('sch_id');
                $school_id_without_label = $this->remove_primarykey_label($last_id, 4);
                $school_id_with_label = 'SCH-' . $this->add_digit($school_id_without_label + 1, 4);

                if (!$school = $this->schoolRepository->createSchool(['sch_id' => $school_id_with_label] + $new_school_details))
                    throw new Exception('Failed to store new school', 1);

                # insert school curriculum
                if (!$this->schoolCurriculumRepository->createSchoolCurriculum($school_id_with_label, $request->sch_curriculum))
                    throw new Exception('Failed to store school curriculum', 1);


                # remove field sch_id from student detail if exist
                unset($new_client_details['sch_id']);

                # create index sch_id to student details
                # filled with a new school id that was inserted before
                $new_client_details['sch_id'] = $school->sch_id;
            }

            # Case 2
            # Create new client
            # When client not existing
            if ($request->existing_client == 0) {

                switch ($request->status_client) {
                    case 'Student':
                        if (!$client_created = $this->clientRepository->createClient('Student', $new_client_details))
                            throw new Exception('Failed to store new client', 2);
                        break;

                    case 'Parent':
                        if (!$client_created = $this->clientRepository->createClient('Parent', $new_client_details))
                            throw new Exception('Failed to store new client', 2);
                        break;

                    case 'Teacher/Counsellor':
                        if (!$client_created = $this->clientRepository->createClient('Teacher/Counselor', $new_client_details))
                            throw new Exception('Failed to store new client', 2);
                        break;
                }

                $new_client_event_details['client_id'] = $client_created->id;
            }

            # Case 3
            # Create client event
            # insert into client event
            if (!$stored_client_event = $this->clientEventRepository->createClientEvent($new_client_event_details))
                throw new Exception('Failed to store new client event', 3);


            # Case 4
            # Generate ticket ID when the event is offline or hybrid
            # Updated generate ticket ID for all events 

            // if (in_array($stored_client_event->event->type, ['offline', 'hybrid'])) {

                $ticket_id = $this->tnGenerateTicketId();
                $new_client_event = $this->clientEventRepository->updateClientEvent($stored_client_event->clientevent_id, ['ticket_id' => $ticket_id]);
            // }
            

        return $new_client_event;
    }
}