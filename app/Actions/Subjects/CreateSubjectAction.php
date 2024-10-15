<?php

namespace App\Actions\Subjects;

use App\Interfaces\SubjectRepositoryInterface;

class CreateSubjectAction
{
    private SubjectRepositoryInterface $subjectRepository;

    public function __construct(SubjectRepositoryInterface $subjectRepository)
    {
        $this->subjectRepository = $subjectRepository;
    }

    public function execute(
        Array $new_subject_details
    )
    {
        # store new subject
        $new_subject = $this->subjectRepository->createSubject($new_subject_details);

        return $new_subject;
    }
}