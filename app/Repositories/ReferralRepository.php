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
            Referral::leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', 'tbl_referral.partner_id')->
                    leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'tbl_referral.prog_id')->
                    leftJoin('users', 'users.id', '=', 'tbl_referral.empl_id')->
                    select(
                        'tbl_referral.id', 
                        'tbl_corp.corp_name as partner_name',
                        'tbl_referral.referral_type',
                        'tbl_prog.prog_program as program_name',
                        'tbl_referral.number_of_student',
                        'tbl_referral.revenue',
                        'tbl_referral.additional_prog_name',
                        'tbl_referral.currency',
                        DB::raw('CONCAT(users.first_name," ",users.last_name) as pic_name')
                    )
        )->make(true);
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
}