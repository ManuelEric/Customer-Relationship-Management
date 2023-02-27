<?php

namespace App\Repositories;

use App\Interfaces\ReferralRepositoryInterface;
use App\Models\Referral;
use DataTables;
use Illuminate\Support\Facades\DB;

class ReferralRepository implements ReferralRepositoryInterface
{
    public function getAllReferralDataTables()
    {
        return Datatables::eloquent(
            Referral::leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', 'tbl_referral.partner_id')->leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'tbl_referral.prog_id')->leftJoin('users', 'users.id', '=', 'tbl_referral.empl_id')->select(
                'tbl_referral.id',
                'tbl_corp.corp_name as partner_name',
                'tbl_referral.referral_type',
                'tbl_prog.prog_program as program_name',
                'tbl_referral.number_of_student',
                'tbl_referral.revenue',
                'tbl_referral.additional_prog_name',
                'tbl_referral.currency',
                DB::raw('CONCAT(users.first_name," ",COALESCE(users.last_name, "")) as pic_name')
            )
        )->make(true);
    }

    public function getAllReferralByTypeAndMonth($type, $monthYear)
    {
        $year = date('Y', strtotime($monthYear));
        $month = date('m', strtotime($monthYear));

        return Referral::leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', 'tbl_referral.partner_id')
            ->leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'tbl_referral.prog_id')
            ->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')
            ->select(
                'tbl_corp.corp_name',
                DB::raw(
                    'CASE tbl_referral.referral_type
                        WHEN "Out" THEN tbl_referral.additional_prog_name
                        WHEN "In" 
                            THEN 
                                (CASE
                                WHEN tbl_prog.sub_prog_id > 0 THEN CONCAT(tbl_sub_prog.sub_prog_name," - ",tbl_prog.prog_program)
                                    ELSE tbl_prog.prog_program
                                END) 
                    END AS program_name'
                )
            )
            ->whereYear('tbl_referral.created_at', '=', $year)
            ->whereMonth('tbl_referral.created_at', '=', $month)
            ->where('tbl_referral.referral_type', $type)
            ->get();
    }

    public function getReferralTypeByMonthly($monthYear)
    {
        $year = date('Y', strtotime($monthYear));
        $month = date('m', strtotime($monthYear));

        return Referral::select('referral_type', 'revenue', DB::raw('COUNT(*) as count_referral_type'))
            ->whereYear('created_at', '=', $year)
            ->whereMonth('created_at', '=', $month)
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
        return Referral::leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'tbl_referral.prog_id')
            ->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')
            ->select(
                'tbl_referral.prog_id',
                DB::raw(
                    'CASE tbl_referral.referral_type
                        WHEN "Out" THEN tbl_referral.additional_prog_name
                        WHEN "In" 
                            THEN 
                                (CASE
                                WHEN tbl_prog.sub_prog_id > 0 THEN CONCAT(tbl_sub_prog.sub_prog_name," - ",tbl_prog.prog_program)
                                    ELSE tbl_prog.prog_program
                                END) 
                    END AS program_name'
                ),
                DB::raw(
                    'CASE tbl_referral.referral_type
                        WHEN "Out" THEN "Referral Out"
                        WHEN "In" THEN "Referral In"
                    END AS type'
                ),
                DB::raw('SUM(number_of_student) as participants'),
                DB::raw('DATE_FORMAT(ref_date, "%Y") as year'),
                DB::raw("SUM(revenue) as total"),
            )
            ->whereYear(
                'ref_date',
                '=',
                DB::raw('(case year(ref_date)
                                when ' . $startYear . ' then ' . $startYear . '
                                when ' . $endYear . ' then ' . $endYear . '
                            end)')
            )
            ->groupBy('prog_id')
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
}
