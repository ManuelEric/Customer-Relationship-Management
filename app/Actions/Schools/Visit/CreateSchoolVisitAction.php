<?php

namespace App\Actions\Schools\Visit;

use App\Http\Requests\StoreSchoolRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\SchoolVisitRepositoryInterface;

class CreateSchoolVisitAction
{
    use CreateCustomPrimaryKeyTrait;
    private SchoolVisitRepositoryInterface $schoolVisitRepository;

    public function __construct(SchoolVisitRepositoryInterface $schoolVisitRepository)
    {
        $this->schoolVisitRepository = $schoolVisitRepository;
    }

    public function execute(
        String $school_id,
        Array $visit_details
    )
    {
        # default status
        $visit_details['status'] = 'waiting';

        # create school visit
        $created_school_visit = $this->schoolVisitRepository->createSchoolVisit(['sch_id' => $school_id] + $visit_details);
        
        return $created_school_visit;
    }
}