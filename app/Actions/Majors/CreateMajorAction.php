<?php

namespace App\Actions\Majors;

use App\Interfaces\MajorRepositoryInterface;

class CreateMajorAction
{
    private MajorRepositoryInterface $majorRepository;

    public function __construct(MajorRepositoryInterface $majorRepository)
    {
        $this->majorRepository = $majorRepository;
    }

    public function execute(
        Array $new_major_details
    )
    {
        # store new lead
        $new_major = $this->majorRepository->createMajor($new_major_details);

        return $new_major;
    }
}