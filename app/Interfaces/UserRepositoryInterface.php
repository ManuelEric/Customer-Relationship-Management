<?php

namespace App\Interfaces;

use App\Enum\ContractUserType;
use App\Models\User;
use Illuminate\Http\Request;

interface UserRepositoryInterface
{
    public function getAllUsersByRoleDataTables($role);
    public function getAllUsers();
    public function getAllUsersWithoutUUID();
    public function getAllUsersByRole($role);
    public function getAllUsersByDepartmentAndRole($role, $department);
    public function getAllUsersProbationContracts();
    public function getAllUsersTutorContracts();
    public function getAllUsersEditorContracts();
    public function getAllUsersExternalMentorContracts();
    public function getAllUsersInternshipContracts();
    public function getPICs();
    public function getUserById($userId);
    public function getUserByUUID($userUUID);
    public function getUserByfirstName($first_name);
    public function getUserByExtendedId($extendedId);
    public function getUserByFullNameOrEmail($userName, $userEmail);
    public function createUsers(array $userDetails);
    public function createUser(array $userDetails);
    public function updateUser($userId, array $newDetails);
    public function updateStatusUser($userId, array $detail);
    public function deleteUserType($userTypeId);
    public function getUserRoles($userId, $roleName);
    public function cleaningUser();
    public function createUserEducation(User $user, array $userEducationDetails);
    public function createOrUpdateUserSubject(User $user, Request $request);
    public function createUserRole(User $user, array $userRoleDetails);
    public function createUserType(User $user, array $userTypeDetails);
    public function getUserSubjectById($user_subject_id);

    //! new methods
    public function rnFindExpiringContracts(ContractUserType $type);
}
