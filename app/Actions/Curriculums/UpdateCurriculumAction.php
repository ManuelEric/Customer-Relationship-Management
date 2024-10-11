<?php

namespace App\Actions\Assets;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\CurriculumRepositoryInterface;

class UpdateCurriculumAction
{
    use CreateCustomPrimaryKeyTrait;
    private CurriculumRepositoryInterface $curriculumRepository;

    public function __construct(CurriculumRepositoryInterface $curriculumRepository)
    {
        $this->curriculumRepository = $curriculumRepository;
    }

    public function execute(
        $curriculum_id,
        Array $new_curriculum_details
    )
    {
        # Update asset
        $updated_asset = $this->curriculumRepository->updateCurriculum($curriculum_id, $new_curriculum_details);

        return $updated_asset;
    }
}