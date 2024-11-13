<?php

namespace App\Actions\Schools;

use App\Interfaces\SchoolRepositoryInterface;

class DeleteSchoolAction
{
    private SchoolRepositoryInterface $schoolRepository;

    public function __construct(SchoolRepositoryInterface $schoolRepository)
    {
        $this->schoolRepository = $schoolRepository;
    }

    public function execute(
        String $school_id
    )
    {
        # Delete schol
        $deleted_school = $this->schoolRepository->deleteSchool($school_id);

        return $deleted_school;
    }
}