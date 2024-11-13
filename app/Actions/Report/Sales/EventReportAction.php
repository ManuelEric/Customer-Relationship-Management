<?php

namespace App\Actions\Report\Sales;

use App\Interfaces\ClientEventRepositoryInterface;
use App\Interfaces\EventRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use Illuminate\Support\Collection;

class EventReportAction
{
    private EventRepositoryInterface $eventRepository;
    private ClientEventRepositoryInterface $clientEventRepository;
    private SchoolRepositoryInterface $schoolRepository;

    public function __construct(
        EventRepositoryInterface $eventRepository,
        ClientEventRepositoryInterface $clientEventRepository,
        SchoolRepositoryInterface $schoolRepository,
        )
    {
        $this->eventRepository = $eventRepository;
        $this->clientEventRepository = $clientEventRepository;
        $this->schoolRepository = $schoolRepository;
    }

    public function execute(String $event_name = null): Array
    {
        $choosen_event = $event_id = null;
        if ($event_name !== null)
        {
            $choosen_event = $this->eventRepository->getEventByName($event_name);
            $event_id = isset($choosen_event) ? $choosen_event->event_id : null;
        }
        $events = $this->eventRepository->getAllEvents();
        $clients = $this->clientEventRepository->getReportClientEventsGroupByRoles($event_id);
        $conversion_leads = $this->clientEventRepository->getConversionLead(['eventId' => $event_id]);

        # new get feeder data
        $feeder = $this->schoolRepository->getFeederSchools($event_id);

        # query existing mentee from client event
        $existing_mentee = $this->clientEventRepository->getExistingMenteeFromClientEvent($event_id);
        $id_mentee = $existing_mentee->pluck('client_id')->toArray();

        # query existing non mentee from client event
        $existing_non_mentee = $this->clientEventRepository->getExistingNonMenteeFromClientEvent($event_id);
        $id_nonMentee = $existing_non_mentee->pluck('client_id')->toArray();

        # to be displayed
        $undefined_clients = $clients->whereNotIn('client_id', $id_nonMentee)->whereNotIn('client_id', $id_mentee)->unique('client_id');
        $check_client = $this->checkExistingOrNewClientEvent($undefined_clients);
        $id_non_client = $this->getIdClient($check_client->where('type', 'ExistNonClient'));
        $existing_non_client = $clients->whereIn('client_id', $id_non_client)->unique('client_id');
        $id_new_client = $this->getIdClient($check_client->where('type', 'New'));
        $new_client = $clients->whereIn('client_id', $id_new_client)->unique('client_id');

        return compact('existing_mentee', 'existing_non_mentee', 'existing_non_client', 'new_client', 'events', 'conversion_leads', 'choosen_event', 'feeder');
    }

    protected function checkExistingOrNewClientEvent($undefinedClients)
    {

        $dataClient =  new Collection();

        foreach ($undefinedClients as $undefinedClient) {

            if ($undefinedClient->main_prog_id != null && $undefinedClient->main_prog_id != 1) {
                $dataClient->push((object)[
                    'type' => 'ExistNonClient',
                    'client_id' => $undefinedClient->client_id,
                ]);
            } else {
                $dataClient->push((object)[
                    'type' => 'New',
                    'client_id' => $undefinedClient->client_id,
                ]);
            }
        }
        return $dataClient;
    }

    protected function getIdClient($data)
    {
        $id_client = array();

        $i = 0;
        foreach ($data as $d) {
            $id_client[$i] = $d->client_id;
            $i++;
        }

        return $id_client;
    }
}