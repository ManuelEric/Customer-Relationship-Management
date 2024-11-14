<?php

namespace App\Actions\Schools\Raw;

use App\Interfaces\SchoolRepositoryInterface;

class UpdateSchoolRawAction
{
    private SchoolRepositoryInterface $schoolRepository;

    public function __construct(SchoolRepositoryInterface $schoolRepository)
    {
        $this->schoolRepository = $schoolRepository;
    }

    public function execute(
        String $school_id,
        Array $school_details
    )
    {
        # set is_verified school to Y (yes)
        $updated_school_raw = $this->schoolRepository->updateSchool($school_id, $school_details + ['is_verified' => 'Y']);

        return $updated_school_raw;
    }
}