<?php

namespace App\Actions\Majors;

use App\Interfaces\MajorRepositoryInterface;

class UpdateMajorAction
{
    private MajorRepositoryInterface $majorRepository;

    public function __construct(MajorRepositoryInterface $majorRepository)
    {
        $this->majorRepository = $majorRepository;
    }

    public function execute(
        $major_id,
        Array $new_major_details
    )
    {

        $new_major = $this->majorRepository->updateMajor($major_id, $new_major_details);


        return $new_major;
    }
}