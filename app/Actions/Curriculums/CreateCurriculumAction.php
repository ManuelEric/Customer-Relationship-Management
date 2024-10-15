<?php

namespace App\Actions\Curriculums;

use App\Interfaces\CurriculumRepositoryInterface;

class CreateCurriculumAction
{
    private CurriculumRepositoryInterface $curriculumRepository;

    public function __construct(CurriculumRepositoryInterface $curriculumRepository)
    {
        $this->curriculumRepository = $curriculumRepository;
    }

    public function execute(
        Array $new_curriculum_details
    )
    {

        # store new curriculum
        $new_curriculum = $this->curriculumRepository->createOneCurriculum($new_curriculum_details);

        return $new_curriculum;
    }
}