<?php

namespace App\Repositories;

use App\Interfaces\FollowupRepositoryInterface;
use App\Models\FollowUp;
use App\Models\FollowupClient;
use DateTime;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class FollowupRepository implements FollowupRepositoryInterface 
{
    public function getAllFollowupByClientProgramId($clientProgramId)
    {
        return FollowUp::where('clientprog_id', $clientProgramId)->get();
    }

    public function getAllFollowupScheduleByDate($requested_date)
    {   
        return FollowUp::where('followup_date', $requested_date)->where('reminder', 0)->get();
    }

    public function createFollowup(array $followupDetails)
    {   
        return FollowUp::create($followupDetails);
    }

    public function updateFollowup($followupId, array $newDetails)
    {
        return FollowUp::whereId($followupId)->update($newDetails);
    }

    public function deleteFollowup($followupId)
    {
        return FollowUp::destroy($followupId);
    }

    # dashboard
    # getting follow up data 
    # within next 7 days
    public function getAllFollowupWithin($days, $month = null)
    {
        $today = date('Y-m-d');
        $lastday = date('Y-m-d', strtotime('+'.$days.' days'));

        $data = [];

        # employee that logged in
        $empl_id = auth()->guard('api')->user()->id ?? auth()->user()->id;

        $followup = FollowUp::when($month, function($query) use ($month) {
                        $query->whereMonth('followup_date', date('m', strtotime($month)))->whereYear('followup_date', date('Y', strtotime($month)));
                    }, function ($query) use ($today, $lastday) {
                        $query->whereBetween('followup_date', [$today, $lastday]);
                    })->
                    when(Session::get('user_role') == 'Employee', function ($query) use ($empl_id) {
                        $query->whereHas('clientProgram', function ($subQuery) use ($empl_id) {
                            $subQuery->where(function ($Q2) use ($empl_id) {
                                $Q2->where('empl_id', $empl_id)->orWhereHas('client', function ($Q3) use ($empl_id) {
                                    $Q3->whereHas('handledBy', function ($Q4) use ($empl_id) {
                                        $Q4->where('users.id', $empl_id);
                                    });
                                });
                            });
                        });
                    })->
                    when(auth()->guard('api')->user(), function ($query) use ($empl_id) {
                        $query->whereHas('clientProgram', function ($subQuery) use ($empl_id) {
                            $subQuery->where(function ($Q2) use ($empl_id) {
                                $Q2->where('empl_id', $empl_id)->orWhereHas('client', function ($Q3) use ($empl_id) {
                                    $Q3->whereHas('handledBy', function ($Q4) use ($empl_id) {
                                        $Q4->where('users.id', $empl_id);
                                    });
                                });
                            });
                        });
                    })->
                    orderBy('followup_date', 'asc')->get();

        if ($followup) {

            foreach ($followup as $detail) 
            {
                $data[$detail->followup_date][] = [
                    'type' => 'followup-client-program',
                    'id' => $detail->id,
                    'clientprog_id' => $detail->clientprog_id,
                    'clientProgram' => $detail->clientProgram,
                    'status' => $detail->status,
                    'notes' => $detail->notes,
                    'reminder' => $detail->reminder
                ];
            }

        }

        if ($followup_client = $this->getAllFollowupClientWithin($days)) {

            foreach ($followup_client as $detail)
            {
                $convert_to_date = date('Y-m-d', strtotime($detail->followup_date));
                $data[$convert_to_date][] = [
                    'type' => 'followup-client',
                    'id' => $detail->id,
                    'client' => $detail->client,
                    'notes' => $detail->notes,
                    'status' => $detail->status
                ];

            }

        }

        ksort($data);
        return $data;
        

    }

    public function getScheduledAppointmentsByUser($advanced_filter = [])
    {
        return FollowupClient::with('client')->whereHas('client', function ($q) {
            $q->isNotSalesAdmin();
        })->where('status', 0)->
        when(!empty($advanced_filter['client_name']), function ($q) use ($advanced_filter) {
            $q->whereHas('client', function ($q2) use ($advanced_filter) {
                $q2->whereRaw("CONCAT(first_name, ' ', COALESCE(last_name, '')) LIKE ?", ["%{$advanced_filter['client_name']}%"]);
            });
        })->
        when(!empty($advanced_filter['followup_date']), function ($q) use ($advanced_filter) {
            $q->whereRaw("followup_date BETWEEN ? AND ?", [
                $advanced_filter['followup_date']['start'].' 00:00:00',
                $advanced_filter['followup_date']['end'].' 23:59:59'
            ]);            
        })->
        get();
    }

    public function getFollowedUpAppointmentsByUser($advanced_filter = [])
    {
        return FollowupClient::with('client')->whereHas('client', function ($q) {
            $q->isNotSalesAdmin();
        })->
        where('status', 1)->
        // whereNotIn('client_id', $this->getScheduledAppointmentsByUser()->pluck('client_id')->toArray())->
        when(!empty($advanced_filter['client_name']), function ($q) use ($advanced_filter) {
            $q->whereHas('client', function ($q2) use ($advanced_filter) {
                $q2->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$advanced_filter['client_name']}%"]);
            });
        })->
        when(!empty($advanced_filter['followedup_date']), function ($q) use ($advanced_filter) {
            $q->whereRaw("followup_date BETWEEN ? AND ?", [
                $advanced_filter['followedup_date']['start'].' 00:00:00',
                $advanced_filter['followedup_date']['end'].' 23:59:59'
            ]);            
        })->
        get();
    }

    #
    # followup client
    #

    public function getAllFollowupClientScheduleByDate($requested_date)
    {   
        return FollowupClient::whereRaw('followup_date like ?', ['%'.$requested_date.'%'])->where('reminder_is_sent', 0)->get();
    }

    public function findFollowupClient($followupId)
    {
        return FollowupClient::find($followupId);
    }

    public function create(array $followupDetails)
    {
        $created = FollowupClient::create($followupDetails);
        
        # get the client from created followup
        $the_client = $created->client_id;

        # turned the status of previous followup into done
        # because there are 2 process that using this function
        # 1 when storing
        # 2 when user set another appointments
        FollowupClient::where('client_id', $the_client)->whereNot('id', $created->id)->update(['status' => 1]);

        # return the created followup
        return $created;
    }

    public function update($followupId, array $followupDetails)
    {
        $followup = FollowupClient::find($followupId);
        $followup->update($followupDetails);
        return $followup;
    }

    public function getAllFollowupClientWithin(int $days)
    {
        $from = date('Y-m-d');
        $to = date('Y-m-d', strtotime('+'.$days.' days'));

        return FollowupClient::with('client')->whereBetween('followup_date', [$from, $to])->get();
    }

}