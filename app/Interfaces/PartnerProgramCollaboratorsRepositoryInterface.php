<?php

namespace App\Interfaces;

interface PartnerProgramCollaboratorsRepositoryInterface
{
    # school
    public function getSchoolCollaboratorsByPartnerProgId(string $partnerprogId);
    public function storeSchoolCollaborators($partnerprogId, $schoolId);
    public function deleteSchoolCollaborators($partnerprogId, $schoolId);

    # university
    public function getUnivCollaboratorsByPartnerProgId(string $partnerprogId);
    public function storeUnivCollaborators($partnerprogId, $univId);
    public function deleteUnivCollaborators($partnerprogId, $univId);

    # partner / corporate
    public function getPartnerCollaboratorsByPartnerProgId(string $partnerprogId);
    public function storePartnerCollaborators($partnerprogId, $corpId);
    public function deletePartnerCollaborators($partnerprogId, $corpId);
}
