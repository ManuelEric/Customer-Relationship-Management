<?php

namespace App\Actions\Universities;

use App\Interfaces\UniversityRepositoryInterface;

class DeleteUniversityAction
{
    private UniversityRepositoryInterface $universityRepository;

    public function __construct(UniversityRepositoryInterface $universityRepository)
    {
        $this->universityRepository = $universityRepository;
    }

    public function execute(
        String $university_id
    )
    {
        # Delete university
        $deleted_university = $this->universityRepository->deleteUniversity($university_id);

        return $deleted_university;
    }
}