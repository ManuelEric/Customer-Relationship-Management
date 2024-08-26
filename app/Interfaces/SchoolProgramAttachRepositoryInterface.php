<?php

namespace App\Interfaces;

interface SchoolProgramAttachRepositoryInterface
{

    public function getAllSchoolProgramAttachsBySchprogId($schoolProgramId);
    public function getSchoolProgramAttachById($schProgAttachId);
    public function deleteSchoolProgramAttach($schProgAttachId);
    public function createSchoolProgramAttach(array $schoolProgramAttachs);
    public function updateSchoolProgramAttach($schProgAttachId, array $schoolProgramAttachs);
}
