<?php

namespace App\Http\Controllers\Api\v1;

use App\Enum\LogModule;
use App\Http\Controllers\Controller;
use App\Http\Traits\MainProgramTrait;
use App\Interfaces\ClientLeadTrackingRepositoryInterface;
use App\Jobs\Client\ProcessInsertLogClient;
use App\Models\ClientProgram;
use App\Models\Program;
use App\Services\Log\LogService;
use App\Services\Program\ClientProgramService;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExtClientProgramController extends Controller
{
    use MainProgramTrait;

    protected $clientLeadTrackingRepository;

    public function __construct(
        ClientLeadTrackingRepositoryInterface $clientLeadTrackingRepository
    ) {
        $this->clientLeadTrackingRepository = $clientLeadTrackingRepository;
    }
    
    public function getSuccessPrograms(Request $request, $authorization = null): JsonResponse
    {
        $mentor_uuid = $request->get('k');
        $requested_main_program_name = $request->route('main_program_name');
        [$group_of, $sub_program] = $this->tnGetMainProgramName($requested_main_program_name);
        
        $b2cPrograms = \App\Models\ClientProgram::
        with([
            'client' => function ($query) {
                $query->
                    select('id', 'sch_id', 'first_name', 'last_name', 'grade_now');
                    // selectRaw('UpdateGradeStudent (year(CURDATE()),year(created_at),month(CURDATE()),month(created_at),st_grade) as grade');
            },
            'client.school' => function ($query) {
                $query->select('sch_id', 'sch_name');
            },
            'invoice' => function ($query) {
                $query->select('clientprog_id', 'inv_id');
            },
            'program' => function ($query) {
                $query->select('prog_id', 'main_prog_id', 'prog_program', 'prog_mentor');
            }
        ])->
        whereRelation('program.main_prog', 'group_of', $group_of)->
        // whereHas('program', function ($query) use ($main_program, $sub_program) {
        //     $query->whereHas('main_prog', function ($query) use ($main_program) {
        //         $query->where('prog_name', $main_program);
        //     })->whereHas('sub_prog', function ($query) use ($sub_program) {
        //         $query->when($sub_program != 'all', function ($query) use ($sub_program) {
        //             $query->whereIn('sub_prog_name', $sub_program);
        //         });
        //     });
        // })->
        when($mentor_uuid, function ($query) use ($mentor_uuid) {
            $query->whereHas('clientMentor', function ($query) use ($mentor_uuid) {
                $query->where('users.id', $mentor_uuid);
            });
        })->
        successAndPaid()->select('clientprog_id', 'prog_id', 'client_id', 'package', 'curriculum')->get();

        $mappedB2CPrograms = $b2cPrograms->map(function ($data) {

            $clientprog_id = $data->clientprog_id;
            $invoice_id = $data->invoice?->inv_id ?? null;
            $program_name = $data->program->program_name;
            // $require = $data->program->main_prog->id == 4 ? "Tutor" : "Mentor";
            $require = $data->program->prog_mentor;
            $package = $data->package;
            $curriculum = $data->curriculum;
            $client_id = $data->client->id;
            $client_fname = $data->client->first_name;
            $client_lname = $data->client->last_name;
            $client_grade = $data->client->grade_now ?? 0;
            $school_name = $data->client->school ? $data->client->school->sch_name : null;

            return [
                'category' => 'b2c',
                'clientprog_id' => $clientprog_id,
                'invoice_id' => $invoice_id,
                'program_name' => $program_name,
                'require' => $require,
                'package' => $package,
                'curriculum' => $curriculum,
                'client' => [
                    'uuid' => $client_id,
                    'first_name' => $client_fname,
                    'last_name' => $client_lname,
                    'school_name' => $school_name,
                    'grade' => $client_grade,
                ]
            ];
        });

        # take academic & test preparation b2b success program
        # if main program is 'Academic & Test Preparation'
        // if ( $main_program == 'Academic & Test Preparation' )

        # take tutoring b2b success program
        # get the program by group of column value
        if ( $group_of == 'Tutoring' )
        {

            $b2bPrograms = \App\Models\SchoolProgram::
            with([
                'school' => function ($query) {
                    $query->select('sch_id', 'sch_name');
                },
                'invoiceB2b' => function ($query) {
                    $query->select('schprog_id', 'invb2b_id');
                },
                'program' => function ($query) {
                    $query->select('prog_id', 'main_prog_id', 'prog_program');
                }
            ])->
            success()->
            // programIs('Academic & Test Preparation')->
            programIsGroupOf('Tutoring')->
            select('tbl_sch_prog.id', 'prog_id', 'sch_id')->
            get();
    
            $mappedB2BPrograms = $b2bPrograms->map(function ($data) {
    
                $schprog_id = $data->id;
                $invoiceb2b_id = $data->invoiceB2b->invb2b_id;
                $program_name = $data->program->program_name;
                $school_name = $data->school->sch_name;
    
                return [
                    'category' => 'b2b',
                    'schprog_id' => $schprog_id,
                    'invoice_id' => $invoiceb2b_id,
                    'program_name' => $program_name,
                    'client' => [
                        'school_name' => $school_name,
                    ]
                ];
            });
        }


        # if requested program is not academic tutoring
        # then return only B2C Programs
        $programs = isset($mappedB2BPrograms) ? $mappedB2CPrograms->merge($mappedB2BPrograms) : $mappedB2CPrograms;

        return response()->json($programs);
    }

    public function fnGetSuccessProgramsByIdentifier(Request $request, $authorization = null): JsonResponse
    {
        $mentor_uuid = $request->get('k');
        $requested_main_program_name = $request->route('main_program_name');
        $requested_clientprogram_id = $request->route('clientprogram_id');
        // dd($this->tnGetMainProgramName($requested_main_program_name));
        [$main_program, $sub_program] = $this->tnGetMainProgramName($requested_main_program_name);
        
        $b2cPrograms = \App\Models\ClientProgram::
        with([
            'client' => function ($query) {
                $query->
                    select('id', 'sch_id', 'first_name', 'last_name', 'grade_now');
                    // selectRaw('UpdateGradeStudent (year(CURDATE()),year(created_at),month(CURDATE()),month(created_at),st_grade) as grade');
            },
            'client.school' => function ($query) {
                $query->select('sch_id', 'sch_name');
            },
            'invoice' => function ($query) {
                $query->select('clientprog_id', 'inv_id');
            },
            'program' => function ($query) {
                $query->select('prog_id', 'main_prog_id', 'prog_program', 'prog_mentor');
            }
        ])->
        whereHas('program', function ($query) use ($main_program, $sub_program) {
            $query->whereHas('main_prog', function ($query) use ($main_program) {
                $query->where('group_of', $main_program);
            })->whereHas('sub_prog', function ($query) use ($sub_program) {
                $query->when($sub_program != 'all', function ($query) use ($sub_program) {
                    $query->whereIn('sub_prog_name', $sub_program);
                });
            });
        })->
        when($mentor_uuid, function ($query) use ($mentor_uuid) {
            $query->whereHas('clientMentor', function ($query) use ($mentor_uuid) {
                $query->where('users.id', $mentor_uuid);
            });
        })->
        where('clientprog_id', $requested_clientprogram_id)->
        select('clientprog_id', 'prog_id', 'client_id', 'package', 'curriculum')->get();

        $mappedB2CPrograms = $b2cPrograms->map(function ($data) {

            $clientprog_id = $data->clientprog_id;
            $invoice_id = $data->invoice?->inv_id ?? null;
            $program_name = $data->program->program_name;
            // $require = $data->program->main_prog->id == 4 ? "Tutor" : "Mentor";
            $require = $data->program->prog_mentor;
            $client_id = $data->client->id;
            $client_fname = $data->client->first_name;
            $client_lname = $data->client->last_name;
            $client_grade = $data->client->grade_now ?? 0;
            $school_name = $data->client->school ? $data->client->school->sch_name : null;

            return [
                'category' => 'b2c',
                'clientprog_id' => $clientprog_id,
                'invoice_id' => $invoice_id,
                'program_name' => $program_name,
                'require' => $require,
                'client' => [
                    'uuid' => $client_id,
                    'first_name' => $client_fname,
                    'last_name' => $client_lname,
                    'school_name' => $school_name,
                    'grade' => $client_grade,
                ],
                'package' => $data->package,
                'curriculum' => $data->curriculum,
            ];
        });

        # take academic & test preparation b2b success program
        # if main program is 'Academic & Test Preparation'
        if ( $main_program == 'Academic & Test Preparation' )
        {

            $b2bPrograms = \App\Models\SchoolProgram::
            with([
                'school' => function ($query) {
                    $query->select('sch_id', 'sch_name');
                },
                'invoiceB2b' => function ($query) {
                    $query->select('schprog_id', 'invb2b_id');
                },
                'program' => function ($query) {
                    $query->select('prog_id', 'main_prog_id', 'prog_program');
                }
            ])->success()->programIs('Academic & Test Preparation')->select('tbl_sch_prog.id', 'prog_id', 'sch_id')->get();
    
            $mappedB2BPrograms = $b2bPrograms->map(function ($data) {
    
                $schprog_id = $data->id;
                $invoiceb2b_id = $data->invoiceB2b->invb2b_id;
                $program_name = $data->program->program_name;
                $school_name = $data->school->sch_name;
    
                return [
                    'category' => 'b2b',
                    'schprog_id' => $schprog_id,
                    'invoice_id' => $invoiceb2b_id,
                    'program_name' => $program_name,
                    'client' => [
                        'school_name' => $school_name,
                    ]
                ];
            });
        }


        # if requested program is not academic tutoring
        # then return only B2C Programs
        $programs = isset($mappedB2BPrograms) ? $mappedB2CPrograms->merge($mappedB2BPrograms) : $mappedB2CPrograms;

        return response()->json($programs);
    }

    public function fnGetFreeTrialPrograms()
    {
        $clients_who_own_free_trial_tutor = \App\Models\ClientProgram::
        with([
            'client' => function ($query) {
                $query->
                    select('id', 'sch_id', 'first_name', 'last_name', 'grade_now');
                    // selectRaw('UpdateGradeStudent (year(CURDATE()),year(created_at),month(CURDATE()),month(created_at),st_grade) as grade');
            },
            'client.school' => function ($query) {
                $query->select('sch_id', 'sch_name');
            },
            'invoice' => function ($query) {
                $query->select('clientprog_id', 'inv_id');
            },
            'program' => function ($query) {
                $query->select('prog_id', 'main_prog_id', 'prog_program', 'prog_mentor');
            }
        ])->
        whereHas('program', function ($query) {
            $query->whereHas('main_prog', function ($query) {
                $query->whereIn('prog_name', ['Test Preparation', 'Subject Tutoring', 'Skillset Tutoring', 'Competition']);
            });

            //! commented because Academic Tutoring and Subject Tutoring turns into main program
            // ->whereHas('sub_prog', function ($query) {
            //     $query->whereIn('sub_prog_name', ['Academic Tutoring', 'Subject Tutoring']);
            // });
        })->
        pending()->
        getFreeTrial()->
        select('clientprog_id', 'prog_id', 'client_id')->get();

        $mapped_program = $clients_who_own_free_trial_tutor->map(function ($data) {

            $clientprog_id = $data->clientprog_id;
            $program_name = $data->program->program_name;
            $require = $data->program->prog_mentor;
            $client_id = $data->client->id;
            $client_fname = $data->client->first_name;
            $client_lname = $data->client->last_name;
            $client_grade = $data->client->grade_now;
            $school_name = $data->client->school ? $data->client->school->sch_name : null;

            return [
                'category' => 'b2c',
                'clientprog_id' => $clientprog_id,
                'trial' => true,
                'program_name' => $program_name,
                'require' => $require,
                'client' => [
                    'uuid' => $client_id,
                    'first_name' => $client_fname,
                    'last_name' => $client_lname,
                    'school_name' => $school_name,
                    'grade' => $client_grade,
                ]
            ];
        });

        return response()->json($mapped_program);
    }

    public function fnGetSuccessEssayProgram()
    {
        $b2cPrograms = \App\Models\ClientProgram::
        with([
            'client' => function ($query) {
                $query->
                    select('id', 'sch_id', 'first_name', 'last_name')->
                    selectRaw('UpdateGradeStudent (year(CURDATE()),year(created_at),month(CURDATE()),month(created_at),st_grade) as grade');
            },
            'client.school' => function ($query) {
                $query->select('sch_id', 'sch_name');
            },
            'invoice' => function ($query) {
                $query->select('clientprog_id', 'inv_id');
            },
            'program' => function ($query) {
                $query->select('prog_id', 'main_prog_id', 'prog_program', 'prog_mentor');
            },
            'clientMentor' => function ($query) {
                $query->
                    wherePivot('status', 1)->
                    select('users.id', 'phone', 'email', 'password', 'active');
            }
        ])->
        whereHas('program.main_prog', function ($query) {
            $query->where('prog_name', 'Admissions Mentoring');
        })->
        successAndPaid()->
        select('clientprog_id', 'prog_id', 'client_id', 'status', 'prog_running_status')->get();

        $mappedB2CPrograms = $b2cPrograms->map(function ($data) {

            $mentor_pics = [];
            $clientprog_id = $data->clientprog_id;
            $invoice_id = $data->invoice->inv_id;
            $program_name = $data->program->program_name;
            // $require = $data->program->main_prog->id == 4 ? "Tutor" : "Mentor";
            $require = $data->program->prog_mentor;
            $client_id = $data->client->id;
            $client_fname = $data->client->first_name;
            $client_lname = $data->client->last_name;
            $client_grade = $data->client->grade;
            $client_email = $data->client->mail;
            $client_address = $data->client->address;
            $school_name = $data->client->school ? $data->client->school->sch_name : null;

            foreach ($data->clientMentor as $mentor)
            {
                $mentor_pics[] = [
                    'mentor_id' => $mentor->id,
                    'type' => $this->fnGetMentorType($mentor->pivot->type),
                ];
            }

            return [
                'clientprog_id' => $clientprog_id,
                'invoice_id' => $invoice_id,
                'program_name' => $program_name,
                'is_active' => $data->status == 1 && $data->prog_running_status != 2 ? 1 : 0,
                'require' => $require,
                'mentors' => $mentor_pics,
                'client' => [
                    'uuid' => $client_id,
                    'first_name' => $client_fname,
                    'last_name' => $client_lname,
                    'email' => $client_email,
                    'address' => $client_address,
                    'school_name' => $school_name,
                    'grade' => $client_grade,
                ]
            ];
        });

        return $mappedB2CPrograms;
    }

    private function fnGetMentorType(int $type): string
    {
        switch ($type)
        {
            case 1:
                $type_desc = 'Supervising Mentor';
                break;
            case 2:
                $type_desc = 'Profile Building & Exploration Mentor';
                break;
            case 3:
                $type_desc = 'Application Strategy Mentor';
                break;
            case 4:
                $type_desc = 'Writing Mentor';
                break;
            case 5:
                $type_desc = 'Tutor';
                break;
            case 6:
                $type_desc = 'Subject Specialist';
                break;
        }

        return $type_desc;
    }

    public function fnPromoteToGraduatedMentee(
        ClientProgram $client_program,
        LogService $log_service,
        )
    {
        if ($client_program->prog_running_status == 2)
        {
            return response()->json([
                'message' => 'Client was already graduated.',
            ]);
        }

        DB::beginTransaction();
        try {

            # since we want to promote the client into mentee
            # which mean, his/her client program should be updated into `done` or prog_running_status (2)
            $client_program->update([
                'prog_running_status' => 2
            ]);
    
            $leads_tracking = $this->clientLeadTrackingRepository->getCurrentClientLead($client_program->client->id);
    
            # update status client lead tracking
            if($leads_tracking->count() > 0){
                foreach($leads_tracking as $lead_tracking){
                    $this->clientLeadTrackingRepository->updateClientLeadTrackingById($lead_tracking->id, ['status' => 0]);
                }
            }
            DB::commit();
    
            $client_data_for_log_client[] = [
                'client_id' => $client_program->client->id,
                'first_name' => $client_program->client->first_name,
                'last_name' => $client_program->client->last_name,
                'inputted_from' => 'update-client-program',
                'clientprog_id' => $client_program->clientprog_id,
                'status_program' => 1,
                'old_status_program' => 0,
                'running_status_program' => 2,
                'old_running_status_program' => 1
            ];
    
            # trigger to insert log client
            ProcessInsertLogClient::dispatch($client_data_for_log_client)->onQueue('insert-log-client')->afterCommit();

        } catch (Exception $e) {
            
            DB::rollBack();
            $log_service->createErrorLog(LogModule::UPDATE_CLIENT_PROGRAM, $e->getMessage(), $e->getLine(), $e->getFile());
            throw new HttpResponseException(
                response()->json([
                    'message' => 'Failed to change mentee status.',
                    'error' => $e->getMessage()
                ])
            );
        }
        
        # Update success
        # create log success
        $log_service->createSuccessLog(LogModule::UPDATE_CLIENT_PROGRAM, 'Client program has been updated');
        return response()->json([
            'message' => 'Client program has been updated',
            'data' => $client_program
        ]);
    }

    public function fnPromoteMultipleToGraduatedMentee(Request $request, LogService $log_service)
    {
        $client_programs = $request->get('client_programs', []);
        if (empty($client_programs)) {
            return response()->json([
                'message' => 'No client programs provided.'
            ], 400);
        }

        DB::beginTransaction();
        try {
            foreach ($client_programs as $client_program_id) {
                $client_program = ClientProgram::findOrFail($client_program_id);
                if ($client_program->prog_running_status == 2) {
                    continue; // Skip if already graduated
                }
                $client_program->update(['prog_running_status' => 2]);

                $leads_tracking = $this->clientLeadTrackingRepository->getCurrentClientLead($client_program->client->id);
    
                # update status client lead tracking
                if($leads_tracking->count() > 0){
                    foreach($leads_tracking as $lead_tracking){
                        $this->clientLeadTrackingRepository->updateClientLeadTrackingById($lead_tracking->id, ['status' => 0]);
                    }
                }

            }
            DB::commit();

            $client_data_for_log_client[] = [
                'client_id' => $client_program->client->id,
                'first_name' => $client_program->client->first_name,
                'last_name' => $client_program->client->last_name,
                'inputted_from' => 'update-client-program',
                'clientprog_id' => $client_program->clientprog_id,
                'status_program' => 1,
                'old_status_program' => 0,
                'running_status_program' => 2,
                'old_running_status_program' => 1
            ];
    
            # trigger to insert log client
            ProcessInsertLogClient::dispatch($client_data_for_log_client)->onQueue('insert-log-client')->afterCommit();

            // Log success
            $log_service->createSuccessLog(LogModule::MULTIPLE_UPDATE_CLIENT_PROGRAM, 'Multiple client programs have been updated');
            return response()->json([
                'message' => 'Client programs have been updated successfully.'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            $log_service->createErrorLog(LogModule::MULTIPLE_UPDATE_CLIENT_PROGRAM, $e->getMessage(), $e->getLine(), $e->getFile());
            throw new HttpResponseException(
                response()->json([
                    'message' => 'Failed to update client programs.',
                    'error' => $e->getMessage()
                ])
            );
        }
    }
}
