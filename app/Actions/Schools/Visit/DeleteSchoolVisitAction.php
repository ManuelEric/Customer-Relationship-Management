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
        # Delete schoil visit
        $deleted_school_visit = $this->schoolVisitRepository->deleteSchoolVisit($visit_id);

        return $deleted_school_visit;
    }
}