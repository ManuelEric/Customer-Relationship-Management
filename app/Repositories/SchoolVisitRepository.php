<?php

namespace App\Repositories;

use App\Interfaces\SchoolVisitRepositoryInterface;
use App\Models\SchoolVisit;
use Carbon\Carbon;

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

    public function getReportSchoolVisit($start_date, $end_date)
    {
        return SchoolVisit::whereDate('visit_date', '>=', $start_date)
                ->whereDate('visit_date', '<=', $end_date)
                ->get();
    }
}
