<?php

namespace App\Repositories;

use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\RoleRepositoryInterface;
use App\Models\UserClient;
use DataTables;
use Illuminate\Support\Carbon;

class ClientRepository implements ClientRepositoryInterface 
{
    private RoleRepositoryInterface $roleRepository;

    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function getAllClientDataTables()
    {
        return Datatables::eloquent(UserClient::query())->make(true);
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