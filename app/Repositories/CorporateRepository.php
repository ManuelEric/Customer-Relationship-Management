<?php

namespace App\Repositories;

use App\Interfaces\CorporateRepositoryInterface;
use App\Models\Corporate;
use App\Models\v1\Corp as CRMCorp;
use Carbon\Carbon;
use DataTables;
use Illuminate\Support\Facades\DB;

class CorporateRepository implements CorporateRepositoryInterface
{
    public function getAllCorporateDataTables()
    {
        return Datatables::eloquent(
            Corporate::
                leftJoin('users', 'users.id', '=', 'tbl_corp.user_id')->
                leftJoin('tbl_industry', 'tbl_industry.id', '=', 'tbl_corp.corp_industry')->
                leftJoin('tbl_industry_subsector', 'tbl_industry_subsector.id', '=', 'tbl_corp.corp_subsector_id')->
                select(
                    'corp_id',
                    DB::raw('(CASE WHEN tbl_corp.type = "Individual Professional" AND tbl_corp.user_id is not null 
                                THEN CONCAT(users.first_name, " ", COALESCE(users.last_name, ""))
                                ELSE tbl_corp.corp_name
                            END) as partnership_name'),
                    'tbl_industry.name as industry_name',
                    'tbl_industry_subsector.name as subsector_name',
                    'corp_mail',
                    'corp_phone',
                    'corp_address',
                    'type',
                    'country_type',
                    'partnership_type',
                    'corp_region',
                    'active_status'
                ))
                ->rawColumns(['corp_address'])
                ->filterColumn('partnership_name', function ($query, $keyword) {
                    $sql = '(CASE WHEN tbl_corp.type = "Individual Professional" AND tbl_corp.user_id is not null 
                                THEN CONCAT(users.first_name, " ", COALESCE(users.last_name, ""))
                                ELSE tbl_corp.corp_name
                            END) like ?';
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->make(true);
    }

    public function getAllCorporate()
    {
        return Corporate::orderBy('corp_name', 'asc')->get();
    }
    public function getCorporateByMonthly($monthYear, $type)
    {
        $year = date('Y', strtotime($monthYear));
        $month = date('m', strtotime($monthYear));

        $query = Corporate::when(!empty($type), function ($q) use ($type)
                {
                    if($type != 'list'){
                        $q->select(DB::raw("count(*) as corp_count"));
                    }else{
                        $q->select('*');
                    }
                });

        if ($type == 'all') {
            $query->where(DB::raw("year(created_at)"), '<=', $year)
                ->where(DB::raw("month(created_at)"), '<=', $month);
        } else {
            $query->where(DB::raw("year(created_at)"), '=', $year)
                ->where(DB::raw("month(created_at)"), '=', $month);
        }


        if ($type != 'list'){
            return $query->pluck('corp_count')->toArray()[0];
        }else{
            return $query->get();
        }
    }

    public function getCorporateById($corporateId)
    {
        return Corporate::whereCorpId($corporateId);
    }

    // public function getCorporateByName($corporateName)
    // {
    //     return Corporate::whereCorpName($corporateName);
    // }

    public function getCorporateByName($corp_name)
    {
        return Corporate::where('corp_name', 'like', $corp_name)->first();
    }

    public function deleteCorporate($corporateId)
    {
        return Corporate::whereCorpId($corporateId)->delete();
    }

    public function createCorporate(array $corporateDetails)
    {
        return Corporate::create($corporateDetails);
    }

    public function updateCorporate($corporateId, array $newDetails)
    {
        return tap(Corporate::whereCorpId($corporateId))->update($newDetails);
    }

    public function cleaningCorporate()
    {
        Corporate::where('corp_industry', '=', '')->update(
            [
                'corp_industry' => null
            ]
        );

        Corporate::where('corp_mail', '=', '')->orWhere('corp_mail', '=', '-')->orWhere('corp_mail', '=', 'no email. contact it')->update(
            [
                'corp_mail' => null
            ]
        );

        Corporate::where('corp_phone', '=', '')->orWhere('corp_phone', '=', '-')->orWhere('corp_phone', '=', 'no contact')->update(
            [
                'corp_phone' => null
            ]
        );

        Corporate::where('corp_insta', '=', '')->update(
            [
                'corp_insta' => null
            ]
        );

        Corporate::where('corp_site', '=', '')->update(
            [
                'corp_site' => null
            ]
        );

        Corporate::where('corp_region', '=', '')->update(
            [
                'corp_region' => null
            ]
        );

        Corporate::where('corp_address', '=', '')->update(
            [
                'corp_address' => null
            ]
        );

        Corporate::where('corp_note', '=', '')->update(
            [
                'corp_note' => null
            ]
        );

        Corporate::where('corp_password', '=', '')->update(
            [
                'corp_password' => null
            ]
        );
    }

    public function getReportNewPartner($start_date, $end_date)
    {
        return Corporate::whereDate('created_at', '>=', $start_date)
                ->whereDate('created_at', '<=', $end_date)
                ->get();
    }

    # crm
    public function getCorpFromV1()
    {
        return CRMCorp::select([
            'corp_id',
            'corp_name',
            DB::raw('(CASE
                WHEN corp_industry = "" THEN NULL ELSE corp_industry
            END) AS corp_industry'),
            DB::raw('(CASE
                WHEN corp_mail = "" OR corp_mail = "-" OR corp_mail like "%no email. contact it%" THEN NULL ELSE corp_mail
            END) AS corp_mail'),
            DB::raw('(CASE
                WHEN corp_phone = "" OR corp_phone = "-" OR corp_phone like "%no contact%" THEN NULL ELSE corp_phone
            END) AS corp_phone'),
            DB::raw('(CASE
                WHEN corp_insta = "" THEN NULL ELSE corp_insta
            END) AS corp_insta'),
            DB::raw('(CASE
                WHEN corp_site = "" THEN NULL ELSE corp_site
            END) AS corp_site'),
            DB::raw('(CASE
                WHEN corp_region = "" THEN NULL ELSE corp_region
            END) AS corp_region'),
            DB::raw('(CASE
                WHEN corp_address = "" THEN NULL ELSE corp_address
            END) AS corp_address'),
            DB::raw('(CASE
                WHEN corp_note = "" THEN NULL ELSE corp_note
            END) AS corp_note'),
            DB::raw('(CASE
                WHEN corp_password = "" THEN NULL ELSE corp_password
            END) AS corp_password'),
            'crop_datecreated',
            'corp_datelastedit',
        ])->get();
    }
}
