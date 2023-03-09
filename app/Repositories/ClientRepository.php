<?php

namespace App\Repositories;

use App\Http\Traits\FindDestinationCountryScore;
use App\Http\Traits\FindSchoolYearLeftScoreTrait;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\RoleRepositoryInterface;
use App\Models\Client;
use App\Models\Tag;
use App\Models\UserClient;
use App\Models\v1\Student as CRMStudent;
use App\Models\v1\StudentParent as CRMParent;
use DataTables;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ClientRepository implements ClientRepositoryInterface 
{
    use FindSchoolYearLeftScoreTrait;
    use FindDestinationCountryScore;
    private RoleRepositoryInterface $roleRepository;

    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function getAllClients()
    {
        return UserClient::all();
    }

    public function getAllClientDataTables()
    {
        return Datatables::eloquent(UserClient::query())->make(true);
    }

    # unused
    public function getAllClientByRoleAndStatusDataTablesOld($roleName, $statusClient)
    {
        return Datatables::eloquent(UserClient::whereHas('roles', function ($query) use ($roleName) {
            $query->where('role_name', $roleName);
        })->where('st_statuscli', $statusClient))
            ->addColumn('full_name', function ($data) { return $data->fullname; })
            ->addColumn('parent_name', function ($data) { return $data->parents()->count() > 0 ? $data->parents()->first()->first_name.' '.$data->parents()->first()->last_name : null; })
            ->addColumn('parent_phone', function ($data) { return $data->parents()->count() > 0 ? $data->parents()->first()->phone : null; })
            ->addColumn('school_name', function ($data) { return $data->school->sch_name; })
            ->addColumn('lead_source', function ($data) { 
                switch ($lead_source = $data->lead->main_lead) {
                    case "KOL":
                        return "KOL - ".$data->lead->sub_lead;
                        break;
                    
                    case str_contains($lead_source, 'External Edufair'):
                        return $lead_source.' - '.$data->external_edufair->title;
                        break;
                    
                    case str_contains($lead_source, 'All-In Event'):
                        return $lead_source.' - '.$data->event->event_title;
                        break;

                    default:
                        return $lead_source;
                    

                }
                // return $data->lead->main_lead == "KOL" ? "KOL - ".$data->lead->sub_lead : $data->lead->main_lead.' - '.$data->event->event_title; 
            })
            ->addColumn('interest_programs', function ($data) { 
                $no = 1;
                $render = '';
                foreach ($data->interestPrograms as $program) {
                    $render .= $no == 1 ? $program->prog_program : ", ".$program->prog_program;
                    $no++;
                }
                return $render; 
            })
            ->addColumn('st_abrcountry', function ($data) {
                $st_abrcountry = json_decode($data->st_abrcountry);
                $no = 1;
                $render = '';
                foreach ($st_abrcountry as $key => $val) {
                    $render .= $no == 1 ? $val : ', '.$val;
                    $no++;
                }  
                return $render;
            })
            ->addColumn('dream_universities', function ($data) {
                $no = 1;
                $render = '';
                foreach ($data->interestUniversities as $university) {
                    $render .= $no == 1 ? $university->univ_name : ", ".$university->univ_name;
                    $no++;
                }
                return $render; 
            })
            ->addColumn('interest_majors', function ($data) {
                $no = 1;
                $render = '';
                foreach ($data->interestMajor as $major) {
                    $render .= $no == 1 ? $major->name : ", ".$major->name;
                    $no++;
                }
                return $render; 
            })
            ->addColumn('updated_at', function ($data) { return date('d M Y H:i:s', strtotime($data->updated_at)); })
            ->orderColumn('full_name', function ($query, $order) {
                $query->orderBy(DB::raw('CONCAT(first_name, " ", COALESCE(last_name, ""))'), $order);
            })
            ->rawColumns(['address'])
            ->make(true);
    }

    public function getAllClientByRoleAndStatusDataTables($roleName, $statusClient = NULL)
    {
        # if role name is student
        # then retrieve all student without mentee
        # so first select all mentee
        # then use not in all mentee
        $client = [];
        if ($roleName == "Student" && $statusClient == 0) {
            $client = UserClient::whereHas('roles', function($query) {
                $query->where('role_name', 'mentee');
            })->pluck('id')->toArray();
        }

        return Datatables::eloquent(
                Client::
                whereHas('roles', function ($query) use ($roleName) {
                    $query->where('role_name', $roleName);
                })->when($roleName == "Student", function ($q) use ($client) {
                    $q->whereNotIn('client.id', $client);
                })
                
                ->when(is_int($statusClient) && $statusClient !== NULL, function($query) use ($statusClient) {
                    $query->where('st_statuscli', $statusClient);
                })
                ->when(is_string($statusClient) && $statusClient !== NULL, function($query) use ($statusClient) {

                    $query->when($statusClient == "active", function ($query1) {
    
                        $query1->whereHas('clientProgram', function ($q2) {
                            $q2->whereIn('prog_running_status', [0,1]);
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
            )
            ->addColumn('parent_name', function ($data) { return $data->parents()->count() > 0 ? $data->parents()->first()->first_name.' '.$data->parents()->first()->last_name : null; })
            ->addColumn('parent_phone', function ($data) { return $data->parents()->count() > 0 ? $data->parents()->first()->phone : null; })
            ->addColumn('children_name', function ($data) { return $data->childrens()->count() > 0 ? $data->childrens()->first()->first_name.' '.$data->childrens()->first()->last_name : null; })
            ->rawColumns(['address'])
            ->make(true);
    }

    public function getAllClientByRole($roleName, $month = null) # mentee, parent, teacher
    {
        return UserClient::when($roleName == "alumni", function($query) {
            $query->whereHas('clientProgram', function ($q2) {
                $q2->whereIn('prog_running_status', [2]);
            })->withCount([
                'clientProgram as client_program_count' => function ($query) {
                    $query->whereIn('prog_running_status', [0, 1, 2]);   
                },
                'clientProgram as client_program_finish_count' => function ($query) {
                    $query->where('prog_running_status', 2);
                }
            ])->havingRaw('client_program_count = client_program_finish_count');
        }, function ($query) use ($roleName) {
            $query->whereHas('roles', function ($query2) use ($roleName) {
                $query2->where('role_name', $roleName);
            });
        })->when($month, function ($query) use ($month) {
            $query->whereMonth('tbl_client.created_at', date('m', strtotime($month)))->whereYear('tbl_client.created_at', date('Y', strtotime($month)));
        })->get();
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
        })->doesntHave('parents')->when($parentChilds, function($query) use ($parentChilds) {
            $query->orWhereIn('id', $parentChilds);
        })->get();
    }

    public function getClientById($clientId)
    {
        return UserClient::find($clientId);
    }

    public function deleteClient($clientId)
    {

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
        return UserClient::where(DB::raw('CONCAT(first_name, " ", COALESCE(last_name, ""))'), 'like', '%'.$parentName.'%')->whereHas('roles', function ($query) {
            $query->where('role_name', 'Parent');
        })->first();
    }

    # connecting student with parents
    public function createClientRelation($parentId, $studentId)
    {
        $student = UserClient::where('id',$studentId)->first();

        # why sync?
        # to create and update all at once
        $student->parents()->sync($parentId);
        return $student;
    }

    public function createManyClientRelation($parentId, $studentId)
    {
        $parent = UserClient::find($parentId);

        # why sync?
        # to create and update all at once
        $parent->childrens()->sync($studentId);  
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
        return Client::where('st_statuscli', $status)->when($month, function($query) use ($month) {
            $query->whereMonth('created_at', date('m', strtotime($month)))->whereYear('created_at', date('Y', strtotime($month)));
        })->whereHas('roles', function($query) {
            $query->where('role_name', 'Student');
        })->count();
    }

    public function getMenteesBirthdayMonthly($month)
    {
        return Client::whereMonth('dob', date('m', strtotime($month)))->whereHas('roles', function($query) {
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
        return UserClient::where(DB::raw('CONCAT(first_name, " ", last_name)'), $studentName)->first();
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
}