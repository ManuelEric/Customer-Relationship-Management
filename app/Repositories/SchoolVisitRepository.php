<?php

namespace App\Repositories;

use App\Interfaces\SchoolVisitRepositoryInterface;
use App\Models\SchoolVisit;
use Carbon\Carbon;

class SchoolVisitRepository implements SchoolVisitRepositoryInterface
{

    public function getSchoolVisitById($visitId)
    {
        return SchoolVisit::whereId($visitId)->first();
    }

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
        return tap(SchoolVisit::whereId($visitId)->first())->update($newDetails);
    }

    public function deleteSchoolVisit($visitId)
    {
        return SchoolVisit::destroy($visitId);
    }

    public function getReportSchoolVisit($start_date = null, $end_date = null)
    {
        $firstDay = Carbon::now()->startOfMonth()->toDateString();
        $lastDay = Carbon::now()->endOfMonth()->toDateString();

        if (isset($start_date) && isset($end_date)) {
            return SchoolVisit::whereDate('visit_date', '>=', $start_date)
                ->whereDate('visit_date', '<=', $end_date)
                ->get();
        } else if (isset($start_date) && !isset($end_date)) {
            return SchoolVisit::whereDate('visit_date', '>=', $start_date)
                ->get();
        } else if (!isset($start_date) && isset($end_date)) {
            return SchoolVisit::whereDate('visit_date', '<=', $end_date)
                ->get();
        } else {
            return SchoolVisit::whereBetween('visit_date', [$firstDay, $lastDay])
                ->get();
        }
    }
}
