<?php

namespace App\Repositories;

use App\Http\Traits\FindDestinationCountryScore;
use App\Http\Traits\FindSchoolYearLeftScoreTrait;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\RoleRepositoryInterface;
use App\Models\Client;
use App\Models\Tag;
use App\Models\UserClient;
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

    public function getAllClientDataTables()
    {
        return Datatables::eloquent(UserClient::query())->make(true);
    }

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
                $query->orderBy(DB::raw('CONCAT(first_name, " ", last_name)'), $order);
            })
            ->rawColumns(['address'])
            ->make(true);
    }

    public function getAllClientByRoleAndStatusDataTables($roleName, $statusClient)
    {
        return Datatables::eloquent(Client::whereHas('roles', function ($query) use ($roleName) {
            $query->where('role_name', $roleName);
        })->where('st_statuscli', $statusClient))
            ->addColumn('parent_name', function ($data) { return $data->parents()->count() > 0 ? $data->parents()->first()->first_name.' '.$data->parents()->first()->last_name : null; })
            ->addColumn('parent_phone', function ($data) { return $data->parents()->count() > 0 ? $data->parents()->first()->phone : null; })
            ->rawColumns(['address'])
            ->make(true);
    }

    public function getAllClientByRole($roleName) # mentee, parent, teacher
    {
        return UserClient::whereHas('roles', function ($query) use ($roleName) {
            $query->where('role_name', $roleName);
        })->get();
    }

    public function getAllClientByRoleAndStatus($roleName, $statusClient)
    {
        return UserClient::whereHas('roles', function ($query) use ($roleName) {
            $query->where('role_name', $roleName);
        })->where('st_statuscli', $statusClient)->get();
    }

    public function getClientById($clientId)
    {

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

    public function createClientRelation($parentId, $studentId)
    {
        // return "ini parent id : ".$parentId.' dan ini student id : '.$studentId;
        $student = UserClient::find($studentId);
        $student->parents()->attach($parentId, [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        return $student;
    }

    public function createDestinationCountry($studentId, $destinationCountryDetails)
    {
        $student = UserClient::find($studentId);
        $student->destinationCountries()->attach($destinationCountryDetails);
        return $student;
    }

    public function createInterestProgram($studentId, $interestProgramDetails)
    {
        $student = UserClient::find($studentId);
        $student->interestPrograms()->attach($interestProgramDetails);
        return $student;
    }

    public function createInterestUniversities($studentId, $interestUnivDetails)
    {
        $student = UserClient::find($studentId);
        $student->interestUniversities()->attach($interestUnivDetails);
        return $student;
    }

    public function createInterestMajor($studentId, $interestMajorDetails)
    {
        $student = UserClient::find($studentId);
        $student->interestMajor()->attach($interestMajorDetails);
        return $student;
    }

    public function updateClient($clientId, array $newDetails)
    {
        
    }
}