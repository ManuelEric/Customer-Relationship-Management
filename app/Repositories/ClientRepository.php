<?php

namespace App\Repositories;

use App\Http\Traits\FindDestinationCountryScore;
use App\Http\Traits\FindSchoolYearLeftScoreTrait;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\RoleRepositoryInterface;
use App\Models\Client;
use App\Models\Tag;
use App\Models\UserClient;
use App\Models\UserClientAdditionalInfo;
use App\Models\v1\Student as CRMStudent;
use App\Models\v1\StudentParent as CRMParent;
use DataTables;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Models\ClientAcceptance;
use App\Models\ClientEvent;
use App\Models\ClientLeadTracking;
use App\Models\FollowupClient;
use App\Models\PicClient;
use App\Models\University;
use App\Models\User;
use App\Models\ViewRawClient;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class ClientRepository implements ClientRepositoryInterface
{
    use FindSchoolYearLeftScoreTrait;
    use FindDestinationCountryScore;
    use StandardizePhoneNumberTrait;
    private RoleRepositoryInterface $roleRepository;
    private $potentialClients;
    private $existingMentees;


    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
        // $this->ALUMNI_IDS = $this->getAlumnis();
        // $this->potentialClients = $this->getPotentialClients()->pluck('id')->toArray();
        // $this->existingMentees = $this->getExistingMentees()->pluck('id')->toArray();
    }

    public function getAllClients($selectColumns = [])
    {
        $query = UserClient::filterBasedOnPIC();
        if ($selectColumns)
            $query->select($selectColumns);

            
        return $query->get();
    }

    public function getAllClientsFromViewTable()
    {
        return Client::all();
    }

    public function getAllClientDataTables()
    {
        return Datatables::eloquent(UserClient::query())->make(true);
    }

    public function getMaxGraduationYearFromClient()
    {
        return Client::max('graduation_year');
    }

    public function findDeletedClientById($clientId)
    {
        return Client::onlyTrashed()->where('id', $clientId)->first();
    }

    public function restoreClient($clientId)
    {
        return UserClient::where('id', $clientId)->withTrashed()->restore();
    }

    public function getAllClientByRoleAndStatusDataTables($roleName, $statusClient = NULL)
    {
        # if role name is student
        # then retrieve all student without mentee
        # so first select all mentee
        # then use not in all mentee
        $client = [];
        if ($roleName == "Student" && $statusClient == 0) {
            $client = UserClient::whereHas('roles', function ($query) {
                $query->where('role_name', 'mentee');
            })->pluck('id')->toArray();
        }

        return Datatables::eloquent(
            Client::whereHas('roles', function ($query) use ($roleName) {
                $query->where('role_name', $roleName);
            })->when($roleName == "Student", function ($q) use ($client) {
                $q->whereNotIn('client.id', $client);
            })
                # for prospective, potential, current, completed, the value is [0, 1, 2, 3] 
                ->when(is_int($statusClient) && $statusClient !== NULL, function ($query) use ($statusClient) {
                    $query->where('st_statuscli', $statusClient);
                })
                ->when(is_string($statusClient) && $statusClient !== NULL, function ($query) use ($statusClient) {

                    $query->when($statusClient == "active", function ($query1) {

                        $query1->whereHas('clientProgram', function ($q2) {
                            $q2->whereIn('prog_running_status', [0, 1]);
                        })->withCount('clientProgram')->having('client_program_count', '>', 0);
                    }, function ($query1) {

                        $query1->whereHas('clientProgram', function ($q2) {
                            $q2->whereIn('prog_running_status', [2]);
                        })->withCount([
                            'clientProgram as client_program_count' => function ($query) {
                                $query->whereIn('prog_running_status', [0, 1, 2]);
                            },
                            'clientProgram as client_program_finish_count' => function ($query) {
                                $query->where('prog_running_status', 2);
                            }
                        ])->havingRaw('client_program_count = client_program_finish_count');
                    });
                })->where('client.is_verified', 'Y')->whereNull('client.deleted_at')
                ->orderBy('st_statusact', 'desc')
                ->orderBy('client.updated_at', 'DESC')
            // ->orderBy('client.updated_at', 'DESC')
        )
            ->addColumn('parent_name', function ($data) {
                return $data->parents()->count() > 0 ? $data->parents()->first()->first_name . ' ' . $data->parents()->first()->last_name : null;
            })
            ->addColumn('parent_phone', function ($data) {
                return $data->parents()->count() > 0 ? $data->parents()->first()->phone : null;
            })
            ->addColumn('children_name', function ($data) {
                return $data->childrens()->count() > 0 ? $data->childrens()->first()->first_name . ' ' . $data->childrens()->first()->last_name : null;
            })
            ->addColumn('parent_name', function ($data) {
                return $data->parents()->count() > 0 ? $data->parents()->first()->first_name . ' ' . $data->parents()->first()->last_name : null;
            })
            ->addColumn('parent_phone', function ($data) {
                return $data->parents()->count() > 0 ? $data->parents()->first()->phone : null;
            })
            ->addColumn('children_name', function ($data) {
                return $data->childrens()->count() > 0 ? $data->childrens()->first()->first_name . ' ' . $data->childrens()->first()->last_name : null;
            })
            ->rawColumns(['address'])
            ->make(true);
    }

    public function getAllClientByRole($roleName, $month = null) # mentee, parent, teacher
    {
        $alumnis = UserClient::whereHas('clientProgram', function ($q2) {
            $q2->whereIn('prog_running_status', [2]);
        })->withCount([
            'clientProgram as client_program_count' => function ($query) {
                $query->whereIn('prog_running_status', [0, 1, 2])->whereHas('program', function ($q2) {
                    $q2->whereHas('main_prog', function ($q3) {
                        $q3->where('prog_name', 'Admissions Mentoring');
                    });
                });
            },
            'clientProgram as client_program_finish_count' => function ($query) {
                $query->where('prog_running_status', 2)->whereHas('program', function ($q2) {
                    $q2->whereHas('main_prog', function ($q3) {
                        $q3->where('prog_name', 'Admissions Mentoring');
                    });
                });
            }
        ])->havingRaw('client_program_count = client_program_finish_count')->pluck('tbl_client.id')->toArray();

        return UserClient::when($roleName == "alumni", function ($query) {
            $query->whereHas('clientProgram', function ($q2) {
                $q2->whereIn('prog_running_status', [2]);
            })->withCount([
                'clientProgram as client_program_count' => function ($query) {
                    $query->whereIn('prog_running_status', [0, 1, 2])->whereHas('program', function ($q2) {
                        $q2->whereHas('main_prog', function ($q3) {
                            $q3->where('prog_name', 'Admissions Mentoring');
                        });
                    });
                },
                'clientProgram as client_program_finish_count' => function ($query) {
                    $query->where('prog_running_status', 2)->whereHas('program', function ($q2) {
                        $q2->whereHas('main_prog', function ($q3) {
                            $q3->where('prog_name', 'Admissions Mentoring');
                        });
                    });
                }
            ])->havingRaw('client_program_count = client_program_finish_count');
        }, function ($query) use ($roleName, $alumnis) {
            $query->when($roleName == 'mentee', function ($query2) use ($alumnis) {
                $query2->whereNotIn('tbl_client.id', $alumnis);
            })->whereHas('roles', function ($query2) use ($roleName) {
                $query2->where('role_name', $roleName);
            });
        })->when($month, function ($query) use ($month) {
            $query->whereMonth('tbl_client.created_at', date('m', strtotime($month)))->whereYear('tbl_client.created_at', date('Y', strtotime($month)));
        })->where('is_verified', 'Y')->get();
    }

    public function getClientWithNoPicAndHaveProgram()
    {
        # exclude raw data
        return UserClient::with('clientProgram')->hasNoPic()->whereHas('clientProgram', function ($programQuery) {
                $programQuery->whereHas('internalPic');
            })->
            isVerified()->
            get();
    }

    /* NEW */
    public function getDataTables($model, $raw = false)
    {

        if ($raw === true) 
            return DataTables::of($model)->make(true);

        return DataTables::eloquent($model)->
            // // addColumn('parent_name', function ($data) {
            // //     return $data->parents()->count() > 0 ? $data->parents()->first()->first_name . ' ' . $data->parents()->first()->last_name : null;
            // // })->
            // // addColumn('parent_mail', function ($data) {
            // //     return $data->parents()->count() > 0 ? $data->parents()->first()->mail : null;
            // // })->
            // // addColumn('parent_phone', function ($data) {
            // //     return $data->parents()->count() > 0 ? $data->parents()->first()->phone : null;
            // // })->
            // // addColumn('children_name', function ($data) {
            // //     return $data->childrens()->count() > 0 ? $data->childrens()->first()->first_name . ' ' . $data->childrens()->first()->last_name : null;
            // // })->
            // // addColumn('parent_name', function ($data) {
            // //     return $data->parents()->count() > 0 ? $data->parents()->first()->first_name . ' ' . $data->parents()->first()->last_name : null;
            // // })->
            // // addColumn('parent_phone', function ($data) {
            // //     return $data->parents()->count() > 0 ? $data->parents()->first()->phone : null;
            // // })->
            // // addColumn('children_name', function ($data) {
            // //     return $data->childrens()->count() > 0 ? $data->childrens()->first()->first_name . ' ' . $data->childrens()->first()->last_name : null;
            // // })->
            addColumn('followup_status', function (Client $client) {
                if (!$latestId = $client->followupSchedule()->max('id'))
                    return '-';
                
                $status = $client->followupSchedule()->where('id', $latestId)->first()->status;

                switch ($status) {
                    case 0:
                        $message = 'Currently following up';
                        break;
                    case 1:
                        $message = 'Awaiting response';
                        break;
                }
                return '<a href="'. url('client/board?name='.$client->full_name) .'" target="_blank">'.$message.'</a>';
            })->
            // addColumn('took_ia', function ($data) {
            //     $endpoint = env('EDUALL_ASSESSMENT_URL') . 'api/get/took-ia/' . $data->uuid;

            //     try {
            //         # create 
            //         $response = Http::get($endpoint);
                            
            //         # catch when sending the request to $endpoints failed
            //         if ($response->failed() ) {
            //             return 'error';
            //         }

            //     } catch (Exception $e) {
            //         return 'error';
            //     }
    
            //     return isset($response['data']) ? $response['data'] : 0;
            // })->
            rawColumns(['followup_status', 'address'])->
            filterColumn('parent_name', function ($query, $keyword) {
                $query->whereRaw("RTRIM(CONCAT(parent.first_name, ' ', COALESCE(parent.last_name, ''))) like ?", "%{$keyword}%");
            })->
            filterColumn('parent_mail', function ($query, $keyword) {
                $query->whereRaw("parent.mail like ?", "%{$keyword}%");
            })->
            filterColumn('parent_phone', function ($query, $keyword) {
                $query->whereRaw("parent.phone like ?", "%{$keyword}%");
            })->
            filterColumn('children_name', function ($query, $keyword) {
                $query->whereRaw("RTRIM(CONCAT(children.first_name, ' ', COALESCE(children.last_name, ''))) like ?", "%{$keyword}%");
            })->
            # query for ordering client by status suggest (Hot --> Cold)
            # orderColumn is used to handle sorting when user click javascript header
            // orderColumn('status_lead', function ($query, $order) {
            //     $query->orderBy('status_lead_score', $order);
            // })->
            // # order is used to handle sorting by default when page refreshed
            // order(function ($query) {
            //     $query->orderBy('status_lead_score', 'desc');
            // })->
            toJson();
    }

    public function getNewLeads($asDatatables = false, $month = null, $advanced_filter = [])
    {

        # new client that havent offering our program
        
        $query = Client::select([
                'client.*',
                'parent.mail as parent_mail',
                'parent.phone as parent_phone',
            ])->
            selectRaw('RTRIM(CONCAT(parent.first_name, " ", COALESCE(parent.last_name, ""))) as parent_name')->
            // leftJoin('tbl_client_relation as relation', 'relation.child_id', '=', 'client.id')->
            leftJoin('tbl_client as parent', 'parent.id', '=', 
            DB::raw('( SELECT
                MAX(parent_id) parent_id
                FROM tbl_client_relation as relation
                WHERE relation.child_id = client.id
            )'))->            // where(function ($q) {
            //     $q->
            //         doesntHave('clientProgram')->
            //         orWhere(function ($q_2) {
            //             $q_2->
            //                 whereHas('clientProgram', function ($subQuery) {
            //                     // $subQuery->whereIn('status', [2, 3])->where('status', '!=', 0);
            //                     $subQuery->whereIn('status', [2, 3]);
            //                 })->
            //                 whereDoesntHave('clientProgram', function ($subQuery) {
            //                     $subQuery->whereIn('status', [0, 1]);
            //                 });
            //         });
            // })->

            where('client.category', 'new-lead')->
            // doesntHave('clientProgram')->
            when($month, function ($subQuery) use ($month) {
                $subQuery->whereMonth('client.created_at', date('m', strtotime($month)))->whereYear('client.created_at', date('Y', strtotime($month)));
            })->
            whereHas('roles', function ($subQuery) {
                $subQuery->where('role_name', 'student');
            })->
            when(!empty($advanced_filter['school_name']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereIn('school_name', $advanced_filter['school_name']);
            })->
            when(!empty($advanced_filter['graduation_year']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereIn('client.graduation_year_real', $advanced_filter['graduation_year']);
            })->
            when(!empty($advanced_filter['leads']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereIn('lead_source', $advanced_filter['leads']);
            })->
            when(!empty($advanced_filter['initial_programs']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereIn('program_suggest', $advanced_filter['initial_programs']);
            })->
            when(!empty($advanced_filter['status_lead']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereIn('status_lead', $advanced_filter['status_lead']);
            })->
            when(!empty($advanced_filter['pic']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereIn('client.pic_id', $advanced_filter['pic']);
            })->
            when(!empty($advanced_filter['active_status']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereIn('client.st_statusact', $advanced_filter['active_status']);
            }, function ($subQuery) {
                $subQuery->where('client.st_statusact', 1);
            })->
            when(!empty($advanced_filter['start_joined_date']) && empty($advanced_filter['end_joined_date']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereDate('client.created_at', '>=', $advanced_filter['start_joined_date']);
            })->
            when(!empty($advanced_filter['end_joined_date']) && empty($advanced_filter['start_joined_date']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereDate('client.created_at', '<=', $advanced_filter['end_joined_date']);
            })->
            when(!empty($advanced_filter['start_joined_date']) && !empty($advanced_filter['end_joined_date']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereBetween('client.created_at', [$advanced_filter['start_joined_date'], $advanced_filter['end_joined_date']]);
            })->
            isNotSalesAdmin()->
            isUsingAPI()->
            isActive()->
            isVerified();
            
        return $asDatatables === false ? $query->get() : $query;
    }


    public function getPotentialClients($asDatatables = false, $month = null, $advanced_filter = [])
    {
        # new client that have been offered our program but hasnt deal yet
        $query = Client::select([
                'client.*',
                'parent.mail as parent_mail',
                'parent.phone as parent_phone'
            ])->
            selectRaw('RTRIM(CONCAT(parent.first_name, " ", COALESCE(parent.last_name, ""))) as parent_name')->
            // leftJoin('tbl_client_relation as relation', 'relation.child_id', '=', 'client.id')->
            leftJoin('tbl_client as parent', 'parent.id', '=', 
            DB::raw('( SELECT
                MAX(parent_id) parent_id
                FROM tbl_client_relation as relation
                WHERE relation.child_id = client.id
            )'))->           
            // whereHas('clientProgram', function ($subQuery) {
            //     // $subQuery->whereIn('status', [0, 2, 3]); # because refund and cancel still marked as potential client
            //     $subQuery->where('status', 0); # because refund and cancel still marked as potential client
            // })->
            // whereDoesntHave('clientProgram', function ($subQuery) {
            //     $subQuery->where('status', 1);
            // })-> # tidak punya client program dengan status 1 : success
           

            where('client.category', 'potential')->
            when($month, function ($subQuery) use ($month) {
                $subQuery->whereMonth('client.created_at', date('m', strtotime($month)))->whereYear('client.created_at', date('Y', strtotime($month)));
            })->whereHas('roles', function ($subQuery) {
                $subQuery->where('role_name', 'student');
            })->when(!empty($advanced_filter['school_name']), function ($subQuery) use ($advanced_filter) {
                $subQuery->whereIn('school_name', $advanced_filter['school_name']);
            })->when(!empty($advanced_filter['graduation_year']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereIn('client.graduation_year_real', $advanced_filter['graduation_year']);
            })->when(!empty($advanced_filter['leads']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereIn('lead_source', $advanced_filter['leads']);
            })->when(!empty($advanced_filter['initial_programs']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereIn('program_suggest', $advanced_filter['initial_programs']);
            })->when(!empty($advanced_filter['status_lead']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereIn('status_lead', $advanced_filter['status_lead']);
            })->when(!empty($advanced_filter['pic']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereIn('client.pic_id', $advanced_filter['pic']);
            })->when(!empty($advanced_filter['active_status']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereIn('client.st_statusact', $advanced_filter['active_status']);
            }, function ($subQuery) {
                $subQuery->where('client.st_statusact', 1);
            })->when(!empty($advanced_filter['start_joined_date']) && empty($advanced_filter['end_joined_date']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereDate('client.created_at', '>=', $advanced_filter['start_joined_date']);
            })->when(!empty($advanced_filter['end_joined_date']) && empty($advanced_filter['start_joined_date']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereDate('client.created_at', '<=', $advanced_filter['end_joined_date']);
            })->when(!empty($advanced_filter['start_joined_date']) && !empty($advanced_filter['end_joined_date']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereBetween('client.created_at', [$advanced_filter['start_joined_date'], $advanced_filter['end_joined_date']]);
            })->
            isNotSalesAdmin()->
            isUsingAPI()->
            isActive()->
            isVerified();

        return $asDatatables === false ? $query->orderBy('client.updated_at', 'desc')->get() : $query;
    }

    public function getExistingMentees($asDatatables = false, $month = null, $advanced_filter = [])
    {
        # join program admission mentoring & prog running status hasnt done
        $query = Client::select([
                'client.*',
                'parent.id as parent_id', 
                'parent.mail as parent_mail',
                'parent.phone as parent_phone'
            ])->selectRaw('RTRIM(CONCAT(parent.first_name, " ", COALESCE(parent.last_name, ""))) as parent_name')->
            // leftJoin('tbl_client_relation as relation', 'relation.child_id', '=', 'client.id')->
            leftJoin('tbl_client as parent', 'parent.id', '=', 
                    DB::raw('( SELECT
                        MAX(parent_id) parent_id
                        FROM tbl_client_relation as relation
                        WHERE relation.child_id = client.id
                    )'))->
            # code below is commented out
            # because when code below uncommented then clients that has running admission program and running non-admission program will not be able to show on the list
            // whereDoesntHave('clientProgram', function ($subQuery) {
            //     $subQuery->whereHas('program', function ($subQuery_2) {
            //         $subQuery_2->whereHas('main_prog', function ($subQuery_3) {
            //             $subQuery_3->where('prog_name', '!=', 'Admissions Mentoring');
            //         });
            //     })->where('status', 1)->where('prog_running_status', '!=', 2); # meaning 1 is he/she has been offered admissions mentoring before 
            // })->

            
            // where(function ($r) {

            //     $r->whereHas('clientProgram', function ($subQuery) {
            //         $subQuery->whereHas('program.main_prog', function ($subQuery_2) {
            //             $subQuery_2->where('prog_name', 'Admissions Mentoring');
            //         })->where('status', 1)->where('prog_running_status', '!=', 2);
            //     })->
            //     orWhere(function ($q) {
            //         $q->
            //         whereHas('clientProgram', function ($subQuery) {
            //             $subQuery->whereHas('program.main_prog', function ($subQuery_2) {
            //                 $subQuery_2->where('prog_name', 'Admissions Mentoring');
            //             })->where('status', 1)->where('prog_running_status', 2);
            //         })->
            //         whereHas('clientProgram', function ($subQuery) {
            //             $subQuery->where('status', 0);
            //         });
            //     });
            // })->
            // whereNotIn('client.id', $this->getPotentialClients()->pluck('id')->toArray())->
            
            where('client.category', 'mentee')->
            when($month, function ($subQuery) use ($month) {
                $subQuery->whereMonth('client.created_at', date('m', strtotime($month)))->whereYear('client.created_at', date('Y', strtotime($month)));
            })->whereHas('roles', function ($subQuery) {
                $subQuery->where('role_name', 'student');
            })->when(!empty($advanced_filter['school_name']), function ($subQuery) use ($advanced_filter) {
                $subQuery->whereIn('school_name', $advanced_filter['school_name']);
            })->when(!empty($advanced_filter['graduation_year']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereIn('client.graduation_year_real', $advanced_filter['graduation_year']);
            })->when(!empty($advanced_filter['leads']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereIn('lead_source', $advanced_filter['leads']);
            })->when(!empty($advanced_filter['initial_programs']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereIn('program_suggest', $advanced_filter['initial_programs']);
            })->when(!empty($advanced_filter['status_lead']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereIn('status_lead', $advanced_filter['status_lead']);
            })->when(!empty($advanced_filter['pic']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereIn('client.pic_id', $advanced_filter['pic']);
            })->when(!empty($advanced_filter['active_status']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereIn('client.st_statusact', $advanced_filter['active_status']);
            }, function ($subQuery) {
                $subQuery->where('client.st_statusact', 1);
            })->when(!empty($advanced_filter['start_joined_date']) && empty($advanced_filter['end_joined_date']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereDate('client.created_at', '>=', $advanced_filter['start_joined_date']);
            })->when(!empty($advanced_filter['end_joined_date']) && empty($advanced_filter['start_joined_date']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereDate('client.created_at', '<=', $advanced_filter['end_joined_date']);
            })->when(!empty($advanced_filter['start_joined_date']) && !empty($advanced_filter['end_joined_date']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereBetween('client.created_at', [$advanced_filter['start_joined_date'], $advanced_filter['end_joined_date']]);
            })->
            isNotSalesAdmin()->
            isUsingAPI()->
            isActive()->
            isVerified();
            // groupBy('client.id');

        return $asDatatables === false ? $query->orderBy('client.updated_at', 'desc')->get() : $query;
    }

    public function getExistingNonMentees($asDatatables = false, $month = null, $advanced_filter = [])
    {
        # has join our program but its not admissions mentoring
        $query = Client::select([
                'client.*',
                'parent.mail as parent_mail',
                'parent.phone as parent_phone'
            ])->
            selectRaw('RTRIM(CONCAT(parent.first_name, " ", COALESCE(parent.last_name, ""))) as parent_name')->
            // leftJoin('tbl_client_relation as relation', 'relation.child_id', '=', 'client.id')->
            leftJoin('tbl_client as parent', 'parent.id', '=', 
                    DB::raw('( SELECT
                        MAX(parent_id) parent_id
                        FROM tbl_client_relation as relation
                        WHERE relation.child_id = client.id
                    )'))->
            // where(function ($r) {

            //     $r->
            //     whereHas('clientProgram', function ($subQuery) {
            //         $subQuery->whereHas('program.main_prog', function ($subQuery_2) {
            //             $subQuery_2->where('prog_name', '!=', 'Admissions Mentoring');
            //         })->where(function ($subQuery_2) {
            //             $subQuery_2->where('status', 1)->where('prog_running_status', '!=', 2);
            //         });
            //     })->
            //     orWhere(function ($q) {
            //         $q->
            //         whereHas('clientProgram', function ($subQuery) {
            //             $subQuery->whereHas('program.main_prog', function ($subQuery_2) {
            //                 $subQuery_2->where('prog_name', '!=', 'Admissions Mentoring');
            //             })->where('status', 1)->where('prog_running_status', 2);
            //         })->
            //         whereHas('clientProgram', function ($subQuery) {
            //             $subQuery->where('status', 0);
            //         });
            //     });
            // })->
            // whereNotIn('client.id', $this->getExistingMentees()->pluck('id')->toArray())->
            
            where('client.category', 'non-mentee')->
            when($month, function ($subQuery) use ($month) {
                $subQuery->whereMonth('client.created_at', date('m', strtotime($month)))->whereYear('client.created_at', date('Y', strtotime($month)));
            })->whereHas('roles', function ($subQuery) {
                $subQuery->where('role_name', 'student');
            })->when(!empty($advanced_filter['school_name']), function ($subQuery) use ($advanced_filter) {
                $subQuery->whereIn('school_name', $advanced_filter['school_name']);
            })->when(!empty($advanced_filter['graduation_year']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereIn('client.graduation_year_real', $advanced_filter['graduation_year']);
            })->when(!empty($advanced_filter['leads']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereIn('lead_source', $advanced_filter['leads']);
            })->when(!empty($advanced_filter['initial_programs']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereIn('program_suggest', $advanced_filter['initial_programs']);
            })->when(!empty($advanced_filter['status_lead']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereIn('status_lead', $advanced_filter['status_lead']);
            })->
            when(!empty($advanced_filter['pic']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereIn('client.pic_id', $advanced_filter['pic']);
            })->
            when(!empty($advanced_filter['active_status']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereIn('client.st_statusact', $advanced_filter['active_status']);
            }, function ($subQuery) {
                $subQuery->where('client.st_statusact', 1);
            })->
            when(!empty($advanced_filter['start_joined_date']) && empty($advanced_filter['end_joined_date']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereDate('client.created_at', '>=', $advanced_filter['start_joined_date']);
            })->
            when(!empty($advanced_filter['end_joined_date']) && empty($advanced_filter['start_joined_date']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereDate('client.created_at', '<=', $advanced_filter['end_joined_date']);
            })->
            when(!empty($advanced_filter['start_joined_date']) && !empty($advanced_filter['end_joined_date']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereBetween('client.created_at', [$advanced_filter['start_joined_date'], $advanced_filter['end_joined_date']]);
            })->
            isNotSalesAdmin()->
            isUsingAPI()->
            isActive()->
            isVerified();
            // groupBy('client.id');

        return $asDatatables === false ? $query->orderBy('client.updated_at', 'desc')->get() : $query;
    }

    public function getAllClientStudent($advanced_filter = [], $asDatatables=false)
    {
        // $new_leads = $this->getNewLeads(false, null, $advanced_filter)->pluck('id')->toArray();
        // $potential = $this->getPotentialClients(false, null, $advanced_filter)->pluck('id')->toArray();
        // $existing = $this->getExistingMentees(false, null, $advanced_filter)->pluck('id')->toArray();
        // $existingNon = $this->getExistingNonMentees(false, null, $advanced_filter)->pluck('id')->toArray();

        // $clientStudent = $new_leads;
        // $clientStudent = array_merge($clientStudent, $potential);
        // $clientStudent = array_merge($clientStudent, $existing);
        // $clientStudent = array_merge($clientStudent, $existingNon);

        $query = Client::select([
            'client.*',
            'parent.mail as parent_mail',
            'parent.phone as parent_phone'
        ])
        // ->selectRaw('RTRIM(CONCAT(parent.first_name, " ", COALESCE(parent.last_name, ""))) as parent_name')->leftJoin('tbl_client_relation as relation', 'relation.child_id', '=', 'client.id')->leftJoin('tbl_client as parent', 'parent.id', '=', 'relation.parent_id')->whereIn('client.id', $clientStudent)->where('client.is_verified', 'Y')->whereNull('client.deleted_at');
        ->selectRaw('RTRIM(CONCAT(parent.first_name, " ", COALESCE(parent.last_name, ""))) as parent_name')
        ->leftJoin('tbl_client_relation as relation', 'relation.child_id', '=', 'client.id')
        ->leftJoin('tbl_client as parent', 'parent.id', '=', 'relation.parent_id')
        ->whereIn('client.category', ['new-lead', 'potential', 'mentee', 'non-mentee'])
        ->where('client.is_verified', 'Y')
        ->whereNull('client.deleted_at')
        ->when(!empty($advanced_filter['school_name']), function ($subQuery) use ($advanced_filter) {
            $subQuery->whereIn('school_name', $advanced_filter['school_name']);
        })->when(!empty($advanced_filter['graduation_year']), function ($querySearch) use ($advanced_filter) {
            $querySearch->whereIn('client.graduation_year_real', $advanced_filter['graduation_year']);
        })->when(!empty($advanced_filter['leads']), function ($querySearch) use ($advanced_filter) {
            $querySearch->whereIn('lead_source', $advanced_filter['leads']);
        })->when(!empty($advanced_filter['initial_programs']), function ($querySearch) use ($advanced_filter) {
            $querySearch->whereIn('program_suggest', $advanced_filter['initial_programs']);
        })->when(!empty($advanced_filter['status_lead']), function ($querySearch) use ($advanced_filter) {
            $querySearch->whereIn('status_lead', $advanced_filter['status_lead']);
        })->
        when(!empty($advanced_filter['pic']), function ($querySearch) use ($advanced_filter) {
            $querySearch->whereIn('client.pic_id', $advanced_filter['pic']);
        })->
        when(!empty($advanced_filter['active_status']), function ($querySearch) use ($advanced_filter) {
            $querySearch->whereIn('client.st_statusact', $advanced_filter['active_status']);
        }, function ($subQuery) {
            $subQuery->where('client.st_statusact', 1);
        })->
        when(!empty($advanced_filter['start_joined_date']) && empty($advanced_filter['end_joined_date']), function ($querySearch) use ($advanced_filter) {
            $querySearch->whereDate('client.created_at', '>=', $advanced_filter['start_joined_date']);
        })->
        when(!empty($advanced_filter['end_joined_date']) && empty($advanced_filter['start_joined_date']), function ($querySearch) use ($advanced_filter) {
            $querySearch->whereDate('client.created_at', '<=', $advanced_filter['end_joined_date']);
        })->
        when(!empty($advanced_filter['start_joined_date']) && !empty($advanced_filter['end_joined_date']), function ($querySearch) use ($advanced_filter) {
            $querySearch->whereBetween('client.created_at', [$advanced_filter['start_joined_date'], $advanced_filter['end_joined_date']]);
        });

        return $asDatatables === false ? $query->orderBy('first_name', 'asc')->get() : $query;
    }

    public function getAlumniMentees($groupBy = false, $asDatatables = false, $month = null, $advanced_filter=[])
    {
        # has finish our admission program
        $query = Client::select([
                'client.*',
                'parent.mail as parent_mail',
                'parent.phone as parent_phone'
            ])->selectRaw('RTRIM(CONCAT(parent.first_name, " ", COALESCE(parent.last_name, ""))) as parent_name')->
            leftJoin('tbl_client_relation as relation', 'relation.child_id', '=', 'client.id')->
            leftJoin('tbl_client as parent', 'parent.id', '=', 'relation.parent_id')->
            
            // whereHas('clientProgram', function ($subQuery) {
            //     $subQuery->whereHas('program.main_prog', function ($subQuery_2) {
            //         $subQuery_2->where('prog_name', 'Admissions Mentoring');
            //     })->where('status', 1)->where('prog_running_status', 2);
            // })->
            // whereDoesntHave('clientProgram', function ($subQuery) {
            //     $subQuery->whereIn('status', [0, 1])->whereIn('prog_running_status', [0, 1]);
            // })->

            where('client.category', 'alumni-mentee')->
            when($month, function ($subQuery) use ($month) {
                $subQuery->whereMonth('client.created_at', date('m', strtotime($month)))->whereYear('client.created_at', date('Y', strtotime($month)));
            })->whereHas('roles', function ($subQuery) {
                $subQuery->where('role_name', 'student');
            })->
            when(!empty($advanced_filter['school_name']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereIn('school_name', $advanced_filter['school_name']);
            })->
            when(!empty($advanced_filter['graduation_year']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereIn('client.graduation_year_real', $advanced_filter['graduation_year']);
            })->
            isNotSalesAdmin()->
            isUsingAPI()->
            isActive()->
            isVerified();

        return $asDatatables === false ?
            ($groupBy === true ? $query->addSelect(DB::raw('YEAR(client.created_at) AS year'))->get()->groupBy('year') : $query->get())
            : $query;
    }

    public function getAlumniMenteesSiblings()
    {
        $query = Client::with(['parents', 'parents.childrens'])->whereHas('clientProgram.program.main_prog', function ($subQuery) {
                $subQuery->where('prog_name', 'Admissions Mentoring')->where('status', 1)->where('prog_running_status', 2);
            })->whereDoesntHave('clientProgram', function ($subQuery) {
                $subQuery->whereHas('program.main_prog', function ($subQuery_2) {
                    $subQuery_2->where('prog_name', 'Admissions Mentoring');
                })->where('status', 1)->where('prog_running_status', '!=', 2);
            })->whereHas('roles', function ($subQuery) {
                $subQuery->where('role_name', 'student');
            })->whereHas('parents', function ($subQuery) {
                $subQuery->has('childrens', '>', 1);
            });

        return $query->get();
    }

    public function getAlumniNonMentees($groupBy = false, $asDatatables = false, $month = null, $advanced_filter = [])
    {
        # has finish our program and hasnt joined admission program
        $query = Client::select([
                'client.*',
                'parent.mail as parent_mail',
                'parent.phone as parent_phone'
            ])->
            selectRaw('RTRIM(CONCAT(parent.first_name, " ", COALESCE(parent.last_name, ""))) as parent_name')->
            leftJoin('tbl_client_relation as relation', 'relation.child_id', '=', 'client.id')->
            leftJoin('tbl_client as parent', 'parent.id', '=', 'relation.parent_id')->
            
            // whereDoesntHave('clientProgram', function ($subQuery) {
            //     $subQuery->whereHas('program.main_prog', function ($subQuery_2) {
            //         $subQuery_2->where('prog_name', 'Admissions Mentoring');
            //     })->whereIn('status', [1]);
            // })->
            // // whereHas('clientProgram', function ($subQuery) {
            // //     $subQuery->where('status', 1)->where('prog_running_status', 2);
            // // })->
            // whereHas('clientProgram', function ($subQuery) {
            //     $subQuery->whereHas('program.main_prog', function ($subQuery_2) {
            //         $subQuery_2->where('prog_name', '!=', 'Admissions Mentoring');
            //     })->where('status', 1)->where('prog_running_status', 2);
            // })->
            // whereDoesntHave('clientProgram', function ($subQuery) {
            //     $subQuery->whereIn('status', [0, 1])->whereIn('prog_running_status', [0, 1]);
            // })->

            where('client.category', 'alumni-non-mentee')->
            when($month, function ($subQuery) use ($month) {
                $subQuery->whereMonth('client.created_at', date('m', strtotime($month)))->whereYear('client.created_at', date('Y', strtotime($month)));
            })->whereHas('roles', function ($subQuery) {
                $subQuery->where('role_name', 'student');
            })->
            when(!empty($advanced_filter['school_name']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereIn('school_name', $advanced_filter['school_name']);
            })->
            when(!empty($advanced_filter['graduation_year']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereIn('client.graduation_year_real', $advanced_filter['graduation_year']);
            })->
            isNotSalesAdmin()->
            isUsingAPI()->
            isActive()->
            isVerified();

        return $asDatatables === false ?
            ($groupBy === true ? $query->addSelect(DB::raw('YEAR(client.created_at) AS year'))->get()->groupBy('year') : $query->get())
            : $query;
    }

    public function getParents($asDatatables = false, $month = null, $advanced_filter = [])
    {
        $query = Client::select([
                'client.id',
                'client.first_name',
                'client.last_name',
                'client.full_name',
                'client.lead_source',
                'client.mail',
                'client.phone',
                'client.graduation_year_real',
                'client.dob',
                'client.created_at',
                'client.updated_at',
                'children.mail as children_mail',
                'children.phone as children_phone'
            ])->
            selectRaw('GROUP_CONCAT(RTRIM(CONCAT(children.first_name, " ", COALESCE(children.last_name, ""))) SEPARATOR ", ") as children_name')->
            selectRaw("IF((SELECT COUNT(*) FROM tbl_client_relation WHERE parent_id = client.id) > 1,true,false) as have_siblings")->
            leftJoin('tbl_client_relation as relation', 'relation.parent_id', '=', 'client.id')->
            leftJoin('tbl_client as children', 'children.id', '=', 'relation.child_id')->
            whereHas('roles', function ($subQuery) {
                $subQuery->where('role_name', 'Parent');
            })->
            when($month, function ($subQuery) use ($month) {
                $subQuery->whereMonth('client.created_at', date('m', strtotime($month)))->whereYear('client.created_at', date('Y', strtotime($month)));
            })->
            when(!empty($advanced_filter['have_siblings']), function ($subQuery) use ($advanced_filter) {
                $subQuery->where(DB::raw('IF((SELECT COUNT(*) FROM tbl_client_relation WHERE parent_id = client.id) > 1,true,false)'), $advanced_filter['have_siblings']);
            })->
            isActive()->
            isVerified();


        if ($asDatatables === false) {
            // $query->groupBy('relation.parent_id');
            $query->groupBy('client.id');
        }

        return $asDatatables === false ? $query->get() : $query->groupBy('client.id');
    }

    public function getTeachers($asDatatables = false, $month = null)
    {
        $query = Client::when($month, function ($subQuery) use ($month) {
            $subQuery->whereMonth('client.created_at', date('m', strtotime($month)))->whereYear('client.created_at', date('Y', strtotime($month)));
        })->
        isTeacher()->isActive()->isVerified();

        return $asDatatables === false ? $query->get() : $query;
    }

    public function getClientHotLeads($initialProgram)
    {
        $model = Client::select([
                'client.*',
                'parent.mail as parent_mail',
                'parent.phone as parent_phone'
            ])->
            selectRaw('RTRIM(CONCAT(parent.first_name, " ", COALESCE(parent.last_name, ""))) as parent_name')->
            leftJoin('tbl_client_relation as relation', 'relation.child_id', '=', 'client.id')->
            leftJoin('tbl_client as parent', 'parent.id', '=', 'relation.parent_id')->
            withAndWhereHas('leadStatus', function ($subQuery) use ($initialProgram) {
                $subQuery->where('type', 'program')->where('total_result', '>=', '0.65')->where('status', 1)->where('tbl_initial_program_lead.name', $initialProgram);
            })->
            isActive()->
            orderByDesc(
                DB::table('tbl_client_lead_tracking AS clt')->
                    leftJoin('tbl_initial_program_lead AS ipl', 'ipl.id', '=', 'clt.initialprogram_id')->
                    select('clt.total_result')->
                    whereColumn('clt.client_id', 'client.id')->
                    where('clt.type', 'lead')->
                    where('ipl.name', $initialProgram)->
                    where('clt.status', 1)->
                    groupBy('clt.client_id')
            )->
            isNotSalesAdmin()->
            isUsingAPI()->
            isVerified();

        return $model;
    }

    public function getUnverifiedStudent($asDatatables = false, $month = null, $advanced_filter = [])
    {
        $query = Client::isStudent()->isActive()->isNotVerified();
        return $asDatatables === false ? $query->orderBy('client.created_at', 'desc')->get() : $query;
    }

    public function getUnverifiedParent($asDatatables = false, $month = null, $advanced_filter = [])
    {
        $query = Client::isParent()->isActive()->isNotVerified();
        return $asDatatables === false ? $query->orderBy('client.created_at', 'desc')->get() : $query;
    }

    public function getUnverifiedTeacher($asDatatables = false, $month = null, $advanced_filter = [])
    {
        $query = Client::isTeacher()->isActive()->isNotVerified();
        return $asDatatables === false ? $query->orderBy('client.created_at', 'desc')->get() : $query;
    }

    public function getInactiveStudent($asDatatables = false, $month = null, $advanced_filter = [])
    {
        $query = Client::select([
                'client.*',
                'parent.mail as parent_mail',
                'parent.phone as parent_phone'
            ])->
            selectRaw('RTRIM(CONCAT(parent.first_name, " ", COALESCE(parent.last_name, ""))) as parent_name')->
            leftJoin('tbl_client_relation as relation', 'relation.child_id', '=', 'client.id')->
            leftJoin('tbl_client as parent', 'parent.id', '=', 'relation.parent_id')->
            when($month, function ($subQuery) use ($month) {
                $subQuery->whereMonth('client.created_at', date('m', strtotime($month)))->whereYear('client.created_at', date('Y', strtotime($month)));
            })->
            when(!empty($advanced_filter['school_name']), function ($subQuery) use ($advanced_filter) {
                $subQuery->whereIn('school_name', $advanced_filter['school_name']);
            })->
            when(!empty($advanced_filter['graduation_year']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereIn('client.graduation_year_real', $advanced_filter['graduation_year']);
            })->
            when(!empty($advanced_filter['leads']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereIn('lead_source', $advanced_filter['leads']);
            })->
            when(!empty($advanced_filter['initial_programs']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereIn('program_suggest', $advanced_filter['initial_programs']);
            })->
            when(!empty($advanced_filter['status_lead']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereIn('status_lead', $advanced_filter['status_lead']);
            })->
            when(!empty($advanced_filter['pic']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereIn('client.pic_id', $advanced_filter['pic']);
            })->
            when(!empty($advanced_filter['start_joined_date']) && empty($advanced_filter['end_joined_date']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereDate('client.created_at', '>=', $advanced_filter['start_joined_date']);
            })->
            when(!empty($advanced_filter['end_joined_date']) && empty($advanced_filter['start_joined_date']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereDate('client.created_at', '<=', $advanced_filter['end_joined_date']);
            })->
            when(!empty($advanced_filter['start_joined_date']) && !empty($advanced_filter['end_joined_date']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereBetween('client.created_at', [$advanced_filter['start_joined_date'], $advanced_filter['end_joined_date']]);
            })->
            isStudent()->
            isNotActive();

        return $asDatatables === false ? $query->orderBy('client.updated_at', 'desc')->get() : $query->orderBy('first_name', 'asc');
    }

    public function getInactiveParent($asDatatables = false, $month = null, $advanced_filter = [])
    {
        $query = Client::select([
                'client.*',
                'children.mail as children_mail',
                'children.phone as children_phone'
            ])->
            selectRaw('RTRIM(CONCAT(children.first_name, " ", COALESCE(children.last_name, ""))) as children_name')->
            leftJoin('tbl_client_relation as relation', 'relation.parent_id', '=', 'client.id')->
            leftJoin('tbl_client as children', 'children.id', '=', 'relation.child_id')->
            when($month, function ($subQuery) use ($month) {
                $subQuery->whereMonth('client.created_at', date('m', strtotime($month)))->whereYear('client.created_at', date('Y', strtotime($month)));
            })->
            isParent()->
            isNotActive();

            return $asDatatables === false ? $query->orderBy('client.updated_at', 'desc')->get() : $query->orderBy('first_name', 'asc');
    }

    public function getInactiveTeacher($asDatatables = false, $month = null, $advanced_filter = [])
    {
        $query = Client::isTeacher()->isNotActive();
        return $asDatatables === false ? $query->get() : $query;
    }

    /* ~ END*/

    /* for API External use */

    public function getExistingMenteesAPI()
    {
        return Client::withAndWhereHas('clientProgram', function ($subQuery) {
            $subQuery->with(['clientMentor', 'clientMentor.roles' => function ($subQuery_2) {
                $subQuery_2->where('role_name', 'Mentor');
            }])->whereHas('program', function ($subQuery_2) {
                $subQuery_2->whereHas('main_prog', function ($subQuery_3) {
                    $subQuery_3->where('prog_name', 'Admissions Mentoring');
                });
            })->where('status', 1)->where('prog_running_status', '!=', 2); # 1 success, 2 done
        })->whereHas('roles', function ($subQuery) {
            $subQuery->where('role_name', 'student');
        })->get();
    }

    public function getExistingMentorsAPI()
    {
        return User::with('educations')->withAndWhereHas('roles', function ($subQuery) {
            $subQuery->where('role_name', 'Mentor');
        })->whereNotNull('email')->where('active', 1)->get();
    }

    public function getExistingAlumnisAPI()
    {
        $alumni_mentees = $this->getAlumniMentees();
        $alumni_nonmentees = $this->getAlumniNonMentees();

        $alumni = $alumni_mentees->merge($alumni_nonmentees);
        return $alumni;
    }
    /* ~ API External end */

    public function getAlumnisDataTables()
    {
        $query = Client::whereHas('clientProgram', function ($q2) {
            $q2->whereIn('prog_running_status', [2]);
        })->withCount([
            'clientProgram as client_program_count' => function ($query) {
                $query->whereIn('prog_running_status', [0, 1, 2])->whereHas('program', function ($q2) {
                    $q2->whereHas('main_prog', function ($q3) {
                        $q3->where('prog_name', 'Admissions Mentoring');
                    });
                });
            },
            'clientProgram as client_program_finish_count' => function ($query) {
                $query->where('prog_running_status', 2)->whereHas('program', function ($q2) {
                    $q2->whereHas('main_prog', function ($q3) {
                        $q3->where('prog_name', 'Admissions Mentoring');
                    });
                });
            }
        ])->havingRaw('client_program_count = client_program_finish_count');

        return Datatables::eloquent($query)
            ->addColumn('parent_name', function ($data) {
                return $data->parents()->count() > 0 ? $data->parents()->first()->first_name . ' ' . $data->parents()->first()->last_name : null;
            })
            ->addColumn('parent_phone', function ($data) {
                return $data->parents()->count() > 0 ? $data->parents()->first()->phone : null;
            })
            ->addColumn('children_name', function ($data) {
                return $data->childrens()->count() > 0 ? $data->childrens()->first()->first_name . ' ' . $data->childrens()->first()->last_name : null;
            })
            ->addColumn('parent_name', function ($data) {
                return $data->parents()->count() > 0 ? $data->parents()->first()->first_name . ' ' . $data->parents()->first()->last_name : null;
            })
            ->addColumn('parent_phone', function ($data) {
                return $data->parents()->count() > 0 ? $data->parents()->first()->phone : null;
            })
            ->addColumn('children_name', function ($data) {
                return $data->childrens()->count() > 0 ? $data->childrens()->first()->first_name . ' ' . $data->childrens()->first()->last_name : null;
            })
            ->rawColumns(['address'])
            ->make(true);
    }

    // public function getAlumnis()
    // {
    //     return UserClient::whereHas('clientProgram', function ($q2) {
    //         $q2->whereIn('prog_running_status', [2]);
    //     })->withCount([
    //         'clientProgram as client_program_count' => function ($query) {
    //             $query->whereIn('prog_running_status', [0, 1, 2])->whereHas('program', function ($q2) {
    //                 $q2->whereHas('main_prog', function ($q3) {
    //                     $q3->where('prog_name', 'Admissions Mentoring');
    //                 });
    //             });
    //         },
    //         'clientProgram as client_program_finish_count' => function ($query) {
    //             $query->where('prog_running_status', 2)->whereHas('program', function ($q2) {
    //                 $q2->whereHas('main_prog', function ($q3) {
    //                     $q3->where('prog_name', 'Admissions Mentoring');
    //                 });
    //             });
    //         }
    //     ])->havingRaw('client_program_count = client_program_finish_count')->pluck('tbl_client.id')->toArray();
    // }

    public function getMenteesDataTables()
    {
        $roleName = "mentee";

        // $query = Client::whereNotIn('id', $this->ALUMNI_IDS)->whereHas('roles', function ($query2) use ($roleName) {
        $query = Client::whereHas('roles', function ($query2) use ($roleName) {
            $query2->where('role_name', $roleName);
        })->orderBy('created_at', 'desc');

        return Datatables::eloquent($query)
            ->addColumn('parent_name', function ($data) {
                return $data->parents()->count() > 0 ? $data->parents()->first()->first_name . ' ' . $data->parents()->first()->last_name : null;
            })
            ->addColumn('parent_phone', function ($data) {
                return $data->parents()->count() > 0 ? $data->parents()->first()->phone : null;
            })
            ->addColumn('children_name', function ($data) {
                return $data->childrens()->count() > 0 ? $data->childrens()->first()->first_name . ' ' . $data->childrens()->first()->last_name : null;
            })
            ->addColumn('parent_name', function ($data) {
                return $data->parents()->count() > 0 ? $data->parents()->first()->first_name . ' ' . $data->parents()->first()->last_name : null;
            })
            ->addColumn('parent_phone', function ($data) {
                return $data->parents()->count() > 0 ? $data->parents()->first()->phone : null;
            })
            ->addColumn('children_name', function ($data) {
                return $data->childrens()->count() > 0 ? $data->childrens()->first()->first_name . ' ' . $data->childrens()->first()->last_name : null;
            })
            ->rawColumns(['address'])
            ->make(true);
    }

    public function getNonMenteesDataTables()
    {
        $roleName = "mentee";
        $alumnis = UserClient::whereHas('clientProgram', function ($q2) {
            $q2->whereIn('prog_running_status', [2]);
        })->withCount([
            'clientProgram as client_program_count' => function ($query) {
                $query->whereIn('prog_running_status', [0, 1, 2])->whereHas('program', function ($q2) {
                    $q2->whereHas('main_prog', function ($q3) {
                        $q3->where('prog_name', 'Admissions Mentoring');
                    });
                });
            },
            'clientProgram as client_program_finish_count' => function ($query) {
                $query->where('prog_running_status', 2)->whereHas('program', function ($q2) {
                    $q2->whereHas('main_prog', function ($q3) {
                        $q3->where('prog_name', 'Admissions Mentoring');
                    });
                });
            }
        ])->havingRaw('client_program_count = client_program_finish_count')->pluck('tbl_client.id')->toArray();

        $query = Client::whereNotIn('id', $alumnis)->whereHas('roles', function ($query2) use ($roleName) {
            $query2->where('role_name', $roleName);
        })->whereHas('clientProgram', function ($subQuery) {
            $subQuery->whereHas('program', function ($query2) {
                $query2->whereHas('main_prog', function ($q3) {
                    $q3->where('prog_name', '!=', 'Admissions Mentoring');
                });
            });
        });

        return Datatables::eloquent($query)
            ->addColumn('parent_name', function ($data) {
                return $data->parents()->count() > 0 ? $data->parents()->first()->first_name . ' ' . $data->parents()->first()->last_name : null;
            })
            ->addColumn('parent_phone', function ($data) {
                return $data->parents()->count() > 0 ? $data->parents()->first()->phone : null;
            })
            ->addColumn('children_name', function ($data) {
                return $data->childrens()->count() > 0 ? $data->childrens()->first()->first_name . ' ' . $data->childrens()->first()->last_name : null;
            })
            ->addColumn('parent_name', function ($data) {
                return $data->parents()->count() > 0 ? $data->parents()->first()->first_name . ' ' . $data->parents()->first()->last_name : null;
            })
            ->addColumn('parent_phone', function ($data) {
                return $data->parents()->count() > 0 ? $data->parents()->first()->phone : null;
            })
            ->addColumn('children_name', function ($data) {
                return $data->childrens()->count() > 0 ? $data->childrens()->first()->first_name . ' ' . $data->childrens()->first()->last_name : null;
            })
            ->rawColumns(['address'])
            ->make(true);
    }

    # function below
    # is used on the dashboard to fetch the list client 
    # and the difference between the above function 
    # is that the above function is not using ordering by created at
    public function getAllClientByRoleAndDate($roleName, $month = null) # mentee, parent, teacher
    {
        $alumnis = UserClient::whereHas('clientProgram', function ($q2) {
            $q2->whereIn('prog_running_status', [2]);
        })->withCount([
            'clientProgram as client_program_count' => function ($query) {
                $query->whereIn('prog_running_status', [0, 1, 2])->whereHas('program', function ($q2) {
                    $q2->whereHas('main_prog', function ($q3) {
                        $q3->where('prog_name', 'Admissions Mentoring');
                    });
                });
            },
            'clientProgram as client_program_finish_count' => function ($query) {
                $query->where('prog_running_status', 2)->whereHas('program', function ($q2) {
                    $q2->whereHas('main_prog', function ($q3) {
                        $q3->where('prog_name', 'Admissions Mentoring');
                    });
                });
            }
        ])->havingRaw('client_program_count = client_program_finish_count')->pluck('tbl_client.id')->toArray();

        $selectQuery = UserClient::when($roleName == "alumni", function ($query) {
            $query->whereHas('clientProgram', function ($q2) {
                $q2->whereIn('prog_running_status', [2]);
            })->withCount([
                'clientProgram as client_program_count' => function ($query) {
                    $query->whereIn('prog_running_status', [0, 1, 2])->whereHas('program', function ($q2) {
                        $q2->whereHas('main_prog', function ($q3) {
                            $q3->where('prog_name', 'Admissions Mentoring');
                        });
                    });
                },
                'clientProgram as client_program_finish_count' => function ($query) {
                    $query->where('prog_running_status', 2)->whereHas('program', function ($q2) {
                        $q2->whereHas('main_prog', function ($q3) {
                            $q3->where('prog_name', 'Admissions Mentoring');
                        });
                    });
                }
            ])->havingRaw('client_program_count = client_program_finish_count');
        }, function ($query) use ($roleName, $alumnis) {
            $query->when($roleName == 'mentee', function ($query2) use ($alumnis) {
                $query2->whereNotIn('tbl_client.id', $alumnis);
            })->whereHas('roles', function ($query2) use ($roleName) {
                $query2->where('role_name', $roleName);
            });
        })->when($month, function ($query) use ($month) {
            $query->whereMonth('tbl_client.created_at', date('m', strtotime($month)))->whereYear('tbl_client.created_at', date('Y', strtotime($month)));
        });

        if ($roleName == "alumni") {
            $get = $selectQuery->addSelect(DB::raw('YEAR(tbl_client.created_at) AS year'))->orderBy('tbl_client.created_at', 'desc')->get()->groupBy('year');
        } else {
            $get = $selectQuery->orderBy('tbl_client.created_at', 'desc')->get();
        }

        return $get;
    }

    public function getAllClientByRoleAndStatus($roleName, $statusClient)
    {
        return UserClient::whereHas('roles', function ($query) use ($roleName) {
            $query->where('role_name', $roleName);
        })->where('st_statuscli', $statusClient)->get();
    }

    public function getAllChildrenWithNoParents($parentId = null)
    {
        if ($parentId)
            $parentChilds = UserClient::find($parentId)->childrens()->pluck('tbl_client.id')->toArray();

        return UserClient::whereHas('roles', function ($query) {
            $query->where('role_name', 'Student');
        })->doesntHave('parents')->when($parentChilds, function ($query) use ($parentChilds) {
            $query->orWhereIn('id', $parentChilds);
        })->get();
    }

    public function getClientById($clientId)
    {
        return UserClient::with(['childrens'])->withTrashed()->find($clientId);
    }

    public function getClientByUUID($clientUUID)
    {
        return UserClient::where('uuid', $clientUUID)->first();
    }

    public function getClientsById(array $clientIds)
    {
        return UserClient::whereIn('id', $clientIds)->get();
    }

    public function findHandledClient(int $clientId)
    {
        return UserClient::where('id', $clientId)->filterBasedOnPIC()->exists();
    }

    public function getClientByMonthCreatedAt(array $month)
    {
        return UserClient::whereIn(DB::raw('MONTH(created_at)'), $month)->whereYear('created_at', date('Y-m-d'))->get();
    }

    public function getClientByPhoneNumber($phoneNumber)
    {
        if (substr($phoneNumber, 0, 1) == "+" || substr($phoneNumber, 0, 1) == 0) 
            $phoneNumber = substr($phoneNumber, 4);

        if (substr($phoneNumber, 0, 2) == 62)
            $phoneNumber = substr($phoneNumber, 2);

        return UserClient::whereRaw('SUBSTR(phone, 4) LIKE ?', ['%' . $phoneNumber . '%'])->first();
    }

    public function getClientBySchool($schoolId)
    {
        return UserClient::withTrashed()->where('sch_id', $schoolId)->get();
    }

    public function getClientInSchool(array $schoolIds)
    {
        return UserClient::whereIn('sch_id', $schoolIds)->get();
    }

    public function getViewClientById($clientId)
    {
        return Client::withTrashed()->find($clientId);
    }

    public function getViewClientByUUID($clientUUID)
    {
        return Client::where('uuid', $clientUUID)->first();
    }

    public function checkIfClientIsMentee($clientId)
    {
        return UserClient::whereHas('roles', function ($query) {
            $query->where('role_name', 'Mentee');
        })->where('id', $clientId)->first();
    }

    public function deleteClient($clientId)
    {
        return UserClient::destroy($clientId);
    }

    public function createClient($role, array $clientDetails)
    {
        $roleId = $this->roleRepository->getRoleByName($role);
        $client = UserClient::create($clientDetails);
        $client->roles()->attach($roleId, [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        return $client;
    }

    public function createClientAdditionalInfo(array $infoDetails)
    {
        return UserClientAdditionalInfo::insert($infoDetails);
    }

    public function addRole($clientId, $role)
    {
        $roleId = $this->roleRepository->getRoleByName($role);
        $client = UserClient::find($clientId);
        # roles id 5 = Mentee
        if ($client->roles()->where('tbl_roles.id', 5)->count() == 0) {
            $client->roles()->attach($roleId);
        }
        return $client;
    }

    public function removeRole($clientId, $role)
    {
        $roleId = $this->roleRepository->getRoleByName($role);
        $client = UserClient::find($clientId);
        # roles id 5 = Mentee
        if ($client->roles()->where('tbl_roles.id', 5)->count() > 0) {
            $client->roles()->detach($roleId);
        }
        return $client;
    }

    public function updateClient($clientId, array $newDetails)
    {
        $updated = tap(UserClient::whereId($clientId)->first())->update($newDetails);
        // UserClient::dispatchUpdated($updated);
        return $updated;
    }

    public function updateClients(array $clientIds, array $newDetails)
    {
        return UserClient::whereIn('id', $clientIds)->withTrashed()->update($newDetails);
    }

    public function getParentsByStudentId($studentId)
    {
        $student = UserClient::find($studentId);
        return $student->parents()->pluck('tbl_client.id')->toArray();
    }

    public function getParentByParentName($parentName)
    {
        return UserClient::where(DB::raw('CONCAT(first_name, " ", COALESCE(last_name, ""))'), 'like', '%' . $parentName . '%')->whereHas('roles', function ($query) {
            $query->where('role_name', 'Parent');
        })->first();
    }

    # connecting student with parents
    public function createClientRelation($parentId, $studentId)
    {
        $student = UserClient::where('id', $studentId)->first();

        # why sync?
        # to create and update all at once
        $student->parents()->sync($parentId);
        return $student;
    }

    public function removeClientRelation($parentId, $studentId)
    {
        $student = UserClient::where('id', $studentId)->first();

        $student->parents()->detach($parentId);
        return $student;
    }

    public function createManyClientRelation($parentId, $studentId)
    {
        $parent = UserClient::find($parentId);

        # why sync?
        # to create and update all at once
        // $parent->childrens()->sync($childrens);
        $parent->childrens()->syncWithoutDetaching($studentId);
        return $parent;
    }

    public function createDestinationCountry($studentId, $destinationCountryDetails)
    {
        $arrayCountry = [];
        $student = UserClient::find($studentId);
        if(isset($student->destinationCountries)){
            foreach ($student->destinationCountries as $country) {
                $arrayCountry[] =  $country->id;
            }
        }

        $merge = array_merge($arrayCountry, $destinationCountryDetails);

        $student->destinationCountries()->sync($merge);
        return $student;
    }

    public function syncDestinationCountry($studentId, $destinationCountryDetails)
    {
        # this function similar to function above
        # the differences is that this function does not fetch the existing destination country from the database
        # just using the new destination country from incoming request
        $student = UserClient::find($studentId);
        $student->destinationCountries()->sync($destinationCountryDetails);

        return $student->destinationCountries;
    }

    public function getInterestedProgram($studentId)
    {
        $student = UserClient::find($studentId);
        return $student->interestPrograms;
    }

    public function createInterestProgram($studentId, $interestProgramDetails)
    {
        $student = UserClient::find($studentId);
        $student->interestPrograms()->sync($interestProgramDetails);
        return $student;
    }

    public function createInterestUniversities($studentId, $interestUnivDetails)
    {
        $student = UserClient::find($studentId);
        $student->interestUniversities()->sync($interestUnivDetails);
        return $student;
    }

    public function createInterestMajor($studentId, $interestMajorDetails)
    {
        $student = UserClient::find($studentId);
        $student->interestMajor()->sync($interestMajorDetails);
        return $student;
    }

    public function updateActiveStatus($clientId, $newStatus)
    {
        return UserClient::find($clientId)->update(['st_statusact' => $newStatus]);
    }

    public function checkAllProgramStatus($clientId)
    {
        $client = UserClient::find($clientId);
        return $client->clientProgram()->where('status', 1)->whereNot('prog_running_status', 2)->count() == 0 ? "completed" : "notyet";
    }

    # dashboard
    public function getCountTotalClientByStatus($status, $month = null)
    {
        $client = UserClient::whereHas('roles', function ($query) {
            $query->where('role_name', 'mentee');
        })->pluck('id')->toArray();

        return Client::where('st_statuscli', $status)->when($month, function ($query) use ($month) {
            $query->whereMonth('created_at', date('m', strtotime($month)))->whereYear('created_at', date('Y', strtotime($month)));
        })->whereHas('roles', function ($query) {
            $query->where('role_name', 'Student');
        })->whereNotIn('client.id', $client)->count();
    }

    public function getClientByStatus($status, $month = null)
    {
        $client = UserClient::whereHas('roles', function ($query) {
            $query->where('role_name', 'mentee');
        })->pluck('id')->toArray();

        return Client::where('st_statuscli', $status)->when($month, function ($query) use ($month) {
            $query->whereMonth('created_at', date('m', strtotime($month)))->whereYear('created_at', date('Y', strtotime($month)));
        })->whereHas('roles', function ($query) {
            $query->where('role_name', 'Student');
        })->whereNotIn('client.id', $client)->orderBy('created_at', 'desc')->get();
    }

    public function getMenteesBirthdayMonthly($month)
    {
        return Client::whereMonth('dob', date('m', strtotime($month)))->whereHas('roles', function ($query) {
                    $query->where('role_name', 'Student');
                })->where('st_statusact', 1)->
                orderBy(DB::raw('dayofmonth(dob)'), 'asc')->
                isNotSalesAdmin()->
                isUsingAPI()->
                get();
    }

    public function getStudentByStudentId($studentId)
    {
        return UserClient::where('st_id', $studentId)->whereHas('roles', function ($query) {
            $query->where('role_name', 'Student');
        })->first();
    }

    public function getStudentByStudentName($studentName)
    {
        // $studentName = explode(' ', $studentName);
        // return UserClient::where(DB::raw('CONCAT(first_name, " ", last_name)'), $studentName)->first();
        return UserClient::where(function ($extquery) use ($studentName) {

            $extquery->whereRaw("CONCAT(first_name, ' ', COALESCE(last_name, '')) = ?", [$studentName]);

            # search word by word 
            # and loop based on name length
            // for ($i = 0; $i < count($studentName); $i++) {

            //     # looping at least two times
            //     if ($i <= 1)
            //         $extquery = $extquery->whereRaw("CONCAT(first_name, ' ', COALESCE(last_name, '')) like ?", ['%' . $studentName[$i] . '%']);
            // }
        })->first();
    }

    # CRM
    public function getStudentFromV1()
    {
        return CRMStudent::select([
            'st_num',
            DB::raw('(CASE 
                WHEN st_id = "" THEN NULL ELSE st_id
            END) AS st_id'),
            DB::raw('(CASE 
                WHEN pr_id = 0 THEN NULL ELSE pr_id
            END) AS pr_id'),
            'st_firstname',
            DB::raw('(CASE 
                WHEN st_lastname = "" THEN NULL ELSE st_lastname
            END) AS st_lastname'),
            DB::raw('(CASE 
                WHEN st_mail = "" THEN NULL ELSE st_mail
            END) AS st_mail'),
            DB::raw('(CASE 
                WHEN st_phone = "" THEN NULL ELSE st_phone
            END) AS st_phone'),
            DB::raw('(CASE 
                WHEN st_dob = "" OR st_dob = "0000-00-00" THEN NULL ELSE st_dob
            END) AS st_dob'),
            DB::raw('(CASE 
                WHEN st_insta = "" THEN NULL ELSE st_insta
            END) AS st_insta'),
            DB::raw('(CASE 
                WHEN st_state = "" THEN NULL ELSE st_state
            END) AS st_state'),
            DB::raw('(CASE 
                WHEN st_city = "" THEN NULL ELSE st_city
            END) AS st_city'),
            DB::raw('(CASE 
                WHEN st_address = "" THEN NULL ELSE st_address
            END) AS st_address'),
            DB::raw('(CASE 
                WHEN sch_id = "" THEN NULL ELSE sch_id
            END) AS sch_id'),
            DB::raw('(CASE 
                WHEN st_grade = 0 THEN NULL ELSE st_grade
            END) AS st_grade'),
            'lead_id',
            DB::raw('(CASE 
                WHEN eduf_id = 0 THEN NULL ELSE eduf_id
            END) AS eduf_id'),
            'st_levelinterest',
            'prog_id',
            DB::raw('(CASE 
                WHEN st_abryear = "" THEN NULL ELSE st_abryear
            END) AS st_abryear'),
            'st_abrcountry',
            'st_abruniv',
            'st_abrmajor',
            'st_statusact',
            'st_note',
            'st_statuscli',
            DB::raw('(CASE 
                WHEN st_password = "" THEN NULL ELSE st_password
            END) AS st_password'),
            'st_datecreate'

        ])->get();
    }

    public function getParentFromV1()
    {
        return CRMParent::select([
            'pr_firstname',
            DB::raw('(CASE 
                WHEN pr_lastname = "" THEN NULL ELSE pr_lastname
            END) as pr_lastname'),
            DB::raw('(CASE 
                WHEN pr_mail = "" THEN NULL ELSE pr_mail
            END) as pr_mail'),
            DB::raw('(CASE 
                WHEN pr_phone = "" THEN NULL ELSE pr_phone
            END) as pr_phone'),
            DB::raw('(CASE 
                WHEN pr_dob = "" OR pr_dob = "0000-00-00" THEN NULL ELSE pr_dob
            END) as pr_dob'),
            DB::raw('(CASE 
                WHEN pr_insta = "" THEN NULL ELSE pr_insta
            END) as pr_insta'),
            DB::raw('(CASE 
                WHEN pr_state = "" THEN NULL ELSE pr_state
            END) as pr_state'),
            DB::raw('(CASE 
                WHEN pr_address = "" THEN NULL ELSE pr_address
            END) as pr_address'),
            DB::raw('(CASE 
                WHEN pr_password = "" THEN NULL ELSE pr_password
            END) as pr_password'),
        ])->where('pr_firstname', '!=', '')->orWhere('pr_lastname', '!=', '')->get();
    }

    public function checkExistingByPhoneNumber($phone)
    {
        # From tbl client
        $client_phone = UserClient::select('id', 'mail', 'phone')->whereNot('phone', null)->whereNot('phone', '')->get();
        $std_phone = $client_phone->map(function ($item, int $key) {
            return [
                'id' => $item['id'],
                'mail' => $item['mail'],
                'phone' => $this->setPhoneNumber($item['phone'])
            ];
        });

        $client = $std_phone->where('phone', $phone)->first();

        if (!isset($client)) {

            # From tbl client additional info
            $client_phone = UserClientAdditionalInfo::select('client_id', 'category', 'value')->where('category', 'phone')->whereNot('value', null)->whereNot('value', '')->get();
            $std_phone = $client_phone->map(function ($item, int $key) {
                return [
                    'id' => $item['client_id'],
                    'mail' => $item['category'] == 'mail' ? $item['value'] : null,
                    'phone' => $this->setPhoneNumber($item['value'])
                ];
            });

            $client = $std_phone->where('phone', $phone)->first();
        }

        return $client;
    }

    public function checkExistingByEmail($email)
    {
        # From tbl client
        $client_mail = UserClient::select('id', 'mail', 'phone')->whereNot('mail', null)->whereNot('mail', '')->get();

        $client = $client_mail->where('mail', $email)->first();

        if (!isset($client)) {

            # From tbl client additional info
            $client_mail = UserClientAdditionalInfo::select('client_id', 'category', 'value')->where('category', 'mail')->whereNot('value', null)->whereNot('value', '')->get();
            $getMail = $client_mail->map(function ($item, int $key) {
                return [
                    'id' => $item['client_id'],
                    'mail' => $item['category'] == 'mail' ? $item['value'] : null,
                    'phone' => $this->setPhoneNumber($item['value'])
                ];
            });

            $client = $getMail->where('mail', $email)->first();
        }

        return $client;
    }

    public function storeUniversityAcceptance($client, array $acceptanceDetails)
    {
        return $client->universityAcceptance()->attach($acceptanceDetails);
    }

    public function getClientHasUniversityAcceptance()
    {
        return Datatables::eloquent(ClientAcceptance::query())->make(true);
    }

    public function addInterestProgram($studentId, $interestProgram)
    {
        $student = UserClient::find($studentId);
        $student->interestPrograms()->attach($interestProgram);
        return $student;
    }

    public function removeInterestProgram($studentId, $interestProgram, $progId)
    {
        $student = UserClient::find($studentId);
        $student->interestPrograms()->wherePivot('id', $interestProgram)->detach($progId);
        return $student;
    }

    /* trash */

    public function getDeletedStudents($asDatatables = false, $advanced_filter = [])
    {
        $query = Client::select([
                    'client.*',
                    'parent.mail as parent_mail',
                    'parent.phone as parent_phone'
                ])->
                selectRaw('RTRIM(CONCAT(parent.first_name, " ", COALESCE(parent.last_name, ""))) as parent_name')->
                leftJoin('tbl_client_relation as relation', 'relation.child_id', '=', 'client.id')->
                leftJoin('tbl_client as parent', 'parent.id', '=', 'relation.parent_id')->
                whereHas('roles', function($subQuery) {
                    $subQuery->where('role_name', 'Student');
                })->
                when(!empty($advanced_filter['school_name']), function ($subQuery) use ($advanced_filter) {
                    $subQuery->whereIn('school_name', $advanced_filter['school_name']);
                })->
                when(!empty($advanced_filter['graduation_year']), function ($querySearch) use ($advanced_filter) {
                    $querySearch->whereIn('client.graduation_year_real', $advanced_filter['graduation_year']);
                })->
                when(!empty($advanced_filter['leads']), function ($querySearch) use ($advanced_filter) {
                    $querySearch->whereIn('lead_source', $advanced_filter['leads']);
                })->
                when(!empty($advanced_filter['pic']), function ($querySearch) use ($advanced_filter) {
                    $querySearch->whereIn('client.pic_id', $advanced_filter['pic']);
                })->
                when(!empty($advanced_filter['start_joined_date']) && empty($advanced_filter['end_joined_date']), function ($querySearch) use ($advanced_filter) {
                    $querySearch->whereDate('client.created_at', '>=', $advanced_filter['start_joined_date']);
                })->
                when(!empty($advanced_filter['end_joined_date']) && empty($advanced_filter['start_joined_date']), function ($querySearch) use ($advanced_filter) {
                    $querySearch->whereDate('client.created_at', '<=', $advanced_filter['end_joined_date']);
                })->
                when(!empty($advanced_filter['start_joined_date']) && !empty($advanced_filter['end_joined_date']), function ($querySearch) use ($advanced_filter) {
                    $querySearch->whereBetween('client.created_at', [$advanced_filter['start_joined_date'], $advanced_filter['end_joined_date']]);
                })->
                when(!empty($advanced_filter['start_deleted_date']) && empty($advanced_filter['end_deleted_date']), function ($querySearch) use ($advanced_filter) {
                    $querySearch->whereDate('client.deleted_at', '>=', $advanced_filter['start_deleted_date']);
                })->
                when(!empty($advanced_filter['end_deleted_date']) && empty($advanced_filter['start_deleted_date']), function ($querySearch) use ($advanced_filter) {
                    $querySearch->whereDate('client.deleted_at', '<=', $advanced_filter['end_deleted_date']);
                })->
                when(!empty($advanced_filter['start_deleted_date']) && !empty($advanced_filter['end_deleted_date']), function ($querySearch) use ($advanced_filter) {
                    $querySearch->whereBetween('client.deleted_at', [$advanced_filter['start_deleted_date'], $advanced_filter['end_deleted_date']]);
                })->
                // orderBy('deleted_at', 'desc')->
                onlyTrashed();
        return $asDatatables === false ? $query->get() : $query;
    }

    public function getDeletedParents($asDatatables = false)
    {
        $query = Client::select([
                    'client.*',
                    'children.mail as children_mail',
                    'children.phone as children_phone'
                ])->
                selectRaw('GROUP_CONCAT(RTRIM(CONCAT(children.first_name, " ", COALESCE(children.last_name, ""))) SEPARATOR ", ") as children_name')->
                // selectRaw('RTRIM(CONCAT(children.first_name, " ", COALESCE(children.last_name, ""))) as children_name')->
                leftJoin('tbl_client_relation as relation', 'relation.parent_id', '=', 'client.id')->
                leftJoin('tbl_client as children', 'children.id', '=', 'relation.child_id')->
                whereHas('roles', function ($subQuery) {
                    $subQuery->where('role_name', 'Parent');
                })->
                onlyTrashed()->
                groupBy('client.id');
        return $asDatatables === false ? $query->get() : $query;
    }
    
    public function getDeletedTeachers($asDatatables = false)
    {
        $query = Client::whereHas('roles', function ($query) {
                    $query->where('role_name', 'Teacher/Counselor');
                })->
                orderBy('deleted_at', 'desc')->
                onlyTrashed();
        return $asDatatables === false ? $query->get() : $query;
    }

    /* ~ END */

    public function getAllRawClientDataTables($roleName, $asDatatables = false, $advanced_filter = [])
    {
        $query = ViewRawClient::whereHas('roles', function ($query2) use ($roleName) {
                    switch ($roleName) {
                        case 'student':
                            $query2->whereIn('role_name', ['student', 'parent'])
                                ->whereRaw(DB::raw('(CASE WHEN roles = "Parent" THEN count_second_client = 0 ELSE count_second_client >= 0 END)'));
                            break;
                        case 'parent':
                            $query2->where('role_name', $roleName)
                                ->where('is_verifiedsecond_client', 'Y');
                            break;
                        case 'teacher/counselor':
                            $query2->where('role_name', $roleName);
                            break;
                    }
                })->
                when(Session::get('user_role') == 'Employee', function ($subQuery) {
                    $subQuery->where('pic', auth()->user()->id);
                })->
                when(!empty($advanced_filter['school_name']), function ($querySearch) use ($advanced_filter) {
                    $querySearch->whereIn('school_name', $advanced_filter['school_name']);
                })->
                when(!empty($advanced_filter['grade']), function ($querySearch) use ($advanced_filter) {
                    if(in_array('not_high_school', $advanced_filter['grade'])){
                        $key = array_search('not_high_school', $advanced_filter['grade']);
                        unset($advanced_filter["grade"][$key]);
                        count($advanced_filter['grade']) > 0
                            ?
                                $querySearch->where('grade_now', '>', 12)->orWhereIn('grade_now', $advanced_filter['grade'])
                                    :
                                        $querySearch->where('grade_now', '>', 12);
                    }else{
                        $querySearch->whereIn('grade_now', $advanced_filter['grade']);
                    }
                })->
                when(!empty($advanced_filter['graduation_year']), function ($querySearch) use ($advanced_filter) {
                    $querySearch->whereIn('graduation_year', $advanced_filter['graduation_year']);
                })->
                when(!empty($advanced_filter['leads']), function ($querySearch) use ($advanced_filter) {
                    $querySearch->whereIn('lead_source', $advanced_filter['leads']);
                })->
                when(!empty($advanced_filter['roles']), function ($querySearch) use ($advanced_filter) {
                    $querySearch->whereIn('roles', $advanced_filter['roles']);
                })->
                when(!empty($advanced_filter['start_joined_date']) && empty($advanced_filter['end_joined_date']), function ($querySearch) use ($advanced_filter) {
                    $querySearch->whereDate('raw_client.created_at', '>=', $advanced_filter['start_joined_date']);
                })->
                when(!empty($advanced_filter['end_joined_date']) && empty($advanced_filter['start_joined_date']), function ($querySearch) use ($advanced_filter) {
                    $querySearch->whereDate('raw_client.created_at', '<=', $advanced_filter['end_joined_date']);
                })->
                when(!empty($advanced_filter['start_joined_date']) && !empty($advanced_filter['end_joined_date']), function ($querySearch) use ($advanced_filter) {
                    $querySearch->whereBetween('raw_client.created_at', [$advanced_filter['start_joined_date'], $advanced_filter['end_joined_date']]);
                });

        // return Datatables::eloquent($model)->make(true);
        
        return $asDatatables === false ? $query->get() : $query;
    }

    public function getViewRawClientById($rawClientId)
    {
        return ViewRawClient::where('id', $rawClientId)->first();
    }

    public function getRawClientById($rawClientId)
    {
        return RawClient::where('id', $rawClientId)->first();
    }

    public function deleteRawClient($rawClientId)
    {
        return RawClient::destroy($rawClientId);
    }

    public function deleteRawClientByUUID($rawClientUUID)
    {
        return RawClient::where('uuid', $rawClientUUID)->delete();
    }

    public function moveBulkToTrash($clientIds)
    {
        return UserClient::whereIn('id', $clientIds)->delete();
    }

    public function getClientSuggestion($clientIds, $roleName)
    {

        $relation = [];
        switch ($roleName) {
            case 'student':
                $relation = ['school', 'clientProgram.program', 'parents'];
                break;

            case 'parent':
                $relation = ['school', 'childrens', 'clientProgram.program', 'parents'];

                break;

            case 'teacher/counselor':
                $relation = ['school'];
                break;
        }

        return UserClient::with($relation)->whereIn('id', $clientIds)->get();
    }

    public function insertPicClient($picDetails)
    {
        return PicClient::insert($picDetails);
    }

    public function updatePicClient($picClientId, array $picDetails)
    {

        $picDetails['status'] = 0;

        PicClient::where('id', $picClientId)->update(['status' => 0]);
        unset($picDetails['status']);

        return $this->insertPicClient($picDetails);
    }

    public function checkActivePICByClient($clientId)
    {
        return UserClient::where('id', $clientId)->withAndWhereHas('handledBy', function ($query) {
            $query->where('tbl_pic_client.status', 1);
        })->first();
    }

    public function inactivePreviousPIC(UserClient $client)
    {
                
        foreach ($client->handledBy as $pic) {
            
            $picId = $pic->id;
            $client->handledBy()->updateExistingPivot($picId, ['status' => 0]);
        }

        return $client;
        
    }

    public function getListReferral($selectColumns = [], $filter = [])
    {
        $query = UserClient::query();
        if ($selectColumns)
            $query->select($selectColumns);

            
        return $query->
            when(!empty($filter['full_name']), function ($querySearch) use ($filter) {
                $querySearch->whereRaw("RTRIM(CONCAT(first_name, ' ', COALESCE(last_name, ''))) like ?", "%{$filter['full_name']}%");
            })
            ->simplePaginate(10);
    }

    public function getParentMenteesAPI()
    {
        return UserClient::isParent()
            ->with('childrens')
            ->whereHas('childrens', function ($query) {
                $query->whereHas('clientProgram', function ($subQuery) {
                    $subQuery->whereHas('program.main_prog', function ($subQuery_2) {
                        $subQuery_2->where('prog_name', 'Admissions Mentoring');
                    })->where('status', 1)->where('prog_running_status', '!=', 2);
                })->
                orWhere(function ($q) {
                    $q->
                    whereHas('clientProgram', function ($subQuery) {
                        $subQuery->whereHas('program.main_prog', function ($subQuery_2) {
                            $subQuery_2->where('prog_name', 'Admissions Mentoring');
                        })->where('status', 1)->where('prog_running_status', 2);
                    })->
                    whereHas('clientProgram', function ($subQuery) {
                        $subQuery->where('status', 0);
                    });
                });
            })->get();
    }

    /** Followup */
    public function getClientWithoutScheduledFollowup($advanced_filter = [])
    {
        $query = UserClient::select([
                'tbl_client.id',
                'tbl_client.first_name',
                'tbl_client.last_name',
                'tbl_client.phone',
                'tbl_client.mail',
                'tbl_client.register_as',
                'parent.mail as parent_mail',
                'parent.phone as parent_phone',
            ])->
            selectRaw('RTRIM(CONCAT(parent.first_name, " ", COALESCE(parent.last_name, ""))) as parent_name')->
            leftJoin('tbl_client_relation as relation', 'relation.child_id', '=', 'tbl_client.id')->
            leftJoin('tbl_client as parent', 'parent.id', '=', 'relation.parent_id')->
            where(function ($q) {
                $q->
                    doesntHave('clientProgram')->
                    orWhere(function ($q_2) {
                        $q_2->
                            whereHas('clientProgram', function ($subQuery) {
                                // $subQuery->whereIn('status', [2, 3])->where('status', '!=', 0);
                                $subQuery->whereIn('status', [2, 3]);
                            })->
                            whereDoesntHave('clientProgram', function ($subQuery) {
                                $subQuery->whereIn('status', [0, 1]);
                            });
                    });
            })->
            whereDoesntHave('followupSchedule')->
            whereHas('roles', function ($subQuery) {
                $subQuery->where('role_name', 'student');
            })->
            isNotSalesAdmin()->
            isUsingAPI()->
            isActive()->
            isVerified()->
            when(!empty($advanced_filter['client_name']), function ($q) use ($advanced_filter) {
                $q->whereRaw("CONCAT(tbl_client.first_name, ' ', tbl_client.last_name) LIKE ?", ["%{$advanced_filter['client_name']}%"]);
            });
            
        return $query->get();
    }

    public function getClientWithScheduledFollowup($status)
    {
        $query = UserClient::with('followupSchedule')->select([
                'tbl_client.id',
                'tbl_client.first_name',
                'tbl_client.last_name',
                'tbl_client.phone',
                'tbl_client.mail',
                'parent.mail as parent_mail',
                'parent.phone as parent_phone',
            ])->
            selectRaw('RTRIM(CONCAT(parent.first_name, " ", COALESCE(parent.last_name, ""))) as parent_name')->
            leftJoin('tbl_client_relation as relation', 'relation.child_id', '=', 'tbl_client.id')->
            leftJoin('tbl_client as parent', 'parent.id', '=', 'relation.parent_id')->
            where(function ($q) {
                $q->
                    doesntHave('clientProgram')->
                    orWhere(function ($q_2) {
                        $q_2->
                            whereHas('clientProgram', function ($subQuery) {
                                // $subQuery->whereIn('status', [2, 3])->where('status', '!=', 0);
                                $subQuery->whereIn('status', [2, 3]);
                            })->
                            whereDoesntHave('clientProgram', function ($subQuery) {
                                $subQuery->whereIn('status', [0, 1]);
                            });
                    });
            })->
            whereHas('followupSchedule', function ($q) use ($status) {
                $q->where('status', $status);
            })->
            whereHas('roles', function ($subQuery) {
                $subQuery->where('role_name', 'student');
            })->
            isNotSalesAdmin()->
            isUsingAPI()->
            isActive()->
            isVerified();
            
        return $query->get();
    }

    # API
    public function getClientByTicket($ticket_no)
    {
        # get clientevent info
        $clientevent = ClientEvent::with([
                    'client', 'client.school', 'client.destinationCountries', 'client.roles', 'children', 'children.school', 'children.destinationCountries'
                ])->where('ticket_id', $ticket_no)->first();
        
        # when client that registered is actually a parent
        # then return false. why?
        # because this function is called from initial assessment app which should be student can have access to it
        if ($clientevent->child_id === NULL && !$clientevent->client->roles()->whereIn('role_name', ['student'])->exists())
            return false;
        

        # when the client that joined clientevent, registering a children as well
        # then get the children info
        if ($clientevent->child_id !== NULL)
            $child = $clientevent->children;


        # when the client that joined clientevent, is already a student
        if ($clientevent->client->roles()->whereIn('role_name', ['student'])->exists())
            $child = $clientevent->client;


        return [
            'client' => [
                'id' => $child->id,
                'is_vip' => $clientevent->notes == null ? false : true,
                'took_initial_assessment' => 0,
                'full_name' => $child->full_name,
                'email' => $child->mail,
                'phone' => $child->phone,
                'address' => [
                    'state' => $child->state,
                    'city' => $child->city,
                    'address' => $child->address
                ],
                'education' => [
                    'school' => $child->school->sch_name,
                    'grade' => $child->gradeNow,
                ],
                'country' => $child->destinationCountries->pluck('name')->toArray()
            ],
            'clientevent' => [
                'id' => $clientevent->clientevent_id,
                'ticket_id' => $clientevent->ticket_id,
            ]
        ];

    }

    public function getClientByUUIDforAssessment($uuid)
    {
        $child = UserClient::where('uuid', $uuid)->first();

        return [
            'client' => [
                'id' => $child->id,
                'uuid_crm' => $child->uuid,
                'is_vip' => false,
                'took_initial_assessment' => 0,
                'full_name' => $child->full_name,
                'email' => $child->mail,
                'phone' => $child->phone,
                'address' => [
                    'state' => $child->state,
                    'city' => $child->city,
                    'address' => $child->address
                ],
                'education' => [
                    'school' => isset($child->school) ? $child->school->sch_name : null,
                    'grade' => $child->gradeNow,
                ],
                'country' => $child->destinationCountries->pluck('name')->toArray()
            ],
            'clientevent' => [
                'id' => null,
                'ticket_id' => null,
            ]
        ];

    }

    # use for modal reminder invoice bundle
    public function getDataParentsByChildId($childId)
    {
        $child = UserClient::find($childId);
        return $child->parents()->get();
    }

    public function getClientsByCategory($category)
    {
        return UserClient::where('category', $category)->get();
    }

    public function updateClientByUUID($uuid, array $newDetails)
    {
        return tap(UserClient::where('uuid', $uuid)->first())->update($newDetails);
    }

    public function countClientByCategory($category, $month = null)
    {
        $client = DB::table('tbl_client')
            ->select(DB::raw('count(*) as client_count'))
            ->where('category', $category)
            ->when($month, function ($subQuery) use ($month) {
                $subQuery->whereMonth('created_at', date('m', strtotime($month)))->whereYear('created_at', date('Y', strtotime($month)));
            })
            ->first();

        return $client->client_count;
    }

    public function countClientByRole($role, $month = null, $isRaw = false)
    {
        $client = DB::table('tbl_client')
            ->select(DB::raw('count(*) as client_count'))
            ->join('tbl_client_roles', function ($q) {
                $q->on('tbl_client_roles.client_id', '=', 'tbl_client.id');
            })
            ->join('tbl_roles', function ($q) use($role) {
                $q->on('tbl_roles.id', '=', 'tbl_client_roles.role_id');
            })->where('tbl_roles.role_name', '=', $role)->
            when($month, function ($subQuery) use ($month) {
                $subQuery->whereMonth('tbl_client.created_at', date('m', strtotime($month)))->whereYear('tbl_client.created_at', date('Y', strtotime($month)));
            })->
            when(!$isRaw, function ($subQuery) {
                $subQuery->where('tbl_client.is_verified', 'Y');
            })->
            when($isRaw, function ($subQuery) {
                $subQuery->where('tbl_client.is_verified', 'N');
            })->
            where('st_statusact', 1)->
            first();

        return $client->client_count;
    }
}
