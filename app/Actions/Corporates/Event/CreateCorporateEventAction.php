<?php

namespace App\Actions\Corporates\Event;

use App\Interfaces\CorporatePartnerEventRepositoryInterface;

class CreateCorporateEventAction
{
    private CorporatePartnerEventRepositoryInterface $corporatePartnerEventRepository;

    public function __construct(CorporatePartnerEventRepositoryInterface $corporatePartnerEventRepository)
    {
        $this->corporatePartnerEventRepository = $corporatePartnerEventRepository;
    }

    public function execute(
        string $event_id,
        array $partner_details,
    ) {

        // store new corporate event
        $new_corporate_event = $this->corporatePartnerEventRepository->addPartnerEvent($event_id, $partner_details);

        return $new_corporate_event;
    }
}
