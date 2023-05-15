<?php

namespace App\Repositories;

use App\Interfaces\ReferralRepositoryInterface;
use App\Models\Referral;
use Carbon\Carbon;
use DataTables;
use Illuminate\Support\Facades\DB;

class ReferralRepository implements ReferralRepositoryInterface
{
    public function getAllReferralDataTables()
    {
        return Datatables::eloquent(
            Referral::leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', 'tbl_referral.partner_id')
                ->leftJoin('program', 'program.prog_id', '=', 'tbl_referral.prog_id')
                ->leftJoin('users', 'users.id', '=', 'tbl_referral.empl_id')->select(
                    'tbl_referral.id',
                    'tbl_corp.corp_name as partner_name',
                    'tbl_referral.referral_type',
                    'program.program_name',
                    'tbl_referral.number_of_student',
                    'tbl_referral.revenue',
                    'tbl_referral.additional_prog_name',
                    'tbl_referral.currency',
                    DB::raw('CONCAT(users.first_name," ",COALESCE(users.last_name, "")) as pic_name')
                )
        )->filterColumn(
            'pic_name',
            function ($query, $keyword) {
                $sql = 'CONCAT(users.first_name," ",users.last_name) like ?';
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
        )
            ->make(true);
    }

    public function getAllReferralByTypeAndMonth($type, $monthYear)
    {
        $year = date('Y', strtotime($monthYear));
        $month = date('m', strtotime($monthYear));

        return Referral::leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', 'tbl_referral.partner_id')
            ->leftJoin('program', 'program.prog_id', '=', 'tbl_referral.prog_id')
            // ->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')
            ->select(
                'tbl_corp.corp_name',
                DB::raw(
                    'CASE tbl_referral.referral_type
                        WHEN "Out" THEN tbl_referral.additional_prog_name
                        WHEN "In" 
                            THEN 
                                program.program_name
                    END AS program_name'
                )
            )
            ->whereYear('tbl_referral.ref_date', '=', $year)
            ->whereMonth('tbl_referral.ref_date', '=', $month)
            ->where('tbl_referral.referral_type', $type)
            ->get();
    }

    public function getReferralTypeByMonthly($monthYear)
    {
        $year = date('Y', strtotime($monthYear));
        $month = date('m', strtotime($monthYear));

        return Referral::select('referral_type', 'revenue', DB::raw('COUNT(*) as count_referral_type'))
            ->whereYear('ref_date', '=', $year)
            ->whereMonth('ref_date', '=', $month)
            ->groupBy('referral_type')
            ->get();
    }

    public function getReferralById($referralId)
    {
        return Referral::find($referralId);
    }
    public function createReferral(array $referralDetails)
    {
        return Referral::create($referralDetails);
    }

    public function updateReferral($referralId, array $newDetails)
    {
        return Referral::whereId($referralId)->update($newDetails);
    }

    public function deleteReferral($referralId)
    {
        return Referral::destroy($referralId);
    }

    public function getReferralComparison($startYear, $endYear)
    {
        return Referral::leftJoin('program', 'program.prog_id', '=', 'tbl_referral.prog_id')
            // ->leftJoin('tbl_main_prog', 'tbl_main_prog.id', '=', 'tbl_prog.main_prog_id')
            // ->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')
            ->select(
                'tbl_referral.prog_id',
                DB::raw(
                    'CASE tbl_referral.referral_type
                        WHEN "Out" THEN tbl_referral.additional_prog_name
                        WHEN "In" 
                            THEN 
                               program.program_name
                    END AS program_name'
                ),
                DB::raw(
                    'CASE tbl_referral.referral_type
                        WHEN "Out" THEN "Referral Out"
                        WHEN "In" THEN "Referral In"
                    END AS type'
                ),
                DB::raw('SUM(number_of_student) as participants'),
                DB::raw('(CASE 
                            WHEN SUM(number_of_student) is null THEN 0
                            ELSE SUM(number_of_student)
                        END) as participants'),
                DB::raw('DATE_FORMAT(ref_date, "%Y") as year'),
                DB::raw("SUM(revenue) as total"),
                // DB::raw('count(tbl_referral.prog_id) as count_program')
                DB::raw(
                    'CASE tbl_referral.referral_type
                            WHEN "Out" THEN count(tbl_referral.additional_prog_name)
                            WHEN "In" THEN count(tbl_referral.prog_id)
                        END AS count_program'
                ),
            )
            ->whereYear(
                'ref_date',
                '=',
                DB::raw('(case year(ref_date)
                                when ' . $startYear . ' then ' . $startYear . '
                                when ' . $endYear . ' then ' . $endYear . '
                            end)')
            )
            ->groupBy('tbl_referral.prog_id')
            ->groupBy(DB::raw('year(ref_date)'))
            ->get();
    }

    public function getTotalReferralProgramComparison($startYear, $endYear)
    {
        $start = Referral::select(DB::raw("'start' as 'type'"), 'referral_type', DB::raw('count(id) as count'), DB::raw('sum(revenue) as total_fee'))
            ->whereYear('ref_date', $startYear)
            ->groupBy('referral_type');

        $end = Referral::select(DB::raw("'end' as 'type'"), 'referral_type', DB::raw('count(id) as count'), DB::raw('sum(revenue) as total_fee'))
            ->whereYear('ref_date', $endYear)
            ->groupBy('referral_type')
            ->union($start)
            ->get();

        return $end;
    }

    public function getReportNewReferral($start_date = null, $end_date = null, $type)
    {
        $firstDay = Carbon::now()->startOfMonth()->toDateString();
        $lastDay = Carbon::now()->endOfMonth()->toDateString();

        if (isset($start_date) && isset($end_date)) {
            $query = Referral::whereDate('created_at', '>=', $start_date)
                ->whereDate('created_at', '<=', $end_date);
        } else if (isset($start_date) && !isset($end_date)) {
            $query = Referral::whereDate('created_at', '>=', $start_date);
        } else if (!isset($start_date) && isset($end_date)) {
            $query = Referral::whereDate('created_at', '<=', $end_date);
        } else {
            $query = Referral::whereBetween('created_at', [$firstDay, $lastDay]);
        }

        switch ($type) {
            case 'In':
                return $query->where('referral_type', 'In')
                    ->get();
                break;

            case 'Out':
                return $query->where('referral_type', 'Out')
                    ->get();
                break;
        }
    }
}
