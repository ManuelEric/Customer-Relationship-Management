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
    public function updateStatusUser(User $user, array $new_status_details);
    public function deleteUserType($user_type_id);
    public function getUserRoles($userId, $roleName);
    public function cleaningUser();
    public function createUserEducation(User $user, array $user_education_details);
    public function updateUserEducation(User $user, array $new_user_education_details);
    public function createOrUpdateUserSubject(User $user, Request $request);
    public function createUserRole(User $user, array $user_role_details);
    public function updateUserRole(User $user, array $new_user_role_details);
    public function createUserType(User $user, array $user_type_details);
    public function updateUserType(User $user, array $new_user_type_details);
    public function getUserSubjectById($user_subject_id);

    //! new methods
    public function rnFindExpiringContracts(ContractUserType $type);
}
