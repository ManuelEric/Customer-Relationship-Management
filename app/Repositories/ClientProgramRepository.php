<?php

namespace App\Repositories;

use App\Interfaces\ClientProgramRepositoryInterface;
use App\Models\ClientProgram;
use App\Models\pivot\ClientMentor;
use App\Models\Reason;
use App\Models\ViewClientProgram;
use DataTables;
use Illuminate\Support\Facades\DB;

class ClientProgramRepository implements ClientProgramRepositoryInterface 
{
    public function getAllClientProgramDataTables($clientId = NULL)
    {
        return Datatables::eloquent(ViewClientProgram::when($clientId, function($query) use ($clientId) {
            $query->where('client_id', $clientId);
        }))->make(true);
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
            ->select('users.id', DB::raw('CONCAT(users.first_name, " ", users.last_name) as fullname'))->get();
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
            $status = (strtotime($clientProgramDetails['prog_end_date']) < strtotime(date('Y-m-d'))) ? 0 : 1; # status mentoring [0: inactive, 1: active]

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
}