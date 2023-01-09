<?php

namespace App\Repositories;

use App\Interfaces\PartnerProgramRepositoryInterface;
use App\Models\PartnerProg;
use DataTables;
use Illuminate\Support\Facades\DB;

class PartnerProgramRepository implements PartnerProgramRepositoryInterface
{

    public function getAllPartnerProgramsDataTables($filter=null)
    {
        return Datatables::eloquent(
            PartnerProg::leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', 'tbl_partner_prog.corp_id')->
                    leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'tbl_partner_prog.prog_id')->
                    leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')->
                    leftJoin('users', 'users.id', '=', 'tbl_partner_prog.empl_id')->
                    select(
                        'tbl_corp.corp_id', 
                        'tbl_partner_prog.id', 
                        'tbl_corp.corp_name as corp_name',
                        DB::raw('(CASE
                            WHEN tbl_prog.sub_prog_id > 0 THEN CONCAT(tbl_sub_prog.sub_prog_name," - ",tbl_prog.prog_program)
                            ELSE tbl_prog.prog_program
                        END) AS program_name'),
                        'tbl_partner_prog.first_discuss',
                        'tbl_partner_prog.participants',
                        'tbl_partner_prog.total_fee',
                        'tbl_partner_prog.status',
                        DB::raw('CONCAT(users.first_name," ",users.last_name) as pic_name')
                    )->when($filter && isset($filter['partner_name']), function($query) use ($filter) {
                        $query->whereIn('tbl_corp.corp_name', $filter['partner_name']);
                    })
                    ->when($filter && isset($filter['program_name']), function($query) use ($filter) {
                        $query->whereIn('tbl_prog.prog_program', $filter['program_name']);
                    })
                    ->when($filter && isset($filter['pic']), function($query) use ($filter) {
                        $query->whereIn('users.id', $filter['pic']);
                    })
                    ->when($filter && isset($filter['status']) && !isset($filter['start_date']) && !isset($filter['end_date']), function($query) use ($filter) {
                        $query->whereIn('tbl_partner_prog.status', $filter['status']);
                    })
                    ->when($filter && isset($filter['start_date']) && isset($filter['end_date']), function($query) use ($filter) {

                        if(isset($filter['status'])){

                            if(count($filter['status']) == 1){

                                // Status == success
                                if($filter['status'][0] == 1){
                                    $query->whereDate('tbl_partner_prog.start_date', '>=', $filter['start_date'])
                                    ->whereDate('tbl_partner_prog.end_date', '<=', $filter['end_date'])
                                    ->where('tbl_partner_prog.status', $filter['status'][0]);
                                    
                                // Status == denied
                                }else if($filter['status'][0] == 2){
                                    $query->whereDate('tbl_partner_prog.denied_date', '>=', $filter['start_date'])
                                    ->whereDate('tbl_partner_prog.denied_date', '<=', $filter['end_date'])
                                    ->where('tbl_partner_prog.status', $filter['status'][0]);

                                // Status == pending
                                }else if($filter['status'][0] == 0){
                                    $query->whereDate('tbl_partner_prog.created_at', '>=', $filter['start_date'])
                                    ->whereDate('tbl_partner_prog.created_at', '<=', $filter['end_date'])
                                    ->whereIn('tbl_partner_prog.status', $filter['status']);
                                }
                                
                            }else{
                                $query->whereDate('tbl_partner_prog.created_at', '>=', $filter['start_date'])
                                ->whereDate('tbl_partner_prog.created_at', '<=', $filter['end_date'])
                                ->whereIn('tbl_partner_prog.status', $filter['status']);
                            }

                        }else{

                            $query->whereDate('tbl_partner_prog.created_at', '>=', $filter['start_date'])
                                ->whereDate('tbl_partner_prog.created_at', '<=', $filter['end_date']);
                        }
                        
                    })
                    ->when($filter && isset($filter['start_date']) && !isset($filter['end_date']), function($query) use ($filter) {
                        
                        if(isset($filter['status'])){

                            if(count($filter['status']) == 1){
                                
                                // Status == success
                                if($filter['status'][0] == 1){
                                    $query->whereDate('tbl_partner_prog.success_date', '>=', $filter['start_date'])
                                    ->where('tbl_partner_prog.status', $filter['status'][0]);
                                    
                                    
                                // Status == denied
                                }else if($filter['status'][0] == 2){
                                    $query->whereDate('tbl_partner_prog.denied_date', '>=', $filter['start_date'])
                                    ->where('tbl_partner_prog.status', $filter['status'][0]);
                                    
                                    
                                // Status == pending
                                }else if($filter['status'][0] == 0){
                                    $query->whereDate('tbl_partner_prog.created_at', '>=', $filter['start_date'])
                                    ->where('tbl_partner_prog.status', $filter['status'][0]);
                                }
                                
                            }else{
                                $query->whereDate('tbl_partner_prog.created_at', '>=', $filter['start_date'])
                                ->where('tbl_partner_prog.status', $filter['status'][0]);
                            }

                        }else{
                            $query->whereDate('tbl_partner_prog.created_at', '>=', $filter['start_date']);
                        }
                            
                        
                    })
                    ->when($filter && isset($filter['end_date']) && !isset($filter['start_date']), function($query) use ($filter) {

                        if(isset($filter['status'])){

                            if(count($filter['status']) == 1){
                                
                                // Status == success
                                if($filter['status'][0] == 1){
                                    $query->whereDate('tbl_partner_prog.success_date', '<=', $filter['end_date'])
                                    ->where('tbl_partner_prog.status', $filter['status'][0]);
                                    
                                    
                                // Status == denied
                                }else if($filter['status'][0] == 2){
                                    $query->whereDate('tbl_partner_prog.denied_date', '<=', $filter['end_date'])
                                    ->where('tbl_partner_prog.status', $filter['status'][0]);
                                    
                                    
                                // Status == pending
                                }else if($filter['status'][0] == 0){
                                    $query->whereDate('tbl_partner_prog.created_at', '<=', $filter['end_date'])
                                    ->where('tbl_partner_prog.status', $filter['status'][0]);
                                }
                                
                            }else{
                                $query->whereDate('tbl_partner_prog.created_at', '<=', $filter['end_date'])
                                ->where('tbl_partner_prog.status', $filter['status'][0]);
                            }

                        }else{
                            $query->whereDate('tbl_partner_prog.created_at', '<=', $filter['end_date']);
                        }
                            
                        
                    })
                    
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
