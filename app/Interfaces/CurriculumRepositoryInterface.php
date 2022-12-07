<?php

namespace App\Interfaces;

interface CurriculumRepositoryInterface 
{
    public function getAllCurriculum();
    public function createCurriculums(array $curriculumDetails);
}