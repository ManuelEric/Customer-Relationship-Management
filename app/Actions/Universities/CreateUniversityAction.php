<?php

namespace App\Actions\Universities;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\UniversityRepositoryInterface;
use App\Models\University;

class CreateUniversityAction
{
    use CreateCustomPrimaryKeyTrait;
    private UniversityRepositoryInterface $universityRepository;

    public function __construct(UniversityRepositoryInterface $universityRepository)
    {
        $this->universityRepository = $universityRepository;
    }

    public function execute(
        Array $university_details
    )
    {
        $last_id = University::max('univ_id');
        $univ_id_without_label =  $last_id ? $this->remove_primarykey_label($last_id, 5) : '0000';
        $univ_id_with_label = 'UNIV-' . $this->add_digit($univ_id_without_label + 1, 3);

        # store new university
        $new_university = $this->universityRepository->createUniversity(['univ_id' => $univ_id_with_label] + $university_details);

        return $new_university;
    }
}