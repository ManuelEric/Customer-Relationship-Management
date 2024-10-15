<?php

namespace App\Actions\Majors;

use App\Interfaces\MajorRepositoryInterface;

class DeleteMajorAction
{
    private MajorRepositoryInterface $majorRepository;

    public function __construct(MajorRepositoryInterface $majorRepository)
    {
        $this->majorRepository = $majorRepository;
    }

    public function execute(
        $major_id
    )
    {
        # delete major
        $deleted_major = $this->majorRepository->deleteMajor($major_id);

        return $deleted_major;
    }
}