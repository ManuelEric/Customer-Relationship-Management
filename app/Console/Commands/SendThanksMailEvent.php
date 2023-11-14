<?php

namespace App\Console\Commands;

use App\Interfaces\ClientEventLogMailRepositoryInterface;
use App\Interfaces\ClientEventRepositoryInterface;
use App\Interfaces\EventRepositoryInterface;
use App\Jobs\Event\Stem\ProcessEmailThanks;
use Illuminate\Console\Command;

class SendThanksMailEvent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:thanks_mail_event';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a thank you mail after joining the event';

    private EventRepositoryInterface $eventRepository;
    private ClientEventRepositoryInterface $clientEventRepository;
    private ClientEventLogMailRepositoryInterface $clientEventLogMailRepository;

    public function __construct(EventRepositoryInterface $eventRepository, ClientEventRepositoryInterface $clientEventRepository, ClientEventLogMailRepositoryInterface $clientEventLogMailRepository)
    {
        parent::__construct();
        $this->eventRepository = $eventRepository;
        $this->clientEventRepository = $clientEventRepository;
        $this->clientEventLogMailRepository = $clientEventLogMailRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        # change the event name to whatever you want as long as it match to event_title on tbl_events
        $event_name = 'STEM Wonderlab Registration Form';
        $chosen_event = $this->eventRepository->getEventByName($event_name);

        $eventId = $chosen_event->event_id;
        $getParticipants = $this->clientEventRepository->getJoinedClientByEventId($eventId);
        

        if ($getParticipants->count() == 0) {
            $this->info('No participant found for event');
            return Command::SUCCESS;
        }


        foreach ($getParticipants as $participant) {

            $clientevent_id = $participant->clientevent_id;

            # get full information of client
            $master_client = $participant->client;

            $master_roles = $master_client->roles;
            $roles = [];
            foreach ($master_roles as $role) {
                $roles[] = strtolower($role->role_name);
            }

            $client = [
                'recipient' => $master_client->full_name,
                'email' => $master_client->mail,
                'roles' => $roles
            ];


            if (in_array('student', $roles) || in_array('parent', $roles)) {

                ProcessEmailThanks::dispatch($client, $clientevent_id, $this->clientEventLogMailRepository)->onQueue('email-thanks-event_'.$eventId);

            }

        }        

        return Command::SUCCESS;
    }
}
