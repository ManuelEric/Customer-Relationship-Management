<?php

namespace App\Actions\Events;

use App\Http\Requests\StoreEventRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\DeleteFileIfExistTrait;
use App\Http\Traits\UploadFileTrait;
use App\Interfaces\EventRepositoryInterface;

class UpdateEventAction
{
    use CreateCustomPrimaryKeyTrait, UploadFileTrait, DeleteFileIfExistTrait;
    private EventRepositoryInterface $eventRepository;

    public function __construct(EventRepositoryInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function execute(
        StoreEventRequest $request,
        array $new_event_details
    ) {
        $event_id = $request->route('event');
        $new_pic = $request->user_id;

        # check if the banner event is changed or not
        if (isset($request->change_banner)) {

            # get existing banner as a file
            if ($existing_banner_name = $request->old_event_banner) {
                # delete file if exists
                $this->tnDeleteFile('app/public/uploaded_file/events/', $existing_banner_name);
            }

            $file_name = time() . '-' . $event_id;

            # upload banner 
            $new_event_details['event_banner'] = $this->tnUploadFile($request, 'event_banner', $file_name, 'app/public/uploaded_file/events');
        }

        # Update event
        $updated_event = $this->eventRepository->updateEvent($event_id, $new_event_details);

        $this->eventRepository->updateEventPic($event_id, $new_pic);

        return $updated_event;
    }
}
