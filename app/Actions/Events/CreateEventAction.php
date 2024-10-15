<?php

namespace App\Actions\Events;

use App\Http\Requests\StoreEventRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\UploadFileTrait;
use App\Interfaces\EventRepositoryInterface;
use App\Models\Event;

class CreateEventAction
{
    use CreateCustomPrimaryKeyTrait, UploadFileTrait;
    private EventRepositoryInterface $eventRepository;

    public function __construct(EventRepositoryInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function execute(
        StoreEventRequest $request,
        array $new_event_details
    ) {
        $employee_id = $request->user_id;

        $last_id = Event::max('event_id');
        $event_id_without_label = $this->remove_primarykey_label($last_id, 4);
        $event_id_with_label = 'EVT-' . $this->add_digit((int)$event_id_without_label + 1, 4);
        $event_details['event_id'] = $event_id_with_label;
        $file_name = time() . '-' . $event_id_with_label;

        #upload banner 
        $event_details['event_banner'] = $this->tnUploadFile($request, 'event_banner', $file_name, 'app/public/uploaded_file/events');

        $new_event = $this->eventRepository->createEvent($new_event_details);

        $this->eventRepository->addEventPic($event_id_with_label, $employee_id);

        return $new_event;
    }
}
