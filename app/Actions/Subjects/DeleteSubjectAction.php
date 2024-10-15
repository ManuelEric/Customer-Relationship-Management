<?php

namespace App\Actions\Subjects;

use App\Interfaces\SubjectRepositoryInterface;

class DeleteSubjectAction
{
    private SubjectRepositoryInterface $subjectRepository;

    public function __construct(SubjectRepositoryInterface $subjectRepository)
    {
        $this->subjectRepository = $subjectRepository;
    }

    public function execute(
        $subject_id
    )
    {
        # delete subject
        $deleted_subject = $this->subjectRepository->deleteSubject($subject_id);

        return $deleted_subject;
    }
}