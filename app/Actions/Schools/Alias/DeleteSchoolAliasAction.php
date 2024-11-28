<?php

namespace App\Actions\Schools\Alias;

use App\Interfaces\SchoolRepositoryInterface;

class DeleteSchoolAliasAction
{
    private SchoolRepositoryInterface $schoolRepository;

    public function __construct(SchoolRepositoryInterface $schoolRepository)
    {
        $this->schoolRepository = $schoolRepository;
    }

    public function execute(
        $alias_id
    )
    {
        # Delete school alias
        $deleted_school_alias = $this->schoolRepository->deleteAlias($alias_id);

        return $deleted_school_alias;
    }
}