<?php

namespace App\Actions\Curriculums;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\CurriculumRepositoryInterface;

class DeleteCurriculumAction
{
    use CreateCustomPrimaryKeyTrait;
    private CurriculumRepositoryInterface $curriculumRepository;

    public function __construct(CurriculumRepositoryInterface $curriculumRepository)
    {
        $this->curriculumRepository = $curriculumRepository;
    }

    public function execute(
        String $curriculum_id
    )
    {
        # Update curriculum
        $deleted_curriculum = $this->curriculumRepository->deleteCurriculum($curriculum_id);

        return $deleted_curriculum;
    }
}