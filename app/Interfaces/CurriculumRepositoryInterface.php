<?php

namespace App\Interfaces;

interface CurriculumRepositoryInterface 
{
    public function getAllCurriculumsDataTables();
    public function getAllCurriculums();
    public function getCurriculumById($curriculumId);
    public function createCurriculum(array $curriculumDetails);
    public function deleteCurriculum($curriculumId);
    public function updateCurriculum($curriculumId, array $curriculums);
}