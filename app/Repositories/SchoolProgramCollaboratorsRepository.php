<?php

namespace App\Repositories;

use App\Interfaces\SchoolProgramCollaboratorsRepositoryInterface;
use App\Models\Corporate;
use App\Models\PartnerProg;
use App\Models\School;
use App\Models\SchoolProgram;
use App\Models\University;

class SchoolProgramCollaboratorsRepository implements SchoolProgramCollaboratorsRepositoryInterface
{
    # school
    public function getSchoolCollaboratorsBySchoolProgId(string $schoolprogId)
    {
        $school_program = SchoolProgram::find($schoolprogId);
        return $school_program->schoolCollaborators;
    }

    public function storeSchoolCollaborators($schoolprogId, $schoolId)
    {
        $school_program = SchoolProgram::find($schoolprogId);
        $school_program->schoolCollaborators()->attach($schoolId);
        
        # return school master
        return School::whereSchoolId($schoolId);
    }

    public function deleteSchoolCollaborators($schoolprogId, $schoolId)
    {
        $school_program = SchoolProgram::find($schoolprogId);
        $school_program->schoolCollaborators()->detach($schoolId);

        # return school master
        return School::whereSchoolId($schoolId);
    }

    # university
    public function getUnivCollaboratorsBySchoolProgId(string $schoolprogId)
    {
        $school_program = SchoolProgram::find($schoolprogId);
        return $school_program->univCollaborators;
    }

    public function storeUnivCollaborators($schoolprogId, $univId)
    {
        $school_program = SchoolProgram::find($schoolprogId);
        $school_program->univCollaborators()->attach($univId);
        
        # return university master
        return University::whereUniversityId($univId);
    }

    public function deleteUnivCollaborators($schoolprogId, $univId)
    {
        $school_program = SchoolProgram::find($schoolprogId);
        $school_program->univCollaborators()->detach($univId);

        # return university master
        return University::whereUniversityId($univId);
    }

    # partner
    public function getPartnerCollaboratorsBySchoolProgId(string $schoolprogId)
    {
        $school_program = SchoolProgram::find($schoolprogId);
        return $school_program->partnerCollaborators;
    }   

    public function storePartnerCollaborators($schoolprogId, $corpId)
    {
        $school_program = SchoolProgram::find($schoolprogId);
        $school_program->partnerCollaborators()->attach($corpId);
        
        # return corporate / partner master
        return Corporate::whereCorpId($corpId);
    }

    public function deletePartnerCollaborators($schoolprogId, $corpId)
    {
        $school_program = SchoolProgram::find($schoolprogId);
        $school_program->partnerCollaborators()->detach($corpId);
        
        # return corporate / partner master
        return Corporate::whereCorpId($corpId);
    }
}