<?php

namespace App\Actions\Schools\Detail;

use App\Interfaces\SchoolDetailRepositoryInterface;

class DeleteSchoolDetailAction
{
    private SchoolDetailRepositoryInterface $schoolDetailRepository;

    public function __construct(SchoolDetailRepositoryInterface $schoolDetailRepository)
    {
        $this->schoolDetailRepository = $schoolDetailRepository;
    }

    public function execute(
        $school_detail_id
    )
    {
        $school_detail = $this->schoolDetailRepository->getSchoolDetailById($school_detail_id);

        # Delete school detail
        $this->schoolDetailRepository->deleteSchoolDetail($school_detail_id);

        return $school_detail;
    }
}