<?php

namespace App\Actions\Universities;

use App\Interfaces\UniversityRepositoryInterface;

class UpdateUniversityAction
{
    private UniversityRepositoryInterface $universityRepository;

    public function __construct(UniversityRepositoryInterface $universityRepository)
    {
        $this->universityRepository = $universityRepository;
    }

    public function execute(
        String $university_id,
        Array $university_details
    )
    {
        # update university
        $updated_university = $this->universityRepository->updateUniversity($university_id, $university_details);

        return $updated_university;
    }
}