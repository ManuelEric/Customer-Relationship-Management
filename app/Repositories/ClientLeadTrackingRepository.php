<?php

namespace App\Repositories;

use App\Interfaces\ClientLeadTrackingRepositoryInterface;
use App\Models\Asset;
use App\Models\ClientLeadTracking;
use App\Models\ClientProgram;
use App\Models\InitialProgram;
use App\Models\Receipt;
use App\Models\v1\Asset as CRMAsset;
use DataTables;
use Illuminate\Support\Facades\DB;

class ClientLeadTrackingRepository implements ClientLeadTrackingRepositoryInterface 
{
    public function getAllClientLeadTracking()
    {
        return ClientLeadTracking::orderBy('id', 'asc')->get();
    }

    public function getAllClientLeadTrackingById($id) 
    {
        return ClientLeadTracking::where('id', $id)->first();
    }
    
    public function getAllClientLeadTrackingByClientId($client_id) 
    {
        return ClientLeadTracking::where('client_id', $client_id)->get();
    }
    
    public function getCurrentClientLead($client_id) 
    {
        return ClientLeadTracking::where('client_id', $client_id)->where('status', 1)->orderBy('updated_at', 'desc')->get();

    }

    public function getLatestClientLeadTrackingByType($type, $group_id) 
    {
        return ClientLeadTracking::where('type', $type)->where('group_id', $group_id)->first();
    }
    
    public function getHistoryClientLead($client_id) 
    {
        $clientLeadTracking = ClientLeadTracking::where('client_id', $client_id)->get();
        $leads = $clientLeadTracking->where('type', 'Lead');
        $merge = $clientLeadTracking->where('type', 'Program')->mapToGroups(function ($item, $key) use($leads) {
            $lead = $leads->where('group_id', $item['group_id'])->first();
            return [
                $item->initProg->name => [
                    'status' => $item['status'],
                    'initprog' => $item->initProg->name,
                    'created_at' => $item['created_at'],
                    'updated_at' => $item['updated_at'],
                    'total_result_program' => $item['total_result'],
                    'total_result_lead' => $lead['total_result'],
                    'program_status' => $item['program_status'],
                    'lead_status' => $lead['lead_status'],
                    'reason' => isset($lead->reason) ? $lead->reason->reason_name : null,
                ],
            ];
        });

        return $merge;
    }

    public function getMonthlyClientLeadTracking($monthyear) 
    {

        return ClientLeadTracking::leftJoin('tbl_client', 'tbl_client.id', '=', 'tbl_client_lead_tracking.client_id')
                ->leftJoin('tbl_lead', 'tbl_client.lead_id', '=', 'tbl_lead.lead_id')
                ->whereMonth('tbl_client_lead_tracking.updated_at', date('m', strtotime($monthyear)))
                ->whereYear('tbl_client_lead_tracking.updated_at', date('Y', strtotime($monthyear)))
                ->groupBy('client_id')
                ->where('tbl_client_lead_tracking.status', 1)
                ->where('tbl_client_lead_tracking.type', 'lead')
                ->get();
    }

    public function getInitialConsult($monthyear, $type)
    {
        $clientLeads = $this->getMonthlyClientLeadTracking($monthyear);
        $clientId = null;
        foreach ($clientLeads as $clientLead) {
            $clientId[] = $clientLead->client_id;
        }

        $clientProg = ClientProgram::leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'tbl_client_prog.prog_id')
            ->leftJoin('tbl_main_prog', 'tbl_main_prog.id', '=', 'tbl_prog.main_prog_id')
            // ->select('client_id')
            ->whereHas('program', function ($query) {
                $query->whereHas('main_prog', function ($query2) {
                    $query2->where('prog_name', 'like', '%Admissions Mentoring%');
                })->whereHas('sub_prog', function ($query2) {
                    $query2->where('sub_prog_name', 'like', '%Admissions Mentoring%');
                });
            })
            ->whereMonth('tbl_client_prog.assessmentsent_date', date('m', strtotime($monthyear)))
            ->whereYear('tbl_client_prog.assessmentsent_date', date('Y', strtotime($monthyear)));
        
        if($type == 'success'){
            $clientProg->where('status', 1);
        }

        $clientId != null ?  $clientProg->whereIn('client_id', $clientId) : null;  
        $clientProg->groupBy('client_id');
        
        return $clientId != null ? $clientProg->get() : null;
    }

    public function getFailedLead($monthyear){

        $failLeads = $this->getInitialConsult($monthyear, 'failed');
        $countFail = 0;

        if(isset($failLeads) > 0){
            foreach ($failLeads as $failLead) {
                $failLead->status == 2 ? $countFail++ : $countFail = 0;
            }
        }

        $isFailed = $countFail == 3 ? true : false;
        return $isFailed;

    }

    public function getRevenue($monthyear)
    {
        $clientprogs = $this->getInitialConsult($monthyear, 'success');
        $clientprogId = null;
        foreach ($clientprogs as $clientprog) {
            $clientprogId[] = $clientprog->clientprog_id;
        }
        
        $year = date('Y', strtotime('2023-05-01'));
        $month = date('m', strtotime('2023-05-01'));


        $receipt = Receipt::leftJoin('tbl_invdtl', 'tbl_invdtl.invdtl_id', '=', 'tbl_receipt.invdtl_id')
            ->leftJoin('tbl_inv', 'tbl_inv.inv_id', '=', DB::raw('(CASE WHEN tbl_receipt.invdtl_id is not null THEN tbl_invdtl.inv_id ELSE tbl_receipt.inv_id END)'))
            ->leftJoin('tbl_client_prog', 'tbl_client_prog.clientprog_id', '=', 'tbl_inv.clientprog_id')
            ->select(DB::raw('COUNT(tbl_receipt.id) as count_receipt'), DB::raw('CAST(SUM(receipt_amount_idr) as integer) as total'))
            ->whereYear('tbl_receipt.created_at', '=', $year)
            ->whereMonth('tbl_receipt.created_at', '=', $month)
            ->where('tbl_client_prog.status', 1);
        
        $clientprogId != null ?  $receipt->whereIn('tbl_client_prog.clientprog_id', $clientprogId) : null;

            
        return $clientprogId != null ? $receipt->groupBy('tbl_inv.inv_id')->get() : null;
    }

    public function updateClientLeadTracking($clientId, $initProgId, array $leadTrackingDetails) 
    {
        return ClientLeadTracking::where('client_id', $clientId)->where('initialprogram_id', $initProgId)->update($leadTrackingDetails);
    }

    public function updateClientLeadTrackingById($id, array $leadTrackingDetails) 
    {
        return ClientLeadTracking::where('id', $id)->update($leadTrackingDetails);
    }

    public function updateClientLeadTrackingByType($clientId, $initProgId, $type, array $leadTrackingDetails) 
    {
        return ClientLeadTracking::where('client_id', $clientId)->where('initialprogram_id', $initProgId)->where('type', $type)->update($leadTrackingDetails);
    }

    public function createClientLeadTracking(array $leadTrackingDetails) 
    {
        return ClientLeadTracking::create($leadTrackingDetails);
    }
}