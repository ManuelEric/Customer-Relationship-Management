<?php

namespace App\Interfaces;

interface PartnerProgramCollaboratorsRepositoryInterface
{
    public function getSchoolCollaboratorsByPartnerProgId(string $partnerprogId);
    public function storeSchoolCollaborators($partnerprogId, $schoolId);
}
