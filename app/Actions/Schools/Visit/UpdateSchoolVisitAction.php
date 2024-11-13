<?php

namespace App\Actions\Schools\Visit;

use App\Interfaces\SchoolVisitRepositoryInterface;

class UpdateSchoolVisitAction
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
        # update school visit
        $updated_school_visit = $this->schoolVisitRepository->updateSchoolVisit($visit_id, ['status' => 'visited']);
       
        return $updated_school_visit;
    }
}