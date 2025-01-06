<?php

namespace App\Repositories;

use App\Interfaces\SubjectRepositoryInterface;
use App\Models\Subject;
use DataTables;

class SubjectRepository implements SubjectRepositoryInterface
{
    public function getAllSubjectsDataTables()
    {
        return Datatables::eloquent(Subject::query())
            ->make(true);
    }

    public function getAllSubjects()
    {
        return Subject::orderBy('name', 'ASC')->get();
    }

    public function getSubjectById($subjectId)
    {
        return Subject::whereId($subjectId)->first();
    }

    public function getSubjectByName($subjectName)
    {
        return Subject::where('name', $subjectName)->first();
    }

    public function createSubject(array $subjectDetails)
    {
        return Subject::create($subjectDetails);
    }

    public function updateSubject($subjectId, array $newDetails)
    {
        return tap(Subject::whereId($subjectId)->first())->update($newDetails);
    }

    public function deleteSubject($subjectId)
    {
        return Subject::destroy($subjectId);
    }

    public function rnGetAllSubjectsByRole(string $role)
    {
        return Subject::where('role', $role)->get();
    }
}