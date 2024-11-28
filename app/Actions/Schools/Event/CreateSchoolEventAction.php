<?php

namespace App\Actions\Schools\Event;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\SchoolEventRepositoryInterface;

class CreateSchoolEventAction
{
    use CreateCustomPrimaryKeyTrait;
    private SchoolEventRepositoryInterface $schoolEventRepository;

    public function __construct(SchoolEventRepositoryInterface $schoolEventRepository)
    {
        $this->schoolEventRepository = $schoolEventRepository;
    }

    public function execute(
        String $event_id,
        Array $school_details
    )
    {
        # store new school event
        $new_school_event = $this->schoolEventRepository->addSchoolEvent($event_id, $school_details);

        return $new_school_event;
    }
}