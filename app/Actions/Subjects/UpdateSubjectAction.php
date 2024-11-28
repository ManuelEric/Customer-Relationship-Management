<?php

namespace App\Actions\Subjects;

use App\Interfaces\SubjectRepositoryInterface;

class UpdateSubjectAction
{
    private SubjectRepositoryInterface $subjectRepository;

    public function __construct(SubjectRepositoryInterface $subjectRepository)
    {
        $this->subjectRepository = $subjectRepository;
    }

    public function execute(
        $subject_id,
        Array $new_subject_details
    )
    {

        $updated_subject = $this->subjectRepository->updateSubject($subject_id, $new_subject_details);

        return $updated_subject;
    }
}