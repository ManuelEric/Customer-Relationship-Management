<?php

namespace App\Actions\Corporates\Event;

use App\Interfaces\CorporatePartnerEventRepositoryInterface;
use App\Interfaces\CorporatePicRepositoryInterface;
use App\Interfaces\EventRepositoryInterface;

class DeleteCorporateEventAction
{
    private CorporatePicRepositoryInterface $corporatePicRepository;
    private CorporatePartnerEventRepositoryInterface $corporatePartnerEventRepository;
    private EventRepositoryInterface $eventRepository;

    public function __construct(CorporatePicRepositoryInterface $corporatePicRepository, CorporatePartnerEventRepositoryInterface $corporatePartnerEventRepository, EventRepositoryInterface $eventRepository)
    {
        $this->corporatePicRepository = $corporatePicRepository;
        $this->corporatePartnerEventRepository = $corporatePartnerEventRepository;
        $this->eventRepository = $eventRepository;
    }

    public function execute(
        String $event_id,
        String $corporate_id
    )
    {
        $event = $this->eventRepository->getEventById($event_id);

        if (count($event->partner_speaker()->where('corp_id', $corporate_id)->get()) > 0) {
            $this->corporatePicRepository->deleteAgendaSpeaker($corporate_id,  $event_id);
        }

        # Delete corporate event
        $deleted_corporate_event = $this->corporatePartnerEventRepository->destroyPartnerEvent($event_id, $corporate_id);

        return $deleted_corporate_event;
    }
}