<?php

namespace App\Interfaces;

interface SchoolProgramCollaboratorsRepositoryInterface
{
    # school
    public function getSchoolCollaboratorsBySchoolProgId(string $schoolprogId);
    public function storeSchoolCollaborators($schoolprogId, $schoolId);
    public function deleteSchoolCollaborators($schoolprogId, $schoolId);

    # university
    public function getUnivCollaboratorsBySchoolProgId(string $schoolprogId);
    public function storeUnivCollaborators($schoolprogId, $univId);
    public function deleteUnivCollaborators($schoolprogId, $univId);

    # partner / corporate
    public function getPartnerCollaboratorsBySchoolProgId(string $schoolprogId);
    public function storePartnerCollaborators($schoolprogId, $corpId);
    public function deletePartnerCollaborators($schoolprogId, $corpId);
}
