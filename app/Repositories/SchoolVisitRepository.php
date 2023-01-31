<?php

namespace App\Repositories;

use App\Interfaces\SchoolVisitRepositoryInterface;
use App\Models\SchoolVisit;

class SchoolVisitRepository implements SchoolVisitRepositoryInterface
{
    public function getSchoolVisitBySchoolId($schoolId)
    {
        return SchoolVisit::where('sch_id', $schoolId)->get();
    }

    public function createSchoolVisit(array $visitDetails)
    {
        return SchoolVisit::create($visitDetails);
    }

    public function updateSchoolVisit($visitId, array $newDetails)
    {
        return SchoolVisit::whereId($visitId)->update($newDetails);
    }

    public function deleteSchoolVisit($visitId)
    {
        return SchoolVisit::destroy($visitId);
    }
}