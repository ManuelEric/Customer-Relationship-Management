<?php

namespace App\Repositories;

use App\Interfaces\PartnerProgramCollaboratorsRepositoryInterface;
use App\Models\Corporate;
use App\Models\PartnerProg;
use App\Models\School;
use App\Models\University;

class PartnerProgramCollaboratorsRepository implements PartnerProgramCollaboratorsRepositoryInterface
{
    # school
    public function getSchoolCollaboratorsByPartnerProgId(string $partnerprogId)
    {
        $partner_program = PartnerProg::find($partnerprogId);
        return $partner_program->schoolCollaborators;
    }

    public function storeSchoolCollaborators($partnerprogId, $schoolId)
    {
        $partner_program = PartnerProg::find($partnerprogId);
        $partner_program->schoolCollaborators()->attach($schoolId);
        
        # return school master
        return School::whereSchoolId($schoolId);
    }

    public function deleteSchoolCollaborators($partnerprogId, $schoolId)
    {
        $partner_program = PartnerProg::find($partnerprogId);
        $partner_program->schoolCollaborators()->detach($schoolId);

        # return school master
        return School::whereSchoolId($schoolId);
    }

    # university
    public function getUnivCollaboratorsByPartnerProgId(string $partnerprogId)
    {
        $partner_program = PartnerProg::find($partnerprogId);
        return $partner_program->univCollaborators;
    }

    public function storeUnivCollaborators($partnerprogId, $univId)
    {
        $partner_program = PartnerProg::find($partnerprogId);
        $partner_program->univCollaborators()->attach($univId);
        
        # return university master
        return University::whereUniversityId($univId);
    }

    public function deleteUnivCollaborators($partnerprogId, $univId)
    {
        $partner_program = PartnerProg::find($partnerprogId);
        $partner_program->univCollaborators()->detach($univId);

        # return university master
        return University::whereUniversityId($univId);
    }

    # partner
    public function getPartnerCollaboratorsByPartnerProgId(string $partnerprogId)
    {
        $partner_program = PartnerProg::find($partnerprogId);
        return $partner_program->partnerCollaborators;
    }   

    public function storePartnerCollaborators($partnerprogId, $corpId)
    {
        $partner_program = PartnerProg::find($partnerprogId);
        $partner_program->partnerCollaborators()->attach($corpId);
        
        # return corporate / partner master
        return Corporate::whereCorpId($corpId);
    }

    public function deletePartnerCollaborators($partnerprogId, $corpId)
    {
        $partner_program = PartnerProg::find($partnerprogId);
        $partner_program->partnerCollaborators()->detach($corpId);
        
        # return corporate / partner master
        return Corporate::whereCorpId($corpId);
    }
}