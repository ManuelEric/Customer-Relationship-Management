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
use App\Models\User;
use Illuminate\Support\Str; 

class ClientRepository implements ClientRepositoryInterface
{
    use FindSchoolYearLeftScoreTrait;
    use FindDestinationCountryScore;
    use StandardizePhoneNumberTrait;
    private RoleRepositoryInterface $roleRepository;


    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
        $this->ALUMNI_IDS = $this->getAlumnis();
    }

    public function getAllClients()
    {
        return UserClient::all();
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
                })
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
        })->get();
    }

    /* NEW */
    public function getDataTables($model)
    {
        return DataTables::eloquent($model)->
            addColumn('parent_name', function ($data) {
                return $data->parents()->count() > 0 ? $data->parents()->first()->first_name . ' ' . $data->parents()->first()->last_name : null;
            })->
            addColumn('parent_mail', function ($data) {
                return $data->parents()->count() > 0 ? $data->parents()->first()->mail : null;
            })->
            addColumn('parent_phone', function ($data) {
                return $data->parents()->count() > 0 ? $data->parents()->first()->phone : null;
            })->
            addColumn('children_name', function ($data) {
                return $data->childrens()->count() > 0 ? $data->childrens()->first()->first_name . ' ' . $data->childrens()->first()->last_name : null;
            })->
            addColumn('parent_name', function ($data) {
                return $data->parents()->count() > 0 ? $data->parents()->first()->first_name . ' ' . $data->parents()->first()->last_name : null;
            })->
            addColumn('parent_phone', function ($data) {
                return $data->parents()->count() > 0 ? $data->parents()->first()->phone : null;
            })->
            addColumn('children_name', function ($data) {
                return $data->childrens()->count() > 0 ? $data->childrens()->first()->first_name . ' ' . $data->childrens()->first()->last_name : null;
            })->
            rawColumns(['address'])->
            make(true);
    }

    public function getNewLeads($asDatatables = false, $month = null, $advanced_filter = [])
    {
        # new client that havent offering our program
        $query = Client::doesntHave('clientProgram')->when($month, function ($subQuery) use ($month) {
                $subQuery->whereMonth('created_at', date('m', strtotime($month)))->whereYear('created_at', date('Y', strtotime($month)));
            })->
            whereHas('roles', function ($subQuery) {
                $subQuery->where('role_name', 'student');
            })->
            when(!empty($advanced_filter['school_name']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereIn('school_name', $advanced_filter['school_name']);
            })->
            when(!empty($advanced_filter['graduation_year']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereIn('graduation_year', $advanced_filter['graduation_year']);
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
            when(!empty($advanced_filter['active_status']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereIn('st_statusact', $advanced_filter['active_status']);
            });

        return $asDatatables === false ? $query->orderBy('created_at', 'desc')->get() : $query;
    }

    public function getPotentialClients($asDatatables = false, $month = null, $advanced_filter = [])
    {
        # new client that have been offered our program but hasnt deal yet
        $query = Client::whereHas('clientProgram', function ($subQuery) {
                $subQuery->whereIn('status', [0, 2, 3]); # because refund and cancel still marked as potential client
            })->whereDoesntHave('clientProgram', function ($subQuery) {
                $subQuery->where('status', 1);
            })-> # tidak punya client program dengan status 1 : success
            when($month, function ($subQuery) use ($month) {
                $subQuery->whereMonth('created_at', date('m', strtotime($month)))->whereYear('created_at', date('Y', strtotime($month)));
            })->whereHas('roles', function ($subQuery) {
                $subQuery->where('role_name', 'student');
            })->
            when(!empty($advanced_filter['school_name']), function ($subQuery) use ($advanced_filter) {
                $subQuery->whereIn('school_name', $advanced_filter['school_name']);
            })->
            when(!empty($advanced_filter['graduation_year']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereIn('graduation_year', $advanced_filter['graduation_year']);
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
            when(!empty($advanced_filter['active_status']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereIn('st_statusact', $advanced_filter['active_status']);
            });

        return $asDatatables === false ? $query->orderBy('created_at', 'desc')->get() : $query->orderBy('first_name', 'asc');
    }

    public function getExistingMentees($asDatatables = false, $month = null, $advanced_filter = [])
    {
        # join program admission mentoring & prog running status hasnt done
        $query = Client::whereHas('clientProgram', function ($subQuery) {
                $subQuery->whereHas('program', function ($subQuery_2) {
                    $subQuery_2->whereHas('main_prog', function ($subQuery_3) {
                        $subQuery_3->where('prog_name', 'Admissions Mentoring');
                    });
                })->where('status', 1)->where('prog_running_status', '!=', 2); # 1 success, 2 done
            })->
            when($month, function ($subQuery) use ($month) {
                $subQuery->whereMonth('created_at', date('m', strtotime($month)))->whereYear('created_at', date('Y', strtotime($month)));
            })->
            whereHas('roles', function ($subQuery) {
                $subQuery->where('role_name', 'student');
            })->
            when(!empty($advanced_filter['school_name']), function ($subQuery) use ($advanced_filter) {
                $subQuery->whereIn('school_name', $advanced_filter['school_name']);
            })->
            when(!empty($advanced_filter['graduation_year']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereIn('graduation_year', $advanced_filter['graduation_year']);
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
            when(!empty($advanced_filter['active_status']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereIn('st_statusact', $advanced_filter['active_status']);
            });

        return $asDatatables === false ? $query->orderBy('created_at', 'desc')->get() : $query->orderBy('first_name', 'asc');
    }

    public function getExistingNonMentees($asDatatables = false, $month = null, $advanced_filter = [])
    {
        # has join our program but its not admissions mentoring
        $query = Client::whereDoesntHave('clientProgram', function ($subQuery) {
                $subQuery->whereHas('program', function ($subQuery_2) {
                    $subQuery_2->whereHas('main_prog', function ($subQuery_3) {
                        $subQuery_3->where('prog_name', 'Admissions Mentoring');
                    });
                })->where('status', 1); # meaning 1 is he/she has been offered admissions mentoring before 
            })->
            whereHas('clientProgram', function ($subQuery) {
                $subQuery->whereHas('program.main_prog', function ($subQuery_2) {
                    $subQuery_2->where('prog_name', '!=', 'Admissions Mentoring');
                })->where(function ($subQuery_2) {
                    $subQuery_2->where('status', 1)->where('prog_running_status', '!=', 2);
                });
            })->
            when($month, function ($subQuery) use ($month) {
                $subQuery->whereMonth('created_at', date('m', strtotime($month)))->whereYear('created_at', date('Y', strtotime($month)));
            })->
            whereHas('roles', function ($subQuery) {
                $subQuery->where('role_name', 'student');
            })->
            when(!empty($advanced_filter['school_name']), function ($subQuery) use ($advanced_filter) {
                $subQuery->whereIn('school_name', $advanced_filter['school_name']);
            })->
            when(!empty($advanced_filter['graduation_year']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereIn('graduation_year', $advanced_filter['graduation_year']);
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
            when(!empty($advanced_filter['active_status']), function ($querySearch) use ($advanced_filter) {
                $querySearch->whereIn('st_statusact', $advanced_filter['active_status']);
            });

        return $asDatatables === false ? $query->orderBy('created_at', 'desc')->get() : $query->orderBy('first_name', 'asc');
    }

    public function getAllClientStudent($advanced_filter = [])
    {
        $new_leads = $this->getNewLeads(false, null, $advanced_filter)->pluck('id')->toArray();
        $potential = $this->getPotentialClients(false, null, $advanced_filter)->pluck('id')->toArray();
        $existing = $this->getExistingMentees(false, null, $advanced_filter)->pluck('id')->toArray();
        $existingNon = $this->getExistingNonMentees(false, null, $advanced_filter)->pluck('id')->toArray();
        
        $clientStudent = $new_leads;
        $clientStudent = array_merge($clientStudent, $potential);
        $clientStudent = array_merge($clientStudent, $existing);
        $clientStudent = array_merge($clientStudent, $existingNon);

        $query = Client::whereIn('client.id', $clientStudent);

        return $query->orderBy('first_name', 'asc');
    }

    public function getAlumniMentees($groupBy = false, $asDatatables = false, $month = null)
    {
        # has finish our admission program
        $query = Client::whereHas('clientProgram.program.main_prog', function ($subQuery) {
            $subQuery->where('prog_name', 'Admissions Mentoring')->where('status', 1)->where('prog_running_status', 2);
        })->whereDoesntHave('clientProgram', function ($subQuery) {
            $subQuery->whereHas('program.main_prog', function ($subQuery_2) {
                $subQuery_2->where('prog_name', 'Admissions Mentoring');
            })->where('status', 1)->where('prog_running_status', '!=', 2);
        })->when($month, function ($subQuery) use ($month) {
            $subQuery->whereMonth('created_at', date('m', strtotime($month)))->whereYear('created_at', date('Y', strtotime($month)));
        })->whereHas('roles', function ($subQuery) {
            $subQuery->where('role_name', 'student');
        });

        return $asDatatables === false ?
            ($groupBy === true ? $query->select('*')->addSelect(DB::raw('YEAR(created_at) AS year'))->orderBy('created_at', 'desc')->get()->groupBy('year') : $query->get())
            : $query->orderBy('first_name', 'asc');
    }

    public function getAlumniMenteesSiblings()
    {
        $query = Client::
            with(['parents', 'parents.childrens'])->
            whereHas('clientProgram.program.main_prog', function ($subQuery) {
                $subQuery->where('prog_name', 'Admissions Mentoring')->where('status', 1)->where('prog_running_status', 2);
            })->
            whereDoesntHave('clientProgram', function ($subQuery) {
                $subQuery->whereHas('program.main_prog', function ($subQuery_2) {
                    $subQuery_2->where('prog_name', 'Admissions Mentoring');
                })->where('status', 1)->where('prog_running_status', '!=', 2);
            })->
            whereHas('roles', function ($subQuery) {
                $subQuery->where('role_name', 'student');
            })->
            whereHas('parents', function ($subQuery) {
                $subQuery->has('childrens', '>', 1);
            });

        return $query->get();
    }

    public function getAlumniNonMentees($groupBy = false, $asDatatables = false, $month = null)
    {
        # has finish our program and hasnt joined admission program
        $query = Client::whereDoesntHave('clientProgram', function ($subQuery) {
            $subQuery->whereHas('program.main_prog', function ($subQuery_2) {
                $subQuery_2->where('prog_name', 'Admissions Mentoring');
            })->where('status', 1);
        })->whereHas('clientProgram', function ($subQuery) {
            $subQuery->where('status', 1)->where('prog_running_status', 2);
        })->whereDoesntHave('clientProgram', function ($subQuery) {
            $subQuery->where('status', 1)->whereIn('prog_running_status', [0, 1]);
        })->when($month, function ($subQuery) use ($month) {
            $subQuery->whereMonth('created_at', date('m', strtotime($month)))->whereYear('created_at', date('Y', strtotime($month)));
        })->whereHas('roles', function ($subQuery) {
            $subQuery->where('role_name', 'student');
        });

        return $asDatatables === false ?
            ($groupBy === true ? $query->select('*')->addSelect(DB::raw('YEAR(created_at) AS year'))->orderBy('created_at', 'desc')->get()->groupBy('year') : $query->get())
            : $query->orderBy('first_name', 'asc');
    }

    public function getParents($asDatatables = false, $month = null)
    {
        $query = Client::whereHas('roles', function ($subQuery) {
            $subQuery->where('role_name', 'Parent');
        })->when($month, function ($subQuery) use ($month) {
            $subQuery->whereMonth('created_at', date('m', strtotime($month)))->whereYear('created_at', date('Y', strtotime($month)));
        });

        return $asDatatables === false ? $query->get() : $query->orderBy('first_name', 'asc');
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

    public function getAlumnis()
    {
        return UserClient::whereHas('clientProgram', function ($q2) {
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
    }

    public function getMenteesDataTables()
    {
        $roleName = "mentee";

        $query = Client::whereNotIn('id', $this->ALUMNI_IDS)->whereHas('roles', function ($query2) use ($roleName) {
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
        return UserClient::find($clientId);
    }

    public function getClientByPhoneNumber($phoneNumber)
    {
        if (substr($phoneNumber, 0, 1) == "+")
            $phoneNumber = substr($phoneNumber, 4);
        
        return UserClient::whereRaw('SUBSTR(phone, 4) LIKE ?', ['%'.$phoneNumber.'%'])->first();
    }

    public function getViewClientById($clientId)
    {
        return Client::find($clientId);
    }

    public function checkIfClientIsMentee($clientId)
    {
        return UserClient::whereHas('roles', function ($query) {
            $query->where('role_name', 'mentee');
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
        return UserClient::whereId($clientId)->update($newDetails);
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
        $student = UserClient::find($studentId);
        $student->destinationCountries()->sync($destinationCountryDetails);
        return $student;
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
        })->where('st_statusact', 1)->get();
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
}
