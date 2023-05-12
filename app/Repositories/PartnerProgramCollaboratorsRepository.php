<?php

namespace App\Repositories;

use App\Interfaces\PartnerProgramCollaboratorsRepositoryInterface;
use App\Models\PartnerProg;

class PartnerProgramCollaboratorsRepository implements PartnerProgramCollaboratorsRepositoryInterface
{
    public function getSchoolCollaboratorsByPartnerProgId(string $partnerprogId)
    {
        $partner_program = PartnerProg::find($partnerprogId);
        return $partner_program->schoolCollaborators;
    }

    public function storeSchoolCollaborators($partnerprogId, $schoolId)
    {
        $partner_program = PartnerProg::find($partnerprogId);
        $partner_program->schoolCollaborators()->attach($schoolId);
        return $partner_program;
    }
}