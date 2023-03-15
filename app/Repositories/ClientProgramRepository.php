<?php

namespace App\Repositories;

use App\Interfaces\ClientProgramRepositoryInterface;
use App\Models\ClientProgram;
use App\Models\InvoiceProgram;
use App\Models\pivot\ClientMentor;
use App\Models\Reason;
use App\Models\Receipt;
use App\Models\User;
use App\Models\v1\ClientProgram as CRMClientProgram;
use App\Models\ViewClientProgram;
use DataTables;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ClientProgramRepository implements ClientProgramRepositoryInterface 
{
    public function getAllClientProgramDataTables($searchQuery = NULL)
    {
        # finding fieldKey that being searched
        # depends on status
        $fieldKey = ["created_at"];
        if (isset($searchQuery['status'])) {
            foreach ($searchQuery['status'] as $status) {

                switch ($status) {
                    case $status == 1: # success
                        $fieldKey[] = "success_date";
                        break;
    
                    case $status == 2: # failed
                        $fieldKey[] = "failed_date";
                        break;
    
                    case $status == 3: # refund
                        $fieldKey[] = "refund_date";
                        break;
    
                    default: # pending
                        $fieldKey = ["created_at"];
                }
            }
        }

        return Datatables::eloquent(ViewClientProgram::when($searchQuery['clientId'], function($query) use ($searchQuery) {
                $query->where('client_id', $searchQuery['clientId']);
            })
            # search by program name 
            ->when(isset($searchQuery['programName']), function ($query) use ($searchQuery) {
                $query->whereIn('prog_id', $searchQuery['programName']);
            })
            # search by conversion lead
            ->when(isset($searchQuery['leadId']), function ($query) use ($searchQuery) {
                $query->whereIn('lead_id', $searchQuery['leadId']);
            })
            # search by status
            ->when(isset($searchQuery['status']), function ($query) use ($searchQuery) {
                $query->whereIn('status', $searchQuery['status']);
            })
            # search by date
            # when start date && end date filled
            ->when(isset($searchQuery['startDate']) && isset($searchQuery['endDate']), function ($query) use ($searchQuery, $fieldKey) {
                $no = 0;
                foreach ($fieldKey as $key => $val) {
                    if ($no == 0)
                        $query->whereBetween($val, [$searchQuery['startDate'], $searchQuery['endDate']]);
                    else
                        $query->orWhereBetween($val, [$searchQuery['startDate'], $searchQuery['endDate']]);

                    $no++;
                }
            })
            # when start date filled && end date null
            ->when(isset($searchQuery['startDate']) && !isset($searchQuery['endDate']), function ($query) use ($searchQuery, $fieldKey) {
                $no = 0;
                foreach ($fieldKey as $key => $val) {
                    if ($no == 0)
                        $query->whereBetween($val, [$searchQuery['startDate'], $searchQuery['startDate']]);
                    else
                        $query->orWhereBetween($val, [$searchQuery['startDate'], $searchQuery['startDate']]);

                    $no++;
                }
            })
            ->when(isset($searchQuery['endDate']) && !isset($searchQuery['startDate']), function ($query) use ($searchQuery, $fieldKey) {
                $no = 0;
                foreach ($fieldKey as $key => $val) {
                    if ($no == 0)
                        $query->whereBetween($val, [$searchQuery['endDate'], $searchQuery['endDate']]);
                    else
                        $query->orWhereBetween($val, [$searchQuery['endDate'], $searchQuery['endDate']]);

                    $no++;
                }
            })


            # search by mentor / tutor id
            ->when(isset($searchQuery['userId']), function ($query) use ($searchQuery) {
                $query->whereHas('clientMentor', function ($query2) use ($searchQuery) {
                    $query2->whereIn('users.id', $searchQuery['userId']);
                });
            })
            # search by pic id
            ->when(isset($searchQuery['emplId']), function ($query) use ($searchQuery) {
                $query->whereHas('internalPic', function ($query2) use ($searchQuery) {
                    $query2->whereIn('users.id', $searchQuery['emplId']);
                });
            })
        )->make(true);
    }

    public function getAllProgramOnClientProgram()
    {
        return ViewClientProgram::distinct('program_name')->select('program_name', 'prog_id')->get();
    }

    public function getAllConversionLeadOnClientProgram()
    {
        return ViewClientProgram::distinct('conversion_lead')->select('conversion_lead', 'lead_id')->get();
    }

    public function getAllMentorTutorOnClientProgram()
    {
        return ClientMentor::leftJoin('users', 'users.id', '=', 'tbl_client_mentor.user_id')->distinct('user_id')
            ->select('users.id', DB::raw('CONCAT(users.first_name, " ", COALESCE(users.last_name, "")) as fullname'))->get();
    }

    public function getAllPICOnClientProgram()
    {
        return ViewClientProgram::distinct('empl_id')->select('empl_id', 'pic_name')->get();
    }

    public function getClientProgramById($clientProgramId)
    {
        return ClientProgram::whereClientProgramId($clientProgramId);
    }

    public function createClientProgram($clientProgramDetails)
    {

        if (array_key_exists('reason_id', $clientProgramDetails) || array_key_exists('other_reason', $clientProgramDetails)) {

            if (isset($clientProgramDetails['other_reason'])) {

                $reason = Reason::create(
                    [
                        'reason_name' => $clientProgramDetails['other_reason'],
                    ]
                );
                $reasonId = $reason->reason_id;
                $clientProgramDetails['reason_id'] = $reasonId;    

            }
        }
        
        $clientProgram = ClientProgram::create($clientProgramDetails);

        # when main_mentor and backup_mentor is filled which is not null
        # then assumed the user want to input "admission mentoring" program
        # do attach main mentor and backup mentor as client mentor
        if (array_key_exists('main_mentor', $clientProgramDetails) && array_key_exists('backup_mentor', $clientProgramDetails)) {

            # if program end date was less than today 
            # then put status into 0 else 1
            // $status = (strtotime($clientProgramDetails['prog_end_date']) < strtotime(date('Y-m-d'))) ? 0 : 1; # status mentoring [0: inactive, 1: active]
            $status = 1;

            $clientProgram->clientMentor()->attach([$clientProgramDetails['main_mentor'], $clientProgramDetails['backup_mentor']], ['status' => $status]);

        } 
        
        # when tutor id is filled which is not null
        # then assumed the user want to input "tutoring" program
        # do attach tutor as client mentor
        elseif (array_key_exists('tutor_id', $clientProgramDetails)) {

            # if program end date was less than today 
            # then put status into 0 else 1
            $status = (strtotime($clientProgramDetails['prog_end_date']) < strtotime(date('Y-m-d'))) ? 0 : 1; # status mentoring [0: inactive, 1: active]

            $clientProgram->clientMentor()->attach($clientProgramDetails['tutor_id'], ['status' => $status]);

        }

        # when tutor_1 is filled which is not null
        # then assumed the user want to input "sat / act" program
        # do attach tutor_1 AND or OR tutor_2 as client mentor
        elseif (array_key_exists('tutor_1', $clientProgramDetails) || array_key_exists('tutor_2', $clientProgramDetails)) {
            
            # hardcode
            $status = 1;

            if (isset($clientProgramDetails['tutor_1']))
                $tutors['tutor_1'] = $clientProgramDetails['tutor_1'];

            if (isset($clientProgramDetails['tutor_2']))
                $tutors['tutor_2'] = $clientProgramDetails['tutor_2'];
            
            $clientProgram->clientMentor()->attach($tutors, ['status' => $status]);

        }

            
        return $clientProgram;
    }

    public function updateClientProgram($clientProgramId, array $clientProgramDetails)
    {
        # initialize
        $additionalDetails = $fullDetails = [];
        unset($clientProgramDetails['kol_lead_id']);

        # use this only when status switches to pending or success
        # and switch program

        if ($clientProgramDetails['status'] <= 1) {
            # client prog database fields
            $fullDetails = [
                'lead_id'                   => null,
                'prog_id'                   => null,
                'clientevent_id'            => null,
                'eduf_lead_id'              => null,
                'partner_id'                => null,
                'first_discuss_date'        => null,
                'meeting_notes'             => null,
                'status'                    => null,
                'empl_id'                   => null,
                'initconsult_date'          => null,
                'assessmentsent_date'       => null,
                'trial_date'                => null,
                'success_date'              => null,
                'prog_start_date'           => null,
                'prog_end_date'             => null,
                'total_uni'                 => 0,
                'total_foreign_currency'    => 0,
                'foreign_currency'          => 0,
                'foreign_currency_exchange' => 0,
                'total_idr'                 => 0,
                'installment_notes'         => null,
                'prog_running_status'       => 0,
                'timesheet_link'            => null,
                'test_date'                 => null,
                'last_class'                => null,
                'diag_score'                => 0,
                'test_score'                => 0,
                'failed_date'               => null,
                'reason_id'                 => null,
                'refund_date'               => null,
            ];
        }

        if (array_key_exists('main_mentor', $clientProgramDetails) && array_key_exists('backup_mentor', $clientProgramDetails)) {

            $additionalDetails = [
                'main_mentor' => $clientProgramDetails['main_mentor'],
                'backup_mentor' => $clientProgramDetails['backup_mentor']
            ];

            unset($clientProgramDetails['main_mentor']);
            unset($clientProgramDetails['backup_mentor']);
        
        } elseif (array_key_exists('tutor_id', $clientProgramDetails)) {

            $additionalDetails = [
                'tutor_id' => $clientProgramDetails['tutor_id']
            ];

            unset($clientProgramDetails['tutor_id']);

        } elseif (array_key_exists('tutor_1', $clientProgramDetails) || array_key_exists('tutor_2', $clientProgramDetails)) {

            $additionalDetails = [
                'tutor_1' => $clientProgramDetails['tutor_1'],
                'tutor_2' => $clientProgramDetails['tutor_2']
            ];

            unset($clientProgramDetails['tutor_1']);
            unset($clientProgramDetails['tutor_2']);

        }

        if (array_key_exists('reason_id', $clientProgramDetails) || array_key_exists('other_reason', $clientProgramDetails)) {

            if (isset($clientProgramDetails['other_reason'])) {

                if (!$reason = Reason::where('reason_name', $clientProgramDetails['other_reason'])->first()) {

                    $reason = Reason::create(
                        [
                            'reason_name' => $clientProgramDetails['other_reason'],
                        ]
                    );
                }

                $reasonId = $reason->reason_id;
                $clientProgramDetails['reason_id'] = $reasonId;    
                
            }
            unset($clientProgramDetails['other_reason']);
        }
        
        $clientProgram = ClientProgram::where('clientprog_id', $clientProgramId)->update(array_merge($fullDetails, $clientProgramDetails));
        $clientProgram = ClientProgram::whereClientProgramId($clientProgramId);

        # delete the client mentor when status client program changed to pending
        if ($clientProgram->status == 0 && $clientProgram->clientMentor()->count() > 0) {

                $mentorsId = $clientProgram->clientMentor()->pluck('users.id')->toArray();
                $clientProgram->clientMentor()->detach($mentorsId);

        }


        # when main_mentor and backup_mentor is filled which is not null
        # then assumed the user want to input "admission mentoring" program
        # do attach main mentor and backup mentor as client mentor
        if (array_key_exists('main_mentor', $additionalDetails) && array_key_exists('backup_mentor', $additionalDetails)) {

            # if program end date was less than today 
            # then put status into 0 else 1
            $status = (strtotime($clientProgramDetails['prog_end_date']) < strtotime(date('Y-m-d'))) ? 0 : 1; # status mentoring [0: inactive, 1: active]

            $clientProgram->clientMentor()->syncWithPivotValues([$additionalDetails['main_mentor'], $additionalDetails['backup_mentor']], ['status' => $status]);

        } 
        
        # when tutor id is filled which is not null
        # then assumed the user want to input "tutoring" program
        # do attach tutor as client mentor
        elseif (array_key_exists('tutor_id', $additionalDetails)) {

            # if program end date was less than today 
            # then put status into 0 else 1
            $status = (strtotime($clientProgramDetails['prog_end_date']) < strtotime(date('Y-m-d'))) ? 0 : 1; # status mentoring [0: inactive, 1: active]

            $clientProgram->clientMentor()->syncWithPivotValues($additionalDetails['tutor_id'], ['status' => $status]);

        }

        # when tutor_1 is filled which is not null
        # then assumed the user want to input "sat / act" program
        # do attach tutor_1 AND or OR tutor_2 as client mentor
        elseif (array_key_exists('tutor_1', $additionalDetails) || array_key_exists('tutor_2', $additionalDetails)) {
            
            # hardcode
            $status = 1;

            if (isset($additionalDetails['tutor_1']))
                $tutors['tutor_1'] = $additionalDetails['tutor_1'];

            if (isset($additionalDetails['tutor_2']))
                $tutors['tutor_2'] = $additionalDetails['tutor_2'];
            
            $clientProgram->clientMentor()->syncWithPivotValues($tutors, ['status' => $status]);

        }

            
        return $clientProgram;
    }    

    public function deleteClientProgram($clientProgramId)
    {
        return ClientProgram::where('clientprog_id', $clientProgramId)->delete();
    }

    private function getStatusId($status)
    {
        switch ($status) {

            case "Pending":
                $statusId = 0;
                break;

            case "Failed":
                $statusId = 2;
                break;

            case "Refund":
                $statusId = 3;
                break;

            case "Success":
                $statusId = 1;
                break;

        }
        return $statusId;
    }

    # sales tracking
    public function getCountProgramByStatus($status, array $dateDetails)
    {
        $statusId = $this->getStatusId($status);
        return ClientProgram::where('status', $statusId)->whereBetween('created_at', [$dateDetails['startDate'], $dateDetails['endDate']])->count();
    }

    public function getSummaryProgramByStatus($status, array $dateDetails)
    {
        $statusId = $this->getStatusId($status);

        $clientPrograms = ClientProgram::where('status', $statusId)->whereBetween('created_at', [$dateDetails['startDate'], $dateDetails['endDate']])->get();

        $no = 0;
        $data = [];
        foreach ($clientPrograms as $mainProg) {
            
            $data[$mainProg->program->main_prog->prog_name][$mainProg->program->prog_program][] = $no++;
        }

        return $data;
    }

    public function getInitAssessmentProgress($dateDetails) # startDate, endDate
    {
        return ClientProgram::leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'tbl_client_prog.prog_id')
            ->leftJoin('tbl_main_prog', 'tbl_main_prog.id', '=', 'tbl_prog.main_prog_id')
            ->select([
                DB::raw('AVG(DATEDIFF(assessmentsent_date, initconsult_date)) as initialMaking'),
                DB::raw('CONCAT(tbl_main_prog.prog_name, ": ", tbl_prog.prog_program) as program_name_st'),
                DB::raw('AVG(DATEDIFF(success_date, assessmentsent_date)) as converted'),
            ])
            ->whereHas('program', function ($query) {
                $query->whereHas('main_prog', function ($query2) {
                    $query2->where('prog_name', 'like', '%Admissions Mentoring%');
                })->whereHas('sub_prog', function ($query2) {
                    $query2->where('sub_prog_name', 'like', '%Admissions Mentoring%');
                });
            })
            ->where('status', 1)
            ->whereBetween('tbl_client_prog.created_at', [$dateDetails['startDate'], $dateDetails['endDate']])
            ->groupBy('program_name_st')
            ->get();
    }

    public function getLeadSource($dateDetails, $cp_filter = null)
    {
        $userId = $this->getUser($cp_filter);
        
        return ClientProgram::leftJoin('tbl_client', 'tbl_client.id', '=', 'tbl_client_prog.client_id')
            ->leftJoin('tbl_lead', 'tbl_lead.lead_id', '=', 'tbl_client.lead_id')
            ->leftJoin('tbl_eduf_lead', 'tbl_eduf_lead.id', '=', 'tbl_client.eduf_id')
            ->leftJoin('tbl_events', 'tbl_events.event_id', 'tbl_client.event_id')
            ->select([
                DB::raw('(CASE 
                    WHEN tbl_lead.main_lead = "KOL" THEN CONCAT("KOL: ", tbl_lead.sub_lead)
                    WHEN tbl_lead.main_lead = "External Edufair" THEN CONCAT("External Edufair: ", tbl_eduf_lead.title)
                    WHEN tbl_lead.main_lead = "All-In Event" THEN CONCAT("All-In Event: ", tbl_events.event_title)
                    ELSE tbl_lead.main_lead
                END) AS lead_source'),
                DB::raw('COUNT((CASE 
                    WHEN tbl_lead.main_lead = "KOL" THEN CONCAT("KOL: ", tbl_lead.sub_lead)
                    WHEN tbl_lead.main_lead = "External Edufair" THEN CONCAT("External Edufair: ", tbl_eduf_lead.title)
                    WHEN tbl_lead.main_lead = "All-In Event" THEN CONCAT("All-In Event: ", tbl_events.event_title)
                    ELSE tbl_lead.main_lead
                END)) AS lead_source_count'),
            ])
            ->when(isset($cp_filter['quuid']), function ($q) use ($userId) {
                $q->where('empl_id', $userId);
            })
            ->where('tbl_client_prog.status', 1)
            ->whereMonth('success_date', date('m', strtotime($cp_filter['qdate'])))->whereYear('success_date', date('Y', strtotime($cp_filter['qdate'])))
            // ->whereBetween('tbl_client_prog.success_date', [$dateDetails['startDate'], $dateDetails['endDate']])
            ->groupBy('lead_source')
            ->get();
    }

    public function getConversionLead($dateDetails, $cp_filter = null)
    {
        $userId = $this->getUser($cp_filter);
        $program = isset($cp_filter['prog']) ? $cp_filter['prog'] : null;

        return ClientProgram::leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'tbl_client_prog.prog_id')
            ->leftJoin('tbl_lead', 'tbl_lead.lead_id', '=', 'tbl_client_prog.lead_id')
            ->leftJoin('tbl_eduf_lead', 'tbl_eduf_lead.id', '=', 'tbl_client_prog.eduf_lead_id')
            ->leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', 'tbl_client_prog.partner_id')
            ->leftJoin('tbl_client_event', 'tbl_client_event.clientevent_id', '=', 'tbl_client_prog.clientevent_id')
            ->leftJoin('tbl_events', 'tbl_events.event_id', '=', 'tbl_client_event.event_id')
            ->select([
                DB::raw('(CASE 
                    WHEN tbl_lead.main_lead = "KOL" THEN CONCAT("KOL: ", tbl_lead.sub_lead)
                    WHEN tbl_lead.main_lead = "External Edufair" THEN CONCAT("External Edufair: ", tbl_eduf_lead.title)
                    WHEN tbl_lead.main_lead = "All-In Event" THEN CONCAT("All-In Event: ", tbl_events.event_title)
                    ELSE tbl_lead.main_lead
                END) AS conversion_lead'),
                DB::raw('COUNT((CASE 
                    WHEN tbl_lead.main_lead = "KOL" THEN CONCAT("KOL: ", tbl_lead.sub_lead)
                    WHEN tbl_lead.main_lead = "External Edufair" THEN CONCAT("External Edufair: ", tbl_eduf_lead.title)
                    WHEN tbl_lead.main_lead = "All-In Event" THEN CONCAT("All-In Event: ", tbl_events.event_title)
                    ELSE tbl_lead.main_lead
                END)) AS conversion_lead_count'),
            ])
            ->when(isset($cp_filter['quuid']), function ($q) use ($userId) {
                $q->where('empl_id', $userId);
            })
            ->when($program, function($query) use ($program) {
                $query->whereHas('program', function ($query) use ($program) {
                    $query->whereHas('main_prog', function ($query2) use ($program) {
                        $query2->where('prog_name', 'like', '%'.$program.'%');
                    })->orWhereHas('sub_prog', function ($query2) use ($program) {
                        $query2->where('sub_prog_name', 'like', '%'.$program.'%');
                    });
                });
            })
            ->where('tbl_client_prog.status', 1)
            ->whereMonth('success_date', date('m', strtotime($cp_filter['qdate'])))->whereYear('success_date', date('Y', strtotime($cp_filter['qdate'])))
            // ->whereBetween('tbl_client_prog.success_date', [$dateDetails['startDate'], $dateDetails['endDate']])
            ->groupBy('conversion_lead')
            ->get();
    }

    public function getConversionTimeSuccessfulPrograms($dateDetails)
    {
        return ClientProgram::leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'tbl_client_prog.prog_id')
            ->leftJoin('tbl_main_prog', 'tbl_main_prog.id', '=', 'tbl_prog.main_prog_id')
            ->select([
                DB::raw('CONCAT(tbl_main_prog.prog_name, ": ", tbl_prog.prog_program) as program_name_st'),
                DB::raw('AVG(DATEDIFF(success_date, first_discuss_date)) as average_time'),
            ])
            ->whereHas('program', function ($query) {
                $query->whereHas('main_prog', function ($query2) {
                    $query2->where('prog_name', 'like', '%Admissions Mentoring%');
                })->whereHas('sub_prog', function ($query2) {
                    $query2->where('sub_prog_name', 'like', '%Admissions Mentoring%');
                });
            })
            ->where('status', 1)
            ->whereBetween('tbl_client_prog.created_at', [$dateDetails['startDate'], $dateDetails['endDate']])
            ->groupBy('program_name_st')
            ->get();
    }

    # dashboard
    public function getClientProgramGroupByStatusAndUserArray($cp_filter)
    {
        $userId = $this->getUser($cp_filter);

        $data[0] = ClientProgram::
                when($cp_filter['program'], function ($q) use ($cp_filter) {
                    $q->whereHas('program', function ($q2) use ($cp_filter) {
                        $q2->whereHas('main_prog', function ($q3) use ($cp_filter) {
                            $q3->where('prog_name', $cp_filter['program']);
                        })->orWhereHas('sub_prog', function ($q3) use ($cp_filter) {
                            $q3->where('sub_prog_name', $cp_filter['program']);
                        });
                    });
                })->
                when(isset($cp_filter['quuid']), function ($q) use ($userId) {
                    $q->where('empl_id', $userId);
                })->where('status', 0)->when($cp_filter['qdate'], function ($q) use ($cp_filter) {
                    $q->whereMonth('created_at', date('m', strtotime($cp_filter['qdate'])))->whereYear('created_at', date('Y', strtotime($cp_filter['qdate'])));
                })->count();

        $data[1] = ClientProgram::
                when($cp_filter['program'], function ($q) use ($cp_filter) {
                    $q->whereHas('program', function ($q2) use ($cp_filter) {
                        $q2->whereHas('main_prog', function ($q3) use ($cp_filter) {
                            $q3->where('prog_name', $cp_filter['program']);
                        })->orWhereHas('sub_prog', function ($q3) use ($cp_filter) {
                            $q3->where('sub_prog_name', $cp_filter['program']);
                        });
                    });
                })->
                when(isset($cp_filter['quuid']), function ($q) use ($userId) {
                    $q->where('empl_id', $userId);
                })->where('status', 2)->when($cp_filter['qdate'], function ($q) use ($cp_filter) {
                    $q->whereMonth('failed_date', date('m', strtotime($cp_filter['qdate'])))->whereYear('failed_date', date('Y', strtotime($cp_filter['qdate'])));
                })->count(); # failed
        $data[2] = ClientProgram::
                when($cp_filter['program'], function ($q) use ($cp_filter) {
                    $q->whereHas('program', function ($q2) use ($cp_filter) {
                        $q2->whereHas('main_prog', function ($q3) use ($cp_filter) {
                            $q3->where('prog_name', $cp_filter['program']);
                        })->orWhereHas('sub_prog', function ($q3) use ($cp_filter) {
                            $q3->where('sub_prog_name', $cp_filter['program']);
                        });
                    });
                })->
                when(isset($cp_filter['quuid']), function ($q) use ($userId) {
                    $q->where('empl_id', $userId);
                })->where('status', 1)->when($cp_filter['qdate'], function ($q) use ($cp_filter) {
                    $q->whereMonth('success_date', date('m', strtotime($cp_filter['qdate'])))->whereYear('success_date', date('Y', strtotime($cp_filter['qdate'])));
                })->count(); # success
        $data[3] = ClientProgram::
                when($cp_filter['program'], function ($q) use ($cp_filter) {
                    $q->whereHas('program', function ($q2) use ($cp_filter) {
                        $q2->whereHas('main_prog', function ($q3) use ($cp_filter) {
                            $q3->where('prog_name', $cp_filter['program']);
                        })->orWhereHas('sub_prog', function ($q3) use ($cp_filter) {
                            $q3->where('sub_prog_name', $cp_filter['program']);
                        });
                    });
                })->
                when(isset($cp_filter['quuid']), function ($q) use ($userId) {
                    $q->where('empl_id', $userId);
                })->where('status', 3)->when($cp_filter['qdate'], function ($q) use ($cp_filter) {
                    $q->whereMonth('refund_date', date('m', strtotime($cp_filter['qdate'])))->whereYear('refund_date', date('Y', strtotime($cp_filter['qdate'])));
                })->count(); # refund
     
        return $data;
    }

    public function getInitialConsultationInformation($cp_filter)
    {
        $userId = $this->getUser($cp_filter);

        $data[0] = ClientProgram::
                when($cp_filter['qdate'], function ($q) use ($cp_filter) {
                    // $q->whereMonth('created_at', date('m', strtotime($cp_filter['qdate'])))->whereYear('created_at', date('Y', strtotime($cp_filter['qdate'])));
                    $q->whereMonth('initconsult_date', date('m', strtotime($cp_filter['qdate'])))->whereYear('initconsult_date', date('Y', strtotime($cp_filter['qdate'])));
                })->
                when(isset($cp_filter['quuid']), function ($q) use ($userId) {
                    $q->where('empl_id', $userId);
                })->where('status', 0)->where('initconsult_date', '>', Carbon::now())->count(); # soon
                
        // $data[0] = $query->where('status', 0)->where('initconsult_date', '>', Carbon::now())->count(); # soon
        $data[1] = ClientProgram::
                when($cp_filter['qdate'], function ($q) use ($cp_filter) {
                    // $q->whereMonth('created_at', date('m', strtotime($cp_filter['qdate'])))->whereYear('created_at', date('Y', strtotime($cp_filter['qdate'])));
                    $q->whereMonth('initconsult_date', date('m', strtotime($cp_filter['qdate'])))->whereYear('initconsult_date', date('Y', strtotime($cp_filter['qdate'])));
                })->
                when(isset($cp_filter['quuid']), function ($q) use ($userId) {
                    $q->where('empl_id', $userId);
                })->where('status', 0)->where('initconsult_date', '<', Carbon::now())->count(); # already
        $data[2] = ClientProgram::
                when($cp_filter['qdate'], function ($q) use ($cp_filter) {
                    // $q->whereMonth('created_at', date('m', strtotime($cp_filter['qdate'])))->whereYear('created_at', date('Y', strtotime($cp_filter['qdate'])));
                    $q->whereMonth('initconsult_date', date('m', strtotime($cp_filter['qdate'])))->whereYear('initconsult_date', date('Y', strtotime($cp_filter['qdate'])));
                })->
                when(isset($cp_filter['quuid']), function ($q) use ($userId) {
                    $q->where('empl_id', $userId);
                })->where('status', 1)->whereNotNull('success_date')->count(); # success
        
        return $data;
    }

    public function getInitialMaking($dateDetails, $cp_filter)
    {
        $userId = $this->getUser($cp_filter);

        # average value of initial consult and assessment sent date
        return ClientProgram::leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'tbl_client_prog.prog_id')
            ->leftJoin('tbl_main_prog', 'tbl_main_prog.id', '=', 'tbl_prog.main_prog_id')
            ->select([
                DB::raw('AVG(DATEDIFF(assessmentsent_date, initconsult_date)) as initialMaking'),
            ])
            ->whereHas('program', function ($query) {
                $query->whereHas('main_prog', function ($query2) {
                    $query2->where('prog_name', 'like', '%Admissions Mentoring%');
                })->whereHas('sub_prog', function ($query2) {
                    $query2->where('sub_prog_name', 'like', '%Admissions Mentoring%');
                });
            })
            ->when(isset($cp_filter['quuid']), function ($q) use ($userId) {
                $q->where('empl_id', $userId);
            })
            ->where('status', 1)
            ->whereBetween('tbl_client_prog.initconsult_date', [$dateDetails['startDate'], $dateDetails['endDate']])
            ->groupBy('tbl_main_prog.id')
            ->first();
    }

    public function getConversionTimeProgress($dateDetails, $cp_filter)
    {
        $userId = $this->getUser($cp_filter);

        # average value of success date and assessment sent date
        return ClientProgram::leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'tbl_client_prog.prog_id')
            ->leftJoin('tbl_main_prog', 'tbl_main_prog.id', '=', 'tbl_prog.main_prog_id')
            ->select([
                DB::raw('AVG(DATEDIFF(success_date, assessmentsent_date)) as conversionTime'),
            ])
            ->whereHas('program', function ($query) {
                $query->whereHas('main_prog', function ($query2) {
                    $query2->where('prog_name', 'like', '%Admissions Mentoring%');
                })->whereHas('sub_prog', function ($query2) {
                    $query2->where('sub_prog_name', 'like', '%Admissions Mentoring%');
                });
            })
            ->when(isset($cp_filter['quuid']), function ($q) use ($userId) {
                $q->where('empl_id', $userId);
            })
            ->where('status', 1)
            ->whereBetween('tbl_client_prog.initconsult_date', [$dateDetails['startDate'], $dateDetails['endDate']])
            ->groupBy('tbl_main_prog.id')
            ->first();
    }

    public function getSuccessProgramByMonth($cp_filter)
    {
        $userId = $this->getUser($cp_filter);

        return ClientProgram::leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'tbl_client_prog.prog_id')
            ->leftJoin('tbl_main_prog', 'tbl_main_prog.id', '=', 'tbl_prog.main_prog_id')
            ->select([
                DB::raw('CONCAT(tbl_main_prog.prog_name, ": ", tbl_prog.prog_program) as program_name_st'),
                DB::raw('COUNT(*) as total_client_per_program')
            ])
            ->where('status', 1)
            ->when(isset($cp_filter['quuid']), function ($q) use ($userId) {
                $q->where('empl_id', $userId);
            })
            // ->whereMonth('tbl_client_prog.created_at', date('m', strtotime($cp_filter['qdate'])))
            // ->whereYear('tbl_client_prog.created_at', date('Y', strtotime($cp_filter['qdate'])))
            ->whereMonth('success_date', date('m', strtotime($cp_filter['qdate'])))
            ->whereYear('success_date', date('Y', strtotime($cp_filter['qdate'])))
            ->groupBy('program_name_st')
            ->get();
    }

    public function getTotalRevenueByProgramAndMonth($cp_filter)
    {
        $userId = $this->getUser($cp_filter);

        return InvoiceProgram::whereHas('clientprog', function ($query) use ($cp_filter, $userId) {
            $query->whereMonth('success_date', date('m', strtotime($cp_filter['qdate'])))
                ->whereYear('success_date', date('Y', strtotime($cp_filter['qdate'])))
                ->when($cp_filter['program'], function ($q) use ($cp_filter) {
                    $q->whereHas('program', function ($q2) use ($cp_filter) {
                        $q2->whereHas('main_prog', function ($q3) use ($cp_filter) {
                            $q3->where('prog_name', $cp_filter['program']);
                        })->orWhereHas('sub_prog', function ($q3) use ($cp_filter) {
                            $q3->where('sub_prog_name', $cp_filter['program']);
                        });
                    });
                })
                ->when(isset($cp_filter['quuid']), function ($q) use ($userId) {
                    $q->where('empl_id', $userId);
                });
        })->sum('inv_totalprice_idr');
    }

    public function getComparisonBetweenYears($cp_filter)
    {
        $userId = $this->getUser($cp_filter);

        return DB::table('tbl_client_prog as cp')->
            leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'cp.prog_id')->
            leftJoin('tbl_main_prog', 'tbl_main_prog.id', '=', 'tbl_prog.main_prog_id')->
            select([
                'cp.prog_id',
                'tbl_main_prog.prog_name',
                'tbl_prog.prog_program',
                'revenue_year1' => DB::table('tbl_client_prog as scp')
                    ->leftJoin('tbl_inv as si', 'si.clientprog_id', '=', 'scp.clientprog_id')
                    ->whereRaw('scp.prog_id = cp.prog_id')
                    ->where(DB::raw('YEAR(scp.created_at)'), $cp_filter['queryParams_year1'])
                    ->select([
                        DB::raw('SUM(si.inv_totalprice_idr)')
                    ]),
                'revenue_year2' => DB::table('tbl_client_prog as scp')
                    ->leftJoin('tbl_inv as si', 'si.clientprog_id', '=', 'scp.clientprog_id')
                    ->whereRaw('scp.prog_id = cp.prog_id')
                    ->where(DB::raw('YEAR(scp.created_at)'), $cp_filter['queryParams_year2'])
                    ->select([
                        DB::raw('SUM(si.inv_totalprice_idr)')
                    ])
            ])
            ->when($userId, function($query) use ($userId) {
                $query->where('cp.empl_id', $userId);
            })
            ->when(isset($cp_filter['qprogs']), function ($query) use ($cp_filter) {
                $query->whereIn('cp.prog_id', $cp_filter['qprogs']);
            })
            ->groupBy('cp.prog_id', 'tbl_main_prog.prog_name', 'tbl_prog.prog_program',)->get();

    }

    # 

    private function getUser($cp_filter)
    {
        $userId = null;
        if (isset($cp_filter['quuid']) && $cp_filter['quuid'] !== null) {
            $uuid = $cp_filter['quuid'];
            $user = User::where('uuid', $uuid)->first();
            $userId = $user->id;
        }

        return $userId;
    }

    # CRM

    public function getClientProgramFromV1()
    {
        return CRMClientProgram::select([
            'stprog_id',
            'st_num',
            'prog_id',
            'lead_id',
            'eduf_id',
            'infl_id',
            'stprog_firstdisdate',
            DB::raw('(CASE 
                WHEN stprog_followupdate = "0000-00-00" THEN NULL ELSE stprog_followupdate
            END) as stprog_followupdate'),
            DB::raw('(CASE 
                WHEN stprog_lastdisdate = "0000-00-00" THEN NULL ELSE stprog_lastdisdate
            END) as stprog_lastdisdate'),
            DB::raw('(CASE 
                WHEN stprog_meetingdate = "0000-00-00" THEN NULL ELSE stprog_meetingdate
            END) as stprog_meetingdate'),
            DB::raw('(CASE 
                WHEN stprog_meetingnote = "" THEN NULL ELSE stprog_meetingnote
            END) as stprog_meetingnote'),
            'stprog_status',
            DB::raw('(CASE 
                WHEN stprog_statusprogdate = "0000-00-00" THEN NULL ELSE stprog_statusprogdate
            END) as stprog_statusprogdate'),
            DB::raw('(CASE 
                WHEN stprog_init_consult = "0000-00-00" THEN NULL ELSE stprog_init_consult
            END) as stprog_init_consult'),
            DB::raw('(CASE 
                WHEN stprog_ass_sent = "0000-00-00" THEN NULL ELSE stprog_ass_sent
            END) as stprog_ass_sent'),
            DB::raw('(CASE 
                WHEN stprog_nego = "0000-00-00" THEN NULL ELSE stprog_nego
            END) as stprog_nego'),
            DB::raw('(CASE 
                WHEN reason_id = 0 THEN NULL ELSE reason_id
            END) as reason_id'),
            DB::raw('(CASE 
                WHEN stprog_test_date = "0000-00-00" THEN NULL ELSE stprog_test_date
            END) as stprog_test_date'),
            DB::raw('(CASE 
                WHEN stprog_last_class = "0000-00-00" THEN NULL ELSE stprog_last_class
            END) as stprog_last_class'),
            'stprog_diag_score',
            'stprog_test_score',
            'stprog_price_from_tutor',
            'stprog_our_price_tutor',
            'stprog_total_price_tutor',
            DB::raw('(CASE 
                WHEN stprog_duration = "" THEN NULL ELSE stprog_duration
            END) as stprog_duration'),
            'stprog_tot_uni',
            'stprog_tot_dollar',
            'stprog_kurs',
            'stprog_tot_idr',
            DB::raw('(CASE 
                WHEN stprog_install_plan = "" THEN NULL ELSE stprog_install_plan
            END) as stprog_install_plan'),
            'stprog_runningstatus',
            'stprog_start_date',
            'stprog_end_date',
            DB::raw('(CASE 
                WHEN empl_id = "" THEN NULL ELSE empl_id
            END) as empl_id'),
        ])->get();
    }
}