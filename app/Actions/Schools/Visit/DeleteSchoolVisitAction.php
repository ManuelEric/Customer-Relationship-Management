<?php

namespace App\Actions\Schools\Visit;

use App\Interfaces\SchoolVisitRepositoryInterface;

class DeleteSchoolVisitAction
{
    private SchoolVisitRepositoryInterface $schoolVisitRepository;

    public function __construct(SchoolVisitRepositoryInterface $schoolVisitRepository)
    {
        $this->schoolVisitRepository = $schoolVisitRepository;
    }

    public function execute(
        $visit_id
    )
    {
        $school_visit = $this->schoolVisitRepository->getSchoolVisitById($visit_id);
        
        # Delete schoil visit
        $this->schoolVisitRepository->deleteSchoolVisit($visit_id);

        return $school_visit;
    }
}