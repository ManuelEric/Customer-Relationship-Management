<?php

namespace App\Interfaces;

interface SubjectRepositoryInterface
{
    public function getAllSubjectsDataTables();
    public function getAllSubjects();
    public function getSubjectById($subjectId);
    public function getSubjectByName($subjectName);
    public function createSubject(array $subjectDetails);
    public function updateSubject($subjectId, array $newDetails);
    public function deleteSubject($subjectId);
}
