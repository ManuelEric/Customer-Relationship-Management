<?php

namespace App\Repositories;

use App\Interfaces\ClientProgramRepositoryInterface;
use App\Models\AcadTutorDetail;
use App\Models\Bundling;
use App\Models\BundlingDetail;
use App\Models\ClientProgram;
use App\Models\InvoiceProgram;
use App\Models\pivot\ClientMentor;
use App\Models\Reason;
use App\Models\Receipt;
use App\Models\School;
use App\Models\User;
use App\Models\UserClient;
use App\Models\v1\ClientProgram as CRMClientProgram;
use App\Models\ViewClientProgram;
use DataTables;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ClientProgramRepository implements ClientProgramRepositoryInterface
{
    public function getAllClientProgramDataTables($searchQuery = NULL)
    {
        # default 
        $fieldKey = ["success_date", "failed_date", "refund_date", "created_at"];

        # finding fieldKey that being searched
        # depends on status
        if (isset($searchQuery['status'])) {
            
            # reset fieldKey
            $fieldKey = [];
            
            foreach ($searchQuery['status'] as $key => $status) {

                switch ((int)$status) {
                    case 1: # success
                        $fieldKey[] = "success_date";
                        break;

                    case 2: # failed
                        $fieldKey[] = "failed_date";
                        break;

                    case 3: # refund
                        $fieldKey[] = "refund_date";
                        break;

                    default: # pending
                        $fieldKey = ["created_at"];
                }
            }
        }

        $model = ViewClientProgram::
                when(Session::get('user_role') == 'Employee', function ($subQuery) {
                    $subQuery->whereHas('internalPic', function ($query2) {
                        $query2->where('users.id', auth()->user()->id);
                    })->orWhere('pic_client', auth()->user()->id);
                })->
                when($searchQuery['clientId'], function ($query) use ($searchQuery) {
                    $query->where('client_id', $searchQuery['clientId']);
                })
                # search by main program 
                ->when(isset($searchQuery['mainProgram']) && count($searchQuery['mainProgram']) > 0, function ($query) use ($searchQuery) {
                    $query->whereIn('main_prog_id', $searchQuery['mainProgram']);
                })
                # search by program name 
                ->when(isset($searchQuery['programName']) && count($searchQuery['programName']) > 0, function ($query) use ($searchQuery) {
                    $query->whereIn('prog_id', $searchQuery['programName']);
                })
                # search by school name 
                ->when(isset($searchQuery['schoolName']), function ($query) use ($searchQuery) {
                    $query->whereIn('sch_id', $searchQuery['schoolName']);
                })
                # search by conversion lead
                ->when(isset($searchQuery['leadId']), function ($query) use ($searchQuery) {
                    $query->whereIn('lead_id', $searchQuery['leadId']);
                })
                # search by grade
                ->when(isset($searchQuery['grade']), function ($query) use ($searchQuery) {
                    if(in_array('not_high_school', $searchQuery['grade'])){
                        $key = array_search('not_high_school', $searchQuery['grade']);
                        unset($searchQuery["grade"][$key]);
                        count($searchQuery['grade']) > 0
                            ?
                                $query->where('grade_now', '>', 12)->orWhereIn('grade_now', $searchQuery['grade'])
                                    :
                                        $query->where('grade_now', '>', 12);
                    }else{
                        $query->whereIn('grade_now', $searchQuery['grade']);
                    }
                })
                # search by status
                ->when(isset($searchQuery['status']) && $searchQuery['status'] != null, function ($query) use ($searchQuery) {
                    $query->whereIn('status', $searchQuery['status']);
                })
                # search by date
                # when start date && end date filled
                ->when(isset($searchQuery['startDate']) && isset($searchQuery['endDate']), function ($query) use ($searchQuery, $fieldKey) {
                    $query->where(function ($subQuery) use ($searchQuery, $fieldKey) {

                        $no = 0;
                        foreach ($fieldKey as $key => $val) {
                            if ($no == 0)
                                $subQuery->whereBetween($val, [$searchQuery['startDate'], $searchQuery['endDate']]);
                            else
                                $subQuery->orWhereBetween($val, [$searchQuery['startDate'], $searchQuery['endDate']]);
    
                            $no++;
                        }
                    });
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
                # when start date null && end date filled
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
                # search by pic uuid
                ->when(isset($searchQuery['emplUUID']) && count($searchQuery['emplUUID']) > 0, function ($query) use ($searchQuery) {
                    $query->whereHas('internalPic', function ($query2) use ($searchQuery) {
                        $query2->whereIn('users.uuid', $searchQuery['emplUUID']);
                    });
                });

        return Datatables::eloquent($model)->
        // rawColumns(['strip_tag_notes'])->
        addColumn('is_bundle', function ($query) {
            return $query->bundlingDetail()->count();
        })->
        filterColumn(
            'status',
            function ($query, $keyword) {
                $sql = '(CASE 
                    WHEN status = 0 THEN "pending"
                    WHEN status = 1 THEN "success"
                    WHEN status = 2 THEN "failed"
                    WHEN status = 3 THEN "refund"
                END) like ?';
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
        )->filterColumn(
            'prog_running_status',
            function ($query, $keyword) {
                $sql = '(CASE 
                    WHEN prog_running_status = 0 THEN "not yet"
                    WHEN prog_running_status = 1 THEN "ongoing"
                    WHEN prog_running_status = 2 THEN "done"
                END) like ?';
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
        )->make(true);
    }

    public function getAllProgramOnClientProgram()
    {
        return ViewClientProgram::distinct('program_name')->select('program_name', 'prog_id')->get();
    }

    public function getAllMainProgramOnClientProgram()
    {
        return ViewClientProgram::distinct('main_prog_name')->select('main_prog_name', 'main_prog_id')->get();
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
        return ViewClientProgram::
                leftJoin('users', 'users.id', '=', 'clientprogram.empl_id')->
                distinct('empl_id')->
                select('empl_id', 'pic_name', 'users.uuid')->get();
    }

    public function getClientProgramById($clientProgramId)
    {
        return ClientProgram::whereClientProgramId($clientProgramId);
    }

    public function getClientProgramByClientId($clientId)
    {
        return ClientProgram::where('client_id', $clientId)->get();
    }

    public function getClientProgramByDetail(array $detail)
    {
        return ClientProgram::
                where('client_id', $detail['client_id'])->
                where('prog_id', $detail['prog_id'])->
                where('first_discuss_date', $detail['first_discuss_date'])->
                where('last_discuss_date', $detail['last_discuss_date'])->
                where('status', $detail['status'])->
                where('statusprog_date', $detail['statusprog_date'])->
                first();
    }

    public function createClientProgram($clientProgramDetails)
    {

        if (array_key_exists('reason_id', $clientProgramDetails) || array_key_exists('other_reason', $clientProgramDetails)) {

            if (isset($clientProgramDetails['other_reason'])) {

                $reason = Reason::create(
                    [
                        'reason_name' => $clientProgramDetails['other_reason'],
                        'type' => 'Program'
                    ]
                );
                $reasonId = $reason->reason_id;
                $clientProgramDetails['reason_id'] = $reasonId;
            }
        }

        if (array_key_exists('session_tutor', $clientProgramDetails)) {
            $howManySession = $clientProgramDetails['session_tutor'];
            $academicTutorSessionDetail = $clientProgramDetails['session_tutor_detail'];
            unset($clientProgramDetails['session_tutor_detail']);
        }

        $clientProgram = ClientProgram::create($clientProgramDetails);

        # when supervising_mentor and profile_building_mentor is filled which is not null
        # then assumed the user want to input "admission mentoring" program
        # do attach main mentor and backup mentor as client mentor
        if (array_key_exists('supervising_mentor', $clientProgramDetails) || array_key_exists('profile_building_mentor', $clientProgramDetails) || array_key_exists('aplication_strategy_mentor', $clientProgramDetails) || array_key_exists('writing_mentor', $clientProgramDetails)) {

            # if program end date was less than today 
            # then put status into 0 else 1
            // $status = (strtotime($clientProgramDetails['prog_end_date']) < strtotime(date('Y-m-d'))) ? 0 : 1; # status mentoring [0: inactive, 1: active]
            $status = 1;

            if (isset($clientProgramDetails['supervising_mentor'])) {

                # if program end date was less than today 
                # then put status into 0 else 1
                // $status = (strtotime($clientProgramDetails['prog_end_date']) < strtotime(date('Y-m-d'))) ? 0 : 1; # status mentoring [0: inactive, 1: active]
                $status = 1;
                $clientProgram->clientMentor()->attach($clientProgramDetails['supervising_mentor'], ['type' => 1, 'status' => $status]);
            }

            if (isset($clientProgramDetails['profile_building_mentor'])) {

                # if program end date was less than today 
                # then put status into 0 else 1
                // $status = (strtotime($clientProgramDetails['prog_end_date']) < strtotime(date('Y-m-d'))) ? 0 : 1; # status mentoring [0: inactive, 1: active]
                $status = 1;
                $clientProgram->clientMentor()->attach($clientProgramDetails['profile_building_mentor'], ['type' => 2, 'status' => $status]);
            }

            if (isset($clientProgramDetails['aplication_strategy_mentor'])) {

                # if program end date was less than today 
                # then put status into 0 else 1
                // $status = (strtotime($clientProgramDetails['prog_end_date']) < strtotime(date('Y-m-d'))) ? 0 : 1; # status mentoring [0: inactive, 1: active]
                $status = 1;
                $clientProgram->clientMentor()->attach($clientProgramDetails['aplication_strategy_mentor'], ['type' => 3, 'status' => $status]);
            }

            if (isset($clientProgramDetails['writing_mentor'])) {

                # if program end date was less than today 
                # then put status into 0 else 1
                // $status = (strtotime($clientProgramDetails['prog_end_date']) < strtotime(date('Y-m-d'))) ? 0 : 1; # status mentoring [0: inactive, 1: active]
                $status = 1;
                $clientProgram->clientMentor()->attach($clientProgramDetails['writing_mentor'], ['type' => 4, 'status' => $status]);
            }
        }

        # when mentor_ic is filled which is not null
        # then assumed the user want to input "admission mentoring" program
        # do attach mentor_ic
        if (array_key_exists('mentor_ic', $clientProgramDetails)){
            if(isset($clientProgramDetails['mentor_ic'])) {
                $clientProgram->mentorIC()->attach($clientProgramDetails['mentor_ic']);
            }
        }


        # when tutor id is filled which is not null
        # then assumed the user want to input "tutoring" program
        # do attach tutor as client mentor
        elseif (array_key_exists('tutor_id', $clientProgramDetails)) {

            # if program end date was less than today 
            # then put status into 0 else 1
            $status = (strtotime($clientProgramDetails['prog_end_date']) < strtotime(date('Y-m-d'))) ? 0 : 1; # status mentoring [0: inactive, 1: active]

            $clientProgram->clientMentor()->attach($clientProgramDetails['tutor_id'], ['type' => 5, 'status' => $status]);

            $clientprog_id = $clientProgram->clientprog_id;

            if (array_key_exists('session_tutor', $clientProgramDetails)) {
                # fetch the session schedule detail
                $i = 0;
                while ($i < $howManySession) {
                    # insert academic tutor detail
                    # insert academic tutor detail
                    $acadTutorDetail[] = new AcadTutorDetail([
                        'date' => date('Y-m-d', strtotime($academicTutorSessionDetail['datetime'][$i])),
                        'time' => date('H:i:s', strtotime($academicTutorSessionDetail['datetime'][$i])),
                        'link' => $academicTutorSessionDetail['linkmeet'][$i]
                    ]);
                    $i++;
                }

                $clientProgram->acadTutorDetail()->saveMany($acadTutorDetail);
            }
        }

        # when tutor_1 is filled which is not null
        # then assumed the user want to input "sat / act" program
        # do attach tutor_1 AND or OR tutor_2 as client mentor
        elseif (array_key_exists('tutor_1', $clientProgramDetails) || array_key_exists('tutor_2', $clientProgramDetails)) {

            # hardcode
            $status = 1;

            if (isset($clientProgramDetails['tutor_1']))
                $tutors['tutor_1'] = $clientProgramDetails['tutor_1'];
                $tutors['timesheet_1'] = $clientProgramDetails['timesheet_1'];
                $clientProgram->clientMentor()->attach($tutors['tutor_1'], ['type' => 5, 'status' => $status, 'timesheet_link' => $tutors['timesheet_1']]);
                
            if (isset($clientProgramDetails['tutor_2']))
                $tutors['tutor_2'] = $clientProgramDetails['tutor_2'];
                $tutors['timesheet_2'] = $clientProgramDetails['timesheet_2'];
                $clientProgram->clientMentor()->attach($tutors['tutor_2'], ['type' => 5, 'status' => $status, 'timesheet_link' => $tutors['timesheet_2']]);
            


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

        if (array_key_exists('supervising_mentor', $clientProgramDetails) || array_key_exists('profile_building_mentor', $clientProgramDetails) || array_key_exists('aplication_strategy_mentor', $clientProgramDetails) || array_key_exists('writing_mentor', $clientProgramDetails)) {

            if (isset($clientProgramDetails['supervising_mentor'])) {

                $additionalDetails['supervising_mentor'] =  $clientProgramDetails['supervising_mentor'];
            }

            if (isset($clientProgramDetails['profile_building_mentor'])) {

                $additionalDetails['profile_building_mentor'] =  $clientProgramDetails['profile_building_mentor'];
            }
            
            if (isset($clientProgramDetails['aplication_strategy_mentor'])) {

                $additionalDetails['aplication_strategy_mentor'] =  $clientProgramDetails['aplication_strategy_mentor'];
            }

            if (isset($clientProgramDetails['writing_mentor'])) {

                $additionalDetails['writing_mentor'] =  $clientProgramDetails['writing_mentor'];
            }

            unset($clientProgramDetails['supervising_mentor']);
            unset($clientProgramDetails['profile_building_mentor']);
            unset($clientProgramDetails['aplication_strategy_mentor']);
            unset($clientProgramDetails['writing_mentor']);
        }


        if (array_key_exists('tutor_id', $clientProgramDetails)) {

            $additionalDetails['tutor_id'] = $clientProgramDetails['tutor_id'];

            unset($clientProgramDetails['tutor_id']);
        } 
        
        if (array_key_exists('tutor_1', $clientProgramDetails) || array_key_exists('tutor_2', $clientProgramDetails)) {

            $additionalDetails['tutor_1'] = $clientProgramDetails['tutor_1'];
            $additionalDetails['tutor_2'] = $clientProgramDetails['tutor_2'];
            $additionalDetails['timesheet_1'] = $clientProgramDetails['timesheet_1'];
            $additionalDetails['timesheet_2'] = $clientProgramDetails['timesheet_2'];

            unset($clientProgramDetails['tutor_1']);
            unset($clientProgramDetails['tutor_2']);
            unset($clientProgramDetails['timesheet_1']);
            unset($clientProgramDetails['timesheet_2']);
        } 
        
        if (array_key_exists('mentor_ic', $clientProgramDetails)){

            $additionalDetails['mentor_ic'] = $clientProgramDetails['mentor_ic'];

            unset($clientProgramDetails['mentor_ic']);
        }

        if (array_key_exists('reason_id', $clientProgramDetails) || array_key_exists('other_reason', $clientProgramDetails)) {

            if (isset($clientProgramDetails['other_reason'])) {

                if (!$reason = Reason::where('reason_name', $clientProgramDetails['other_reason'])->first()) {

                    $reason = Reason::create(
                        [
                            'reason_name' => $clientProgramDetails['other_reason'],
                            'type' => 'Program'
                        ]
                    );
                }

                $reasonId = $reason->reason_id;
                $clientProgramDetails['reason_id'] = $reasonId;
            }
            unset($clientProgramDetails['other_reason']);
        }

        if (array_key_exists('session_tutor', $clientProgramDetails)) {
            $howManySession = $clientProgramDetails['session_tutor'];
            $academicTutorSessionDetail = $clientProgramDetails['session_tutor_detail'];
            unset($clientProgramDetails['session_tutor_detail']);
        }

        $clientProgram = ClientProgram::where('clientprog_id', $clientProgramId)->update(array_merge($fullDetails, $clientProgramDetails));
        $clientProgram = ClientProgram::whereClientProgramId($clientProgramId);

        # delete the client mentor when status client program changed to pending
        if ($clientProgram->status == 0 && $clientProgram->clientMentor()->count() > 0) {

            $mentorsId = $clientProgram->clientMentor()->pluck('users.id')->toArray();
            $clientProgram->clientMentor()->detach($mentorsId);
        }
  
        # when supervising_mentor and profile_building_mentor is filled which is not null
        # then assumed the user want to input "admission mentoring" program
        # do attach main mentor and backup mentor as client mentor
        if (array_key_exists('supervising_mentor', $additionalDetails) || array_key_exists('profile_building_mentor', $additionalDetails) || array_key_exists('aplication_strategy_mentor', $additionalDetails) || array_key_exists('writing_mentor', $additionalDetails)) {
            $mentorInfo = [];

            # if program end date was less than today 
            # then put status into 0 else 1
            $status = (strtotime($clientProgramDetails['prog_end_date']) < strtotime(date('Y-m-d'))) ? 0 : 1; # status mentoring [0: inactive, 1: active]     
            if (array_key_exists('supervising_mentor', $additionalDetails)) {
                $mentorInfo[]=[
                    'user_id' => $additionalDetails['supervising_mentor'],
                    'type' => 1,
                ];

            }

            if (array_key_exists('profile_building_mentor', $additionalDetails)) {
                $mentorInfo[]=[
                    'user_id' => $additionalDetails['profile_building_mentor'],
                    'type' => 2,
                ];
            }

            if (array_key_exists('aplication_strategy_mentor', $additionalDetails)) {
                $mentorInfo[]=[
                    'user_id' => $additionalDetails['aplication_strategy_mentor'],
                    'type' => 3,
                ];
                $clientProgram->clientMentor()->updateExistingPivot($additionalDetails['aplication_strategy_mentor'], ['type' => 3, 'status' => $status]); # Aplication strategy mentor
            }

            if (array_key_exists('writing_mentor', $additionalDetails)) {
                $mentorInfo[]=[
                    'user_id' => $additionalDetails['writing_mentor'],
                    'type' => 4,
                ];
                $clientProgram->clientMentor()->updateExistingPivot($additionalDetails['writing_mentor'], ['type' => 4, 'status' => $status]); # Writing mentor
            }

            if(count($mentorInfo) > 0)
                $clientProgram->clientMentor()->sync($mentorInfo, ['status' => $status]);
        }

        # when tutor id is filled which is not null
        # then assumed the user want to input "tutoring" program
        # do attach tutor as client mentor
        if (array_key_exists('tutor_id', $additionalDetails)) {

            # if program end date was less than today 
            # then put status into 0 else 1
            $status = (strtotime($clientProgramDetails['prog_end_date']) < strtotime(date('Y-m-d'))) ? 0 : 1; # status mentoring [0: inactive, 1: active]

            $clientProgram->clientMentor()->syncWithPivotValues($additionalDetails['tutor_id'], ['status' => $status]);

            $clientprog_id = $clientProgram->clientprog_id;
            if (array_key_exists('session_tutor', $clientProgramDetails)) {

                # fetch the session schedule detail
                $i = 0;
                while ($i < $howManySession) {
                    # insert academic tutor detail
                    $acadTutorDetail[] = new AcadTutorDetail([
                        'date' => date('Y-m-d', strtotime($academicTutorSessionDetail['datetime'][$i])),
                        'time' => date('H:i:s', strtotime($academicTutorSessionDetail['datetime'][$i])),
                        'link' => $academicTutorSessionDetail['linkmeet'][$i]
                    ]);
                    $i++;
                }
    
                $clientProgram->acadTutorDetail()->delete();
                $clientProgram->acadTutorDetail()->saveMany($acadTutorDetail);
            }
        }

        # when tutor_1 is filled which is not null
        # then assumed the user want to input "sat / act" program
        # do attach tutor_1 AND or OR tutor_2 as client mentor
        if (array_key_exists('tutor_1', $additionalDetails) || array_key_exists('tutor_2', $additionalDetails)) {

            # hardcode
            $status = 1;

            $tutorInfo = [];
            if (isset($additionalDetails['tutor_1'])){
                $tutors['tutor_1'] = $additionalDetails['tutor_1'];
                $tutors['timesheet_1'] = $additionalDetails['timesheet_1'];
                $tutorInfo[]=[
                    'user_id' => $tutors['tutor_1'],
                    'type' => 5,
                    'timesheet_link' => $tutors['timesheet_1'],
                ];
            }
               
                // $clientProgram->clientMentor()->syncWithPivotValues($tutors['tutor_1'], ['status' => $status, 'timesheet_link' => $tutors['timesheet_1']]);
                
            if (isset($additionalDetails['tutor_2'])){
                $tutors['tutor_2'] = $additionalDetails['tutor_2'];
                $tutors['timesheet_2'] = $additionalDetails['timesheet_2'];
                $tutorInfo[]=[
                    'user_id' => $tutors['tutor_2'],
                    'type' => 5,
                    'timesheet_link' => $tutors['timesheet_2'],
                ];
            }
             
            if(count($tutorInfo) > 0)
                $clientProgram->clientMentor()->sync($tutorInfo, ['status' => $status]);
        }

        # when mentor_ic is filled which is not null
        # then assumed the user want to input "admission mentoring" program
        # do attach mentor_ic
        if (array_key_exists('mentor_ic', $additionalDetails)){
            if(isset($additionalDetails['mentor_ic'])) {
                $clientProgram->mentorIC()->sync($additionalDetails['mentor_ic']);
            }
        }


        return $clientProgram;
    }

    public function updateFewField(int $clientprog_id, array $newCDetails)
    {
        return ClientProgram::whereClientProgramId($clientprog_id)->update($newCDetails);
    }

    public function endedClientProgram(int $clientprog_id, array $newDetails)
    {
        return ClientProgram::whereClientProgramId($clientprog_id)->update($newDetails);
    }

    public function endedClientPrograms(array $clientprog_ids, array $newDetails)
    {
        return ClientProgram::whereIn('clientprog_id', $clientprog_ids)->update($newDetails);
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
    public function getCountProgramByStatus($status, array $dateDetails, array $additionalFilter = [])
    {
        # array of additional filter is filled with [mainProg, progName, pic]
        $mainProg = $additionalFilter['mainProg']; # filled with id main prog
        $progName = $additionalFilter['progName']; # filled with id
        $pic = $additionalFilter['pic']; # filled with id employee

        $searched_column = $this->getSearchedColumn($status);        
        $statusId = $this->getStatusId($status);
      
        return ClientProgram::has('cleanClient')->
            where('status', $statusId)->
            whereBetween($searched_column, [$dateDetails['startDate'], $dateDetails['endDate']])->
            when($mainProg, function ($query) use ($mainProg) {
                $query->whereHas('program.main_prog', function ($subQuery) use ($mainProg) {
                    $subQuery->where('id', $mainProg);
                });
            })->
            when($progName, function ($query) use ($progName) {
                $query->where('prog_id', $progName);
            })->
            when($pic, function ($query) use ($pic) {
                # check the client pic
                $query->where(function ($sq_1) use ($pic) {
                    $sq_1->whereHas('client', function ($sq_2) use ($pic) {
                        $sq_2->whereHas('handledBy', function ($sq_3) use ($pic) {
                            $sq_3->where('users.id', $pic);
                        });
                    })->
                    # and check the pic client program
                    orWhere('empl_id', $pic);
                });
            })->
            count();
    }

    # function below has same function as above function
    # so if there's a changes between one of them, make sure to do it to another
    public function getSummaryProgramByStatus($status, array $dateDetails, array $additionalFilter = [])
    {
        # array of additional filter is filled with [mainProg, progName, pic]
        $mainProg = $additionalFilter['mainProg']; # filled with id main prog
        $progName = $additionalFilter['progName']; # filled with id
        $pic = $additionalFilter['pic']; # filled with id employee

        $searched_column = $this->getSearchedColumn($status);
        $statusId = $this->getStatusId($status);

        $clientPrograms = ClientProgram::has('cleanClient')->
            where('status', $statusId)->
            whereBetween($searched_column, [$dateDetails['startDate'], $dateDetails['endDate']])->
            when($mainProg, function ($query) use ($mainProg) {
                $query->whereHas('program.main_prog', function ($subQuery) use ($mainProg) {
                    $subQuery->where('id', $mainProg);
                });
            })->
            when($progName, function ($query) use ($progName) {
                $query->where('prog_id', $progName);
            })->
            when($pic, function ($query) use ($pic) {
                # check the client pic
                $query->where(function ($sq_1) use ($pic) {
                    $sq_1->whereHas('client', function ($sq_2) use ($pic) {
                        $sq_2->whereHas('handledBy', function ($sq_3) use ($pic) {
                            $sq_3->where('users.id', $pic);
                        });
                    })->
                    # and check the pic client program
                    orWhere('empl_id', $pic);
                });
            })->
            get();

        $no = 0;
        $data = [];
        foreach ($clientPrograms as $mainProg) {
            $data[$mainProg->program->main_prog->prog_name][$mainProg->program->prog_program][$mainProg->program->prog_id][] = $no++;
        }

        return $data;
    }

    public function getInitAssessmentProgress($dateDetails, $additionalFilter = []) # startDate, endDate
    {
        # array of additional filter is filled with [mainProg, progName, pic]
        $mainProg = $additionalFilter['mainProg']; # filled with id main prog
        $progName = $additionalFilter['progName']; # filled with id
        $pic = $additionalFilter['pic']; # filled with id employee

        $IC_query = "SELECT COUNT(*) FROM tbl_client_prog scp 
                LEFT JOIN tbl_client c ON c.id = scp.client_id
                LEFT JOIN tbl_pic_client pc ON pc.client_id = c.id
                WHERE scp.prog_id = tbl_client_prog.prog_id
                AND scp.created_at BETWEEN '".$dateDetails['startDate']."' AND '".$dateDetails['endDate']."'
                AND (CASE 
                    WHEN scp.initconsult_date IS NOT NULL THEN scp.initconsult_date
                    ELSE scp.first_discuss_date
                END) IS NOT NULL AND scp.status = 1";

        $Success_query = "SELECT COUNT(*) FROM tbl_client_prog scp 
                LEFT JOIN tbl_client c ON c.id = scp.client_id
                LEFT JOIN tbl_pic_client pc ON pc.client_id = c.id
                WHERE scp.prog_id = tbl_client_prog.prog_id
                AND scp.created_at BETWEEN '".$dateDetails['startDate']."' AND '".$dateDetails['endDate']."'
                AND scp.success_date IS NOT NULL AND scp.status = 1";

        if ($pic) {
            $IC_query .= " AND (pc.user_id = ".$pic." OR empl_id = ".$pic.")";
            $Success_query .= " AND (pc.user_id = ".$pic." OR empl_id = ".$pic.")";
        }

        return ClientProgram::
            has('cleanClient')->
            leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'tbl_client_prog.prog_id')
            ->leftJoin('tbl_main_prog', 'tbl_main_prog.id', '=', 'tbl_prog.main_prog_id')
            ->select([
                'tbl_client_prog.prog_id',
                DB::raw("(".$IC_query.") AS IC"),
                DB::raw("(".$Success_query.") AS success" ),
                DB::raw('AVG(DATEDIFF(assessmentsent_date, initconsult_date)) as initialMaking'),
                DB::raw('CONCAT(tbl_main_prog.prog_name, ": ", tbl_prog.prog_program) as program_name_st'),
                DB::raw('AVG(DATEDIFF(success_date, assessmentsent_date)) as converted'),
            ])
            ->whereHas('program', function ($query) {
                $query->whereHas('main_prog', function ($query2) {
                    $query2->where('prog_name', 'like', '%Admissions Mentoring%');
                })->orWhereHas('sub_prog', function ($query2) {
                    $query2->where('sub_prog_name', 'like', '%Admissions Mentoring%');
                });
            })
            ->where('status', 1)
            ->whereBetween('tbl_client_prog.success_date', [$dateDetails['startDate'], $dateDetails['endDate']])->
            
            # added new features
            # filter by main prog 
            // when($mainProg, function ($query) use ($mainProg) {
            //     $query->whereHas('program.main_prog', function ($subQuery) use ($mainProg) {
            //         $subQuery->where('id', $mainProg);
            //     });
            // })->
            // # filter by prog Id
            // when($progName, function ($query) use ($progName) {
            //     $query->where('tbl_prog.prog_id', $progName);
            // })->
            # filter by pic
            when($pic, function ($query) use ($pic) {
                # check the client pic
                $query->where(function ($sq_1) use ($pic) {
                    $sq_1->whereHas('client', function ($sq_2) use ($pic) {
                        $sq_2->whereHas('handledBy', function ($sq_3) use ($pic) {
                            $sq_3->where('users.id', $pic);
                        });
                    })->
                    # and check the pic client program
                    orWhere('empl_id', $pic);
                });
            })
            ->groupBy('program_name_st')
            ->get();
    }

    public function getLeadSource($dateDetails, $cp_filter = null)
    {
        $userId = $this->getUser($cp_filter);

        return ClientProgram::
            leftJoin('tbl_client', 'tbl_client.id', '=', 'tbl_client_prog.client_id')
            ->leftJoin('tbl_lead', 'tbl_lead.lead_id', '=', 'tbl_client.lead_id')
            ->leftJoin('tbl_eduf_lead', 'tbl_eduf_lead.id', '=', 'tbl_client.eduf_id')
            ->leftJoin('tbl_sch', 'tbl_sch.sch_id', '=', 'tbl_eduf_lead.sch_id')
            ->leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', 'tbl_eduf_lead.corp_id')
            ->leftJoin('tbl_events', 'tbl_events.event_id', 'tbl_client.event_id')
            ->select([
                'tbl_lead.lead_id',
                'color_code',
                DB::raw('(CASE 
                    WHEN tbl_lead.main_lead = "KOL" THEN CONCAT("KOL: ", tbl_lead.sub_lead)
                    WHEN tbl_lead.main_lead = "External Edufair" THEN CONCAT("External Edufair: ", 
                        (CASE 
                            WHEN tbl_eduf_lead.title IS NULL THEN 
                                (CASE
                                    WHEN tbl_eduf_lead.sch_id IS NOT NULL THEN tbl_sch.sch_name
                                    ELSE tbl_corp.corp_name
                                END)
                            ELSE tbl_eduf_lead.title
                        END)    
                    )
                    WHEN tbl_lead.main_lead = "All-In Event" THEN CONCAT("All-In Event: ", tbl_events.event_title)
                    ELSE tbl_lead.main_lead
                END) AS lead_source'),
                DB::raw('COUNT((CASE 
                    WHEN tbl_lead.main_lead = "KOL" THEN CONCAT("KOL: ", tbl_lead.sub_lead)
                    WHEN tbl_lead.main_lead = "External Edufair" THEN CONCAT("External Edufair: ", 
                        (CASE 
                            WHEN tbl_eduf_lead.title IS NULL THEN 
                                (CASE
                                    WHEN tbl_eduf_lead.sch_id IS NOT NULL THEN tbl_sch.sch_name
                                    ELSE tbl_corp.corp_name
                                END)
                            ELSE tbl_eduf_lead.title
                        END)
                    )
                    WHEN tbl_lead.main_lead = "All-In Event" THEN CONCAT("All-In Event: ", tbl_events.event_title)
                    ELSE tbl_lead.main_lead
                END)) AS lead_source_count'),
                DB::raw('(CASE 
                        WHEN tbl_lead.main_lead = "External Edufair" THEN (CASE
                            WHEN tbl_eduf_lead.sch_id IS NOT NULL THEN tbl_sch.sch_id
                            ELSE tbl_eduf_lead.corp_id
                        END)
                        WHEN tbl_lead.main_lead = "All-In Event" THEN tbl_events.event_id
                    ELSE NULL
                END) AS sub_lead_id'),
            ])
            ->when(isset($cp_filter['quuid']), function ($q) use ($userId) {
                $q->where('empl_id', $userId);
            })
            ->where('tbl_client.deleted_at', null)
            ->where('tbl_client_prog.status', 1)
            ->when(isset($cp_filter['qdate']), function ($q) use ($cp_filter) {
                $q->whereMonth('success_date', date('m', strtotime($cp_filter['qdate'])))->whereYear('success_date', date('Y', strtotime($cp_filter['qdate'])));
            })
            ->when(!empty($dateDetails), function ($q) use ($dateDetails) {
                // $q->whereBetween('tbl_client_prog.created_at', [$dateDetails['startDate'], $dateDetails['endDate']]);
                $q->whereBetween(DB::raw('
                    (CASE
                        WHEN tbl_client_prog.status = 0 THEN tbl_client_prog.created_at
                        WHEN tbl_client_prog.status = 1 THEN tbl_client_prog.success_date
                        WHEN tbl_client_prog.status = 2 THEN tbl_client_prog.failed_date
                        WHEN tbl_client_prog.status = 3 THEN tbl_client_prog.refund_date
                    END)
                '), [$dateDetails['startDate'], $dateDetails['endDate']]);
            })
            
            ->groupBy('lead_source')
            ->get();
    }

    public function getLeadSourceDetails($filter)
    {
        return ClientProgram::leftJoin('tbl_client', 'tbl_client.id', '=', 'tbl_client_prog.client_id')
            ->leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'tbl_client_prog.prog_id')
            ->leftJoin('tbl_lead', 'tbl_lead.lead_id', '=', 'tbl_client.lead_id')
            ->leftJoin('tbl_eduf_lead', 'tbl_eduf_lead.id', '=', 'tbl_client.eduf_id')
            ->leftJoin('tbl_events', 'tbl_events.event_id', 'tbl_client.event_id')
            ->select([
                'tbl_client_prog.clientprog_id',
                'tbl_prog.*',
                'tbl_client.*',
                'tbl_lead.lead_id',
                'color_code',
                DB::raw('(CASE 
                    WHEN tbl_lead.main_lead = "KOL" THEN CONCAT("KOL: ", tbl_lead.sub_lead)
                    WHEN tbl_lead.main_lead = "External Edufair" THEN CONCAT("External Edufair: ", tbl_eduf_lead.title)
                    WHEN tbl_lead.main_lead = "All-In Event" THEN CONCAT("All-In Event: ", tbl_events.event_title)
                    ELSE tbl_lead.main_lead
                END) AS lead_source'),
            ])
            ->where('tbl_client.deleted_at', null)
            ->where('tbl_client_prog.status', 1)
            ->where('tbl_client.deleted_at', null)
            ->when($filter, function ($q) use ($filter) {
                $q->whereBetween(DB::raw('
                    (CASE
                        WHEN tbl_client_prog.status = 0 THEN tbl_client_prog.created_at
                        WHEN tbl_client_prog.status = 1 THEN tbl_client_prog.success_date
                        WHEN tbl_client_prog.status = 2 THEN tbl_client_prog.failed_date
                        WHEN tbl_client_prog.status = 3 THEN tbl_client_prog.refund_date
                    END)
                '), [$filter['startDate'], $filter['endDate']]);
            })
            ->where('tbl_lead.lead_id', $filter['leadId'])
            ->when($filter['subLead'], function ($q) use ($filter) {
                $q->
                # if lead Id was External Edufair, ID : LS017
                when($filter['leadId'] == 'LS017', function ($q2) use ($filter) {
                    $q2->where('tbl_eduf_lead.sch_id', $filter['subLead']);
                })->
                when($filter['leadId'] == 'LS003', function ($q2) use ($filter) {
                    $q2->where('tbl_events.event_id', $filter['subLead']);
                });
            })
            # added new features
            # filter by main prog 
            ->when(isset($filter['mainProgId']), function ($q) use ($filter) {
                $q->where('tbl_prog.main_prog_id', $filter['mainProgId']);  
            })
            # filter by program id
            ->when(isset($filter['progId']), function ($q) use ($filter) {
                $q->where('tbl_prog.prog_id', $filter['progId']);
            })
            # filter by pic
            ->when(isset($filter['picUUID']), function ($q) use ($filter) {
                $picId = User::where('uuid', $filter['picUUID'])->first()->id;

                $q->
                    where('tbl_client_prog.empl_id', $picId)->
                    orWhereHas('client.handledBy', function ($q2) use ($picId) {
                        $q2->where('id', $picId);
                    });
            })
            ->get();
    }

    public function getConversionLead($dateDetails, $cp_filter = null)
    {
        $userId = $this->getUser($cp_filter);
        $program = isset($cp_filter['prog']) ? $cp_filter['prog'] : null;

        return ClientProgram::has('cleanClient')->leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'tbl_client_prog.prog_id')
            ->leftJoin('tbl_lead', 'tbl_lead.lead_id', '=', 'tbl_client_prog.lead_id')
            ->leftJoin('tbl_eduf_lead', 'tbl_eduf_lead.id', '=', 'tbl_client_prog.eduf_lead_id')
            ->leftJoin('tbl_sch', 'tbl_sch.sch_id', '=', 'tbl_eduf_lead.sch_id')
            ->leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', 'tbl_client_prog.partner_id')
            ->leftJoin('tbl_client_event', 'tbl_client_event.clientevent_id', '=', 'tbl_client_prog.clientevent_id')
            ->leftJoin('tbl_events', 'tbl_events.event_id', '=', 'tbl_client_event.event_id')
            ->select([
                'tbl_lead.lead_id',
                'color_code',
                DB::raw('(CASE 
                    WHEN tbl_lead.main_lead = "KOL" THEN CONCAT("KOL: ", tbl_lead.sub_lead)
                    WHEN tbl_lead.main_lead = "External Edufair" THEN CONCAT("External Edufair: ", 
                        (CASE 
                            WHEN tbl_eduf_lead.title IS NULL THEN 
                                (CASE
                                    WHEN tbl_eduf_lead.sch_id IS NOT NULL THEN tbl_sch.sch_name
                                    ELSE tbl_corp.corp_name
                                END)
                            ELSE tbl_eduf_lead.title
                        END)    
                    )
                    WHEN tbl_lead.main_lead = "All-In Event" THEN CONCAT("All-In Event: ", tbl_events.event_title)
                    ELSE tbl_lead.main_lead
                END) AS conversion_lead'),
                DB::raw('COUNT((CASE 
                    WHEN tbl_lead.main_lead = "KOL" THEN CONCAT("KOL: ", tbl_lead.sub_lead)
                    WHEN tbl_lead.main_lead = "External Edufair" THEN CONCAT("External Edufair: ", 
                        (CASE 
                            WHEN tbl_eduf_lead.title IS NULL THEN 
                                (CASE
                                    WHEN tbl_eduf_lead.sch_id IS NOT NULL THEN tbl_sch.sch_name
                                    ELSE tbl_corp.corp_name
                                END)
                            ELSE tbl_eduf_lead.title
                        END)
                    )
                    WHEN tbl_lead.main_lead = "All-In Event" THEN CONCAT("All-In Event: ", tbl_events.event_title)
                    ELSE tbl_lead.main_lead
                END)) AS conversion_lead_count'),
                DB::raw('(CASE 
                        WHEN tbl_lead.main_lead = "External Edufair" THEN (CASE
                            WHEN tbl_eduf_lead.sch_id IS NOT NULL THEN tbl_sch.sch_id
                            ELSE tbl_eduf_lead.corp_id
                        END)
                        WHEN tbl_lead.main_lead = "All-In Event" THEN tbl_events.event_id
                    ELSE NULL
                END) AS sub_lead_id'),
            ])
            ->when(isset($cp_filter['quuid']), function ($q) use ($userId) {
                $q->where('empl_id', $userId);
            })
            ->when($program, function ($query) use ($program) {
                $query->whereHas('program', function ($query) use ($program) {
                    $query->whereHas('main_prog', function ($query2) use ($program) {
                        $query2->where('prog_name', 'like', '%' . $program . '%');
                    })->orWhereHas('sub_prog', function ($query2) use ($program) {
                        $query2->where('sub_prog_name', 'like', '%' . $program . '%');
                    });
                });
            })
            ->where('tbl_client_prog.status', 1)
            ->when(isset($cp_filter['qdate']), function ($q) use ($cp_filter) {
                $q->whereMonth('success_date', date('m', strtotime($cp_filter['qdate'])))->whereYear('success_date', date('Y', strtotime($cp_filter['qdate'])));
            })
            ->when(!empty($dateDetails), function ($q) use ($dateDetails) {
                $q->whereBetween(DB::raw('
                    (CASE
                        WHEN tbl_client_prog.status = 0 THEN tbl_client_prog.created_at
                        WHEN tbl_client_prog.status = 1 THEN tbl_client_prog.success_date
                        WHEN tbl_client_prog.status = 2 THEN tbl_client_prog.failed_date
                        WHEN tbl_client_prog.status = 3 THEN tbl_client_prog.refund_date
                    END)
                '), [$dateDetails['startDate'], $dateDetails['endDate']]);
            })
            ->groupBy('conversion_lead')
            ->get();
    }

    public function getConversionLeadDetails($filter)
    {
        return ClientProgram::has('cleanClient')->leftJoin('tbl_client', 'tbl_client.id', '=', 'tbl_client_prog.client_id')
            ->leftJoin('tbl_lead as lc', 'lc.lead_id', '=', 'tbl_client.lead_id')
            ->leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'tbl_client_prog.prog_id')
            ->leftJoin('tbl_main_prog', 'tbl_main_prog.id', '=', 'tbl_prog.main_prog_id')
            ->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')
            ->leftJoin('tbl_lead as l', 'l.lead_id', '=', 'tbl_client_prog.lead_id')
            ->leftJoin('tbl_eduf_lead', 'tbl_eduf_lead.id', '=', 'tbl_client_prog.eduf_lead_id')
            ->leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', 'tbl_client_prog.partner_id')
            ->leftJoin('tbl_client_event', 'tbl_client_event.clientevent_id', '=', 'tbl_client_prog.clientevent_id')
            ->leftJoin('tbl_events', 'tbl_events.event_id', '=', 'tbl_client_event.event_id')
            ->select([
                'tbl_prog.*',
                'tbl_client.*',
                'l.lead_id',
                'lc.main_lead as lead_source',
                'l.color_code',
                DB::raw('(CASE 
                    WHEN l.main_lead = "KOL" THEN CONCAT("KOL: ", l.sub_lead)
                    WHEN l.main_lead = "External Edufair" THEN CONCAT("External Edufair: ", tbl_eduf_lead.title)
                    WHEN l.main_lead = "All-In Event" THEN CONCAT("All-In Event: ", tbl_events.event_title)
                    ELSE l.main_lead
                END) AS conversion_lead'),
            ])
            ->where('tbl_client_prog.status', 1)
            ->where('tbl_client.deleted_at', null)
            ->when(isset($filter['startDate']) && isset($filter['endDate']), function ($q) use ($filter) {
                $q->whereBetween(DB::raw('
                    (CASE
                        WHEN tbl_client_prog.status = 0 THEN tbl_client_prog.created_at
                        WHEN tbl_client_prog.status = 1 THEN tbl_client_prog.success_date
                        WHEN tbl_client_prog.status = 2 THEN tbl_client_prog.failed_date
                        WHEN tbl_client_prog.status = 3 THEN tbl_client_prog.refund_date
                    END)
                '), [$filter['startDate'], $filter['endDate']]);
            })
            ->where('l.lead_id', $filter['leadId'])
            ->when($filter['subLead'], function ($q) use ($filter) {
                $q->
                # if lead Id was External Edufair, ID : LS017
                when($filter['leadId'] == 'LS017', function ($q2) use ($filter) {
                    $q2->where('tbl_eduf_lead.sch_id', $filter['subLead']);
                })->
                when($filter['leadId'] == 'LS003', function ($q2) use ($filter) {
                    $q2->where('tbl_events.event_id', $filter['subLead']);
                });
            })
            ->get();
    }

    public function getConversionTimeSuccessfulPrograms($dateDetails)
    {
        return ClientProgram::has('cleanClient')->leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'tbl_client_prog.prog_id')
            ->leftJoin('tbl_main_prog', 'tbl_main_prog.id', '=', 'tbl_prog.main_prog_id')
            ->select([
                DB::raw('CONCAT(tbl_main_prog.prog_name, ": ", tbl_prog.prog_program) as program_name_st'),
                DB::raw('(CASE 
                    WHEN tbl_main_prog.prog_name = "Admissions Mentoring" THEN AVG(DATEDIFF(success_date, initconsult_date))
                    ELSE AVG(DATEDIFF(success_date, first_discuss_date))
                END) AS average_time'),
            ])
            ->whereHas('program', function ($query) {
                $query->whereHas('main_prog', function ($query2) {
                    $query2->where('prog_name', 'like', '%Admissions Mentoring%');
                })->whereHas('sub_prog', function ($query2) {
                    $query2->where('sub_prog_name', 'like', '%Admissions Mentoring%');
                });
            })
            ->where('status', 1)
            ->whereBetween('tbl_client_prog.success_date', [$dateDetails['startDate'], $dateDetails['endDate']])
            ->groupBy('program_name_st')
            ->get();
    }

    # dashboard
    public function getClientProgramGroupByStatusAndUserArray($cp_filter)
    {
        $userId = $this->getUser($cp_filter);

        $data[0] = ClientProgram::when($cp_filter['program'], function ($q) use ($cp_filter) {
            $q->whereHas('program', function ($q2) use ($cp_filter) {
                $q2->whereHas('main_prog', function ($q3) use ($cp_filter) {
                    $q3->where('prog_name', $cp_filter['program']);
                })->orWhereHas('sub_prog', function ($q3) use ($cp_filter) {
                    $q3->where('sub_prog_name', $cp_filter['program']);
                });
            });
        })->when(isset($cp_filter['quuid']), function ($q) use ($userId) {
            $q->where('empl_id', $userId);
        })->where('status', 0)->when($cp_filter['qdate'], function ($q) use ($cp_filter) {
            $q->whereMonth('created_at', date('m', strtotime($cp_filter['qdate'])))->whereYear('created_at', date('Y', strtotime($cp_filter['qdate'])));
        })->count();

        $data[1] = ClientProgram::when($cp_filter['program'], function ($q) use ($cp_filter) {
            $q->whereHas('program', function ($q2) use ($cp_filter) {
                $q2->whereHas('main_prog', function ($q3) use ($cp_filter) {
                    $q3->where('prog_name', $cp_filter['program']);
                })->orWhereHas('sub_prog', function ($q3) use ($cp_filter) {
                    $q3->where('sub_prog_name', $cp_filter['program']);
                });
            });
        })->when(isset($cp_filter['quuid']), function ($q) use ($userId) {
            $q->where('empl_id', $userId);
        })->where('status', 2)->when($cp_filter['qdate'], function ($q) use ($cp_filter) {
            $q->whereMonth('failed_date', date('m', strtotime($cp_filter['qdate'])))->whereYear('failed_date', date('Y', strtotime($cp_filter['qdate'])));
        })->count(); # failed
        $data[2] = ClientProgram::when($cp_filter['program'], function ($q) use ($cp_filter) {
            $q->whereHas('program', function ($q2) use ($cp_filter) {
                $q2->whereHas('main_prog', function ($q3) use ($cp_filter) {
                    $q3->where('prog_name', $cp_filter['program']);
                })->orWhereHas('sub_prog', function ($q3) use ($cp_filter) {
                    $q3->where('sub_prog_name', $cp_filter['program']);
                });
            });
        })->when(isset($cp_filter['quuid']), function ($q) use ($userId) {
            $q->where('empl_id', $userId);
        })->where('status', 1)->when($cp_filter['qdate'], function ($q) use ($cp_filter) {
            $q->whereMonth('success_date', date('m', strtotime($cp_filter['qdate'])))->whereYear('success_date', date('Y', strtotime($cp_filter['qdate'])));
        })->count(); # success
        $data[3] = ClientProgram::when($cp_filter['program'], function ($q) use ($cp_filter) {
            $q->whereHas('program', function ($q2) use ($cp_filter) {
                $q2->whereHas('main_prog', function ($q3) use ($cp_filter) {
                    $q3->where('prog_name', $cp_filter['program']);
                })->orWhereHas('sub_prog', function ($q3) use ($cp_filter) {
                    $q3->where('sub_prog_name', $cp_filter['program']);
                });
            });
        })->when(isset($cp_filter['quuid']), function ($q) use ($userId) {
            $q->where('empl_id', $userId);
        })->where('status', 3)->when($cp_filter['qdate'], function ($q) use ($cp_filter) {
            $q->whereMonth('refund_date', date('m', strtotime($cp_filter['qdate'])))->whereYear('refund_date', date('Y', strtotime($cp_filter['qdate'])));
        })->count(); # refund

        return $data;
    }

    public function getClientProgramGroupDataByStatusAndUserArray($cp_filter)
    {
        $userId = $this->getUser($cp_filter);

        $data['pending'] = ClientProgram::with(
            [
                'client' => function ($query) {
                    $query->select(
                        'id',
                        DB::raw('CONCAT(first_name, " ", COALESCE(last_name,"")) as client_name'),
                    );
                },
                'program'
            ]
        )->when($cp_filter['program'], function ($q) use ($cp_filter) {
            $q->whereHas('program', function ($q2) use ($cp_filter) {
                $q2->whereHas('main_prog', function ($q3) use ($cp_filter) {
                    $q3->where('prog_name', $cp_filter['program']);
                })->orWhereHas('sub_prog', function ($q3) use ($cp_filter) {
                    $q3->where('sub_prog_name', $cp_filter['program']);
                });
            });
        })->when(isset($cp_filter['quuid']), function ($q) use ($userId) {
            $q->where('empl_id', $userId);
        })->where('status', 0)->when($cp_filter['qdate'], function ($q) use ($cp_filter) {
            $q->whereMonth('created_at', date('m', strtotime($cp_filter['qdate'])))->whereYear('created_at', date('Y', strtotime($cp_filter['qdate'])));
        })->get()->groupBy('program.prog_program'); # pending

        $data['failed'] = ClientProgram::with(
            [
                'client' => function ($query) {
                    $query->select(
                        'id',
                        DB::raw('CONCAT(first_name, " ", COALESCE(last_name,"")) as client_name'),
                    );
                },
                'program'
            ]
        )->when($cp_filter['program'], function ($q) use ($cp_filter) {
            $q->whereHas('program', function ($q2) use ($cp_filter) {
                $q2->whereHas('main_prog', function ($q3) use ($cp_filter) {
                    $q3->where('prog_name', $cp_filter['program']);
                })->orWhereHas('sub_prog', function ($q3) use ($cp_filter) {
                    $q3->where('sub_prog_name', $cp_filter['program']);
                });
            });
        })->when(isset($cp_filter['quuid']), function ($q) use ($userId) {
            $q->where('empl_id', $userId);
        })->where('status', 2)->when($cp_filter['qdate'], function ($q) use ($cp_filter) {
            $q->whereMonth('failed_date', date('m', strtotime($cp_filter['qdate'])))->whereYear('failed_date', date('Y', strtotime($cp_filter['qdate'])));
        })->get()->groupBy('program.prog_program'); # failed

        $data['success'] = ClientProgram::with(
            [
                'client' => function ($query) {
                    $query->select(
                        'id',
                        DB::raw('CONCAT(first_name, " ", COALESCE(last_name,"")) as client_name'),
                    );
                },
                'program'
            ]
        )->when($cp_filter['program'], function ($q) use ($cp_filter) {
            $q->whereHas('program', function ($q2) use ($cp_filter) {
                $q2->whereHas('main_prog', function ($q3) use ($cp_filter) {
                    $q3->where('prog_name', $cp_filter['program']);
                })->orWhereHas('sub_prog', function ($q3) use ($cp_filter) {
                    $q3->where('sub_prog_name', $cp_filter['program']);
                });
            });
        })->when(isset($cp_filter['quuid']), function ($q) use ($userId) {
            $q->where('empl_id', $userId);
        })->where('status', 1)->when($cp_filter['qdate'], function ($q) use ($cp_filter) {
            $q->whereMonth('success_date', date('m', strtotime($cp_filter['qdate'])))->whereYear('success_date', date('Y', strtotime($cp_filter['qdate'])));
        })->get()->groupBy('program.prog_program'); # success
        $data['refund'] = ClientProgram::with(
            [
                'client' => function ($query) {
                    $query->select(
                        'id',
                        DB::raw('CONCAT(first_name, " ", COALESCE(last_name,"")) as client_name'),
                    );
                },
                'program'
            ]
        )->when($cp_filter['program'], function ($q) use ($cp_filter) {
            $q->whereHas('program', function ($q2) use ($cp_filter) {
                $q2->whereHas('main_prog', function ($q3) use ($cp_filter) {
                    $q3->where('prog_name', $cp_filter['program']);
                })->orWhereHas('sub_prog', function ($q3) use ($cp_filter) {
                    $q3->where('sub_prog_name', $cp_filter['program']);
                });
            });
        })->when(isset($cp_filter['quuid']), function ($q) use ($userId) {
            $q->where('empl_id', $userId);
        })->where('status', 3)->when($cp_filter['qdate'], function ($q) use ($cp_filter) {
            $q->whereMonth('refund_date', date('m', strtotime($cp_filter['qdate'])))->whereYear('refund_date', date('Y', strtotime($cp_filter['qdate'])));
        })->get()->groupBy('program.prog_program'); # refund

        return $data;
    }

    public function getInitialConsultationInformation($cp_filter)
    {
        $userId = $this->getUser($cp_filter);
        $count = isset($cp_filter['count']) ? $cp_filter['count'] : true;

        $soon = ClientProgram::when($cp_filter['qdate'], function ($q) use ($cp_filter) {
            // $q->whereMonth('created_at', date('m', strtotime($cp_filter['qdate'])))->whereYear('created_at', date('Y', strtotime($cp_filter['qdate'])));
            $q->whereMonth('initconsult_date', date('m', strtotime($cp_filter['qdate'])))->whereYear('initconsult_date', date('Y', strtotime($cp_filter['qdate'])));
        })->when(isset($cp_filter['quuid']), function ($q) use ($userId) {
            $q->where('empl_id', $userId);
        })->where('status', 0)->where('initconsult_date', '>', Carbon::now())->get(); # soon

        // $data[0] = $query->where('status', 0)->where('initconsult_date', '>', Carbon::now())->count(); # soon
        $already = ClientProgram::when($cp_filter['qdate'], function ($q) use ($cp_filter) {
            // $q->whereMonth('created_at', date('m', strtotime($cp_filter['qdate'])))->whereYear('created_at', date('Y', strtotime($cp_filter['qdate'])));
            $q->whereMonth('initconsult_date', '<=', date('m', strtotime($cp_filter['qdate'])))->whereYear('initconsult_date', '<=', date('Y', strtotime($cp_filter['qdate'])));
        })->when(isset($cp_filter['quuid']), function ($q) use ($userId) {
            $q->where('empl_id', $userId);
        })->where('status', 0)->where('initconsult_date', '<', Carbon::now())->get(); # already
        
        $success = ClientProgram::when($cp_filter['qdate'], function ($q) use ($cp_filter) {
            // $q->whereMonth('created_at', date('m', strtotime($cp_filter['qdate'])))->whereYear('created_at', date('Y', strtotime($cp_filter['qdate'])));
            $q->whereMonth('success_date', date('m', strtotime($cp_filter['qdate'])))->whereYear('success_date', date('Y', strtotime($cp_filter['qdate'])));
        })->when(isset($cp_filter['quuid']), function ($q) use ($userId) {
            $q->where('empl_id', $userId);
        })->where('status', 1)->whereNotNull('success_date')->whereNotNull('initconsult_date')->get(); # success

        $data = [$soon, $already, $success];

        if ($count === true)
            $data = [$soon->count(), $already->count(), $success->count()];
            
        return $data;
    }

    public function getInitialMaking($dateDetails, $cp_filter)
    {
        $userId = $this->getUser($cp_filter);

        # average value of initial consult and assessment sent date
        return ClientProgram::leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'tbl_client_prog.prog_id')
            ->leftJoin('tbl_main_prog', 'tbl_main_prog.id', '=', 'tbl_prog.main_prog_id')
            ->select([
                // DB::raw('AVG(DATEDIFF(assessmentsent_date, initconsult_date)) as initialMaking'),
                DB::raw('(CASE 
                    WHEN initconsult_date > assessmentsent_date THEN AVG(DATEDIFF(initconsult_date, assessmentsent_date)) ELSE AVG(DATEDIFF(assessmentsent_date, initconsult_date))
                END) as initialMaking')
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

        return ClientProgram::
            leftJoin('tbl_client', 'tbl_client.id', '=', 'tbl_client_prog.client_id')->
            leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'tbl_client_prog.prog_id')->
            leftJoin('tbl_main_prog', 'tbl_main_prog.id', '=', 'tbl_prog.main_prog_id')->
            select([
                'tbl_prog.prog_id',
                DB::raw('CONCAT(tbl_main_prog.prog_name, ": ", tbl_prog.prog_program) as program_name_st'),
                DB::raw('COUNT(*) as total_client_per_program'),
            ])->where('status', 1)->when(isset($cp_filter['quuid']), function ($q) use ($userId) {
                $q->where('empl_id', $userId);
            })
            // ->whereMonth('tbl_client_prog.created_at', date('m', strtotime($cp_filter['qdate'])))
            // ->whereYear('tbl_client_prog.created_at', date('Y', strtotime($cp_filter['qdate'])))
            ->whereMonth('success_date', date('m', strtotime($cp_filter['qdate'])))
            ->whereYear('success_date', date('Y', strtotime($cp_filter['qdate'])))
            ->groupBy('program_name_st')
            ->get();
    }

    public function getDetailSuccessProgramByMonthAndProgram($cp_filter)
    {
        $userId = $this->getUser($cp_filter);
        $progId = $cp_filter['progId'];
        $queryDate = $cp_filter['qdate'];

        return UserClient::whereHas('clientProgram', function ($q) use ($userId, $progId, $queryDate) {
            $q->where('status', 1)->when($userId, function ($q2) use ($userId) {
                $q2->where('empl_id', $userId);
            })->where('prog_id', $progId)->whereMonth('success_date', date('m', strtotime($queryDate)))->whereYear('success_date', date('Y', strtotime($queryDate)));
        })->get();
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
        $q_date = [];
        $filter_by_month = $cp_filter['query_use_month'] ?? null;
        if ($filter_by_month) {
            $q_date = [
                'year_1' => date('Y', strtotime($cp_filter['queryParams_monthyear1'])),
                'month_1' => date('m', strtotime($cp_filter['queryParams_monthyear1'])),
                'year_2' => date('Y', strtotime($cp_filter['queryParams_monthyear2'])),
                'month_2' => date('m', strtotime($cp_filter['queryParams_monthyear2']))
            ];
        }

        $extended_select = [
            'revenue_year1' => DB::table('tbl_client_prog as scp')
                ->leftJoin('tbl_inv as si', 'si.clientprog_id', '=', 'scp.clientprog_id')
                ->whereRaw('scp.prog_id = cp.prog_id')
                ->when($filter_by_month == "true", function ($q) use ($q_date) {
                    $q->whereYear('scp.created_at', $q_date['year_1'])
                        ->whereMonth('scp.created_at', $q_date['month_1']);
                }, function ($q) use ($cp_filter) {
                    $q->where(DB::raw('YEAR(scp.created_at)'), $cp_filter['queryParams_year1']);
                })
                ->select([
                    DB::raw('SUM(si.inv_totalprice_idr)')
                ]),
            'revenue_year2' => DB::table('tbl_client_prog as scp')
                ->leftJoin('tbl_inv as si', 'si.clientprog_id', '=', 'scp.clientprog_id')
                ->whereRaw('scp.prog_id = cp.prog_id')
                ->when($filter_by_month == "true", function ($q) use ($q_date) {
                    $q->whereYear('scp.created_at', $q_date['year_2'])
                        ->whereMonth('scp.created_at', $q_date['month_2']);
                }, function ($q) use ($cp_filter) {
                    $q->where(DB::raw('YEAR(scp.created_at)'), $cp_filter['queryParams_year2']);
                })
                ->select([
                    DB::raw('SUM(si.inv_totalprice_idr)')
                ])
        ];

        return DB::table('tbl_client_prog as cp')->
            leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'cp.prog_id')->
            leftJoin('tbl_main_prog', 'tbl_main_prog.id', '=', 'tbl_prog.main_prog_id')->
            select([
                'cp.prog_id',
                'tbl_main_prog.prog_name',
                'tbl_prog.prog_program'
            ] + $extended_select)
            ->when($userId, function ($query) use ($userId) {
                $query->where('cp.empl_id', $userId);
            })
            ->when(isset($cp_filter['qprogs']), function ($query) use ($cp_filter) {
                $query->whereIn('cp.prog_id', $cp_filter['qprogs']);
            })
            ->groupBy('cp.prog_id', 'tbl_main_prog.prog_name', 'tbl_prog.prog_program',)->get();
    }

    public function getActiveClientProgramAfterProgramEnd()
    {
        return ClientProgram::where('prog_running_status', 1)->where('prog_end_date', '<', now())->whereNotNull('prog_end_date')->get();
    }

    public function createBundleProgram($uuid, $clientProgramDetails)
    {
        Bundling::create(['uuid' => $uuid]);
        return BundlingDetail::insert($clientProgramDetails);

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

    private function getSearchedColumn(string $status)
    {
        switch (strtolower($status)) {

            case "pending":
                $searched_column = 'created_at';
                break;
                
            case "failed":
                $searched_column = 'failed_date';
                break;

            case "refund":
                $searched_column = 'refund_date';
                break;

            case "success":
                $searched_column = 'success_date';
                break;

            default:
                $searched_column = 'created_at';

        }

        return $searched_column;
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
