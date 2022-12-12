<?php

namespace App\Repositories;

use App\Interfaces\PartnerProgramRepositoryInterface;
use App\Models\PartnerProg;
use DataTables;
use Illuminate\Support\Facades\DB;

class PartnerProgramRepository implements PartnerProgramRepositoryInterface
{

    public function getAllPartnerProgramsDataTables()
    {
        return Datatables::eloquent(
            PartnerProg::leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', 'tbl_partner_prog.corp_id')->
                    leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'tbl_partner_prog.prog_id')->
                    leftJoin('users', 'users.id', '=', 'tbl_partner_prog.empl_id')->
                    select(
                        'tbl_corp.corp_id', 
                        'tbl_partner_prog.id', 
                        'tbl_corp.corp_name as corp_name',
                        'tbl_prog.prog_program as program_name',
                        'tbl_partner_prog.first_discuss',
                        'tbl_partner_prog.participants',
                        'tbl_partner_prog.total_fee',
                        'tbl_partner_prog.status',
                        DB::raw('CONCAT(users.first_name," ",users.last_name) as pic_name')
                    )
        )->filterColumn('pic_name', function($query, $keyword){
            $sql = 'CONCAT(users.first_name," ",users.last_name) like ?';
            $query->whereRaw($sql, ["%{$keyword}%"]);
            }
        )->make(true);
    }

    public function getAllPartnerProgramsByPartnerId($corpId)
    {
        return PartnerProg::where('corp_id', $corpId)->orderBy('id', 'asc')->get();
    }

    public function getPartnerProgramById($partnerProgId)
    {
        return PartnerProg::find($partnerProgId);
    }

    public function deletePartnerProgram($partnerProgId)
    {
        return PartnerProg::destroy($partnerProgId);
    }

    public function createPartnerProgram(array $partnerPrograms)
    {
        return PartnerProg::create($partnerPrograms);
    }

    public function updatePartnerProgram($partnerProgId, array $newPrograms)
    {
        return PartnerProg::find($partnerProgId)->update($newPrograms);
    }
}
