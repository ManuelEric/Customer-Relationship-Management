<?php

namespace App\Interfaces;

use App\Enum\ContractUserType;
use App\Models\User;
use Illuminate\Http\Request;

interface UserRepositoryInterface
{
    public function rnGetAllUsersByRoleDataTables($role);
    public function rnGetAllUsers();
    public function rnGetAllUsersByRole($role);
    public function rnGetAllUsersByDepartmentAndRole($role, $department);
    public function rnGetUserById($userId);
    public function rnCreateUser(array $userDetails);
    public function rnUpdateUser($userId, array $newDetails);
    public function rnUpdateStatusUser(User $user, array $new_status_details);
    public function rnDeleteUserType($user_type_id);
    public function rnCreateUserEducation(User $user, array $user_education_details);
    public function rnUpdateUserEducation(User $user, array $new_user_education_details);
    public function rnCreateOrUpdateUserSubject(User $user, Request $request);
    public function rnCreateUserRole(User $user, array $user_role_details);
    public function rnUpdateUserRole(User $user, array $new_user_role_details);
    public function rnCreateUserType(User $user, array $user_type_details);
    public function rnUpdateUserType(User $user, array $new_user_type_details);
    public function rnGetUserSubjectById($user_subject_id);

    //! new methods
    public function rnFindExpiringContracts(ContractUserType $type);
    public function rnDeleteUserAgreement($user_subject_id);
}
