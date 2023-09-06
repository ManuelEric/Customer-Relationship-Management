<?php

namespace App\Repositories;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\UserRepositoryInterface;
use App\Models\pivot\UserRole;
use App\Models\pivot\UserTypeDetail;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use DataTables;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserRepository implements UserRepositoryInterface
{
    use CreateCustomPrimaryKeyTrait;

    public function getAllUsersByRoleDataTables($role)
    {
        return DataTables::eloquent(
            User::leftJoin('tbl_position', 'tbl_position.id', '=', 'users.position_id')->
            whereHas('roles', function ($query) use ($role) {
                $query->where('role_name', 'like', '%'.$role);
            })
                ->select([
                    'users.id as id',
                    'extended_id',
                    'first_name',
                    'last_name',
                    DB::raw('CONCAT(first_name, " ", COALESCE(last_name, "")) as full_name'),
                    'email',
                    'phone',
                    'tbl_position.position_name',
                    DB::raw('(SELECT GROUP_CONCAT(tbl_user_educations.graduation_date SEPARATOR ", ") FROM tbl_user_educations
                WHERE user_id = users.id GROUP BY tbl_user_educations.user_id ORDER BY tbl_user_educations.degree ASC) as graduation_date_group'),
                    DB::raw('(SELECT GROUP_CONCAT(tbl_major.name SEPARATOR ", ") FROM tbl_user_educations
                LEFT JOIN tbl_major ON tbl_major.id = tbl_user_educations.major_id
                WHERE user_id = users.id GROUP BY tbl_user_educations.user_id ORDER BY tbl_user_educations.degree ASC) as major_group'),
                    'datebirth',
                    'nik',
                    'npwp',
                    'bankacc',
                    'emergency_contact',
                    'address',
                    'active',

                ])
                ->orderBy('extended_id', 'asc')
        )
            ->filterColumn('full_name', function ($query, $keyword) {
                $sql = 'CONCAT(first_name, " ", COALESCE(last_name, "")) like ?';
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->filterColumn('graduation_date_group', function ($query, $keyword) {
                $sql = '(SELECT GROUP_CONCAT(tbl_user_educations.graduation_date SEPARATOR ", ") FROM tbl_user_educations
            WHERE user_id = users.id GROUP BY tbl_user_educations.user_id ORDER BY tbl_user_educations.degree ASC) like ?';
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->filterColumn('major_group', function ($query, $keyword) {
                $sql = '(SELECT GROUP_CONCAT(tbl_major.name SEPARATOR ", ") FROM tbl_user_educations
            LEFT JOIN tbl_major ON tbl_major.id = tbl_user_educations.major_id
            WHERE user_id = users.id GROUP BY tbl_user_educations.user_id ORDER BY tbl_user_educations.degree ASC) like ?';
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->rawColumns(['address'])
            ->make(true);
    }

    public function getAllUsers()
    {
        return User::all();
    }

    public function getAllUsersWithoutUUID()
    {
        return User::whereNull('uuid')->get();
    }

    public function getAllUsersByRole($role)
    {
        return User::with('department')->whereHas('roles', function ($query) use ($role) {
            $query->where('role_name', 'like', '%'.$role);
        })->where('active', 1)->orderBy('first_name', 'asc')->orderBy('last_name', 'asc')->get();
    }

    public function getAllUsersByDepartmentAndRole($role, $department)
    {
        return User::whereHas('roles', function ($query) use ($role) {
            $query->where('role_name', 'like', '%'.$role);
        })->whereHas('department', function ($query) use ($department) {
            $query->where('dept_name', 'like', '%'.$department.'%');
        })->where('active', 1)->orderBy('first_name', 'asc')->orderBy('last_name', 'asc')->get();
    }

    public function getAllUsersProbationContracts()
    {
        $today = date('Y-m-d');
        $twoWeeks = date('Y-m-d', strtotime('+2 weeks', strtotime($today)));

        return User::whereHas('user_type', function($query) use ($twoWeeks) {
                $query->
                    where('tbl_user_type_detail.status', 1)-> # dimana status contractnya active
                    where('tbl_user_type_detail.end_date', $twoWeeks)-> # dimana end date nya sudah H-2 weeks
                    where('tbl_user_type.type_name', 'Probation'); 
            })->get();
    }

    public function getAllUsersTutorContracts()
    {
        $today = date('Y-m-d');
        $twoMonths = date('Y-m-d', strtotime('+2 months', strtotime($today)));

        return User::
            whereHas('roles', function ($query) {
                $query->
                    where('role_name', 'Tutor');
            })->
            whereHas('user_type', function($query) use ($twoMonths) {
                $query->
                    where('tbl_user_type_detail.status', 1)-> # dimana status contractnya active
                    where('tbl_user_type_detail.end_date', $twoMonths)-> # dimana end date nya sudah H-2 weeks
                    where('tbl_user_type.type_name', 'Part-Time');
            })->get();
    }

    public function getAllUsersEditorContracts()
    {
        $today = date('Y-m-d');
        $twoMonths = date('Y-m-d', strtotime('+2 months', strtotime($today)));

        return User::
            whereHas('roles', function ($query) {
                $query->
                    where('role_name', 'like', '%Editor');
            })->
            whereHas('user_type', function($query) use ($twoMonths) {
                $query->
                    where('tbl_user_type_detail.status', 1)-> # dimana status contractnya active
                    where('tbl_user_type_detail.end_date', $twoMonths)-> # dimana end date nya sudah H-2 weeks
                    where('tbl_user_type.type_name', 'Part-Time');
            })->get();
    }

    public function getAllUsersExternalMentorContracts()
    {
        # make sure external mentor adalah yg part-time??

        $today = date('Y-m-d');
        $twoMonths = date('Y-m-d', strtotime('+2 months', strtotime($today)));

        return User::
            whereHas('roles', function ($query) {
                $query->
                    where('role_name', 'Mentor');
            })->
            whereDoesntHave('roles', function ($query) {
                $query->
                    where('role_name', 'Employee');
            })->
            whereHas('user_type', function($query) use ($twoMonths) {
                $query->
                    where('tbl_user_type_detail.status', 1)-> # dimana status contractnya active
                    where('tbl_user_type_detail.end_date', $twoMonths)-> # dimana end date nya sudah H-2 weeks
                    where('tbl_user_type.type_name', 'Part-Time');
            })->
            get();
    }

    public function getAllUsersInternshipContracts()
    {
        $today = date('Y-m-d');
        $oneMonth = date('Y-m-d', strtotime('+1 months', strtotime($today)));

        return User::
            whereHas('user_type', function($query) use ($oneMonth) {
                $query->
                    where('tbl_user_type_detail.status', 1)-> # dimana status contractnya active
                    where('tbl_user_type_detail.end_date', $oneMonth)-> # dimana end date nya sudah H-2 weeks
                    where('tbl_user_type.type_name', 'Internship');
            })->
            get();
    }

    public function getUserById($userId)
    {
        return User::findOrFail($userId);
    }

    public function getUserByExtendedId($extendedId)
    {
        return User::whereExtendedId($extendedId);
    }

    public function getUserByFullNameOrEmail($userName, $userEmail)
    {
        $userName = explode(' ', $userName);

        return User::where(function ($extquery) use ($userName) {

            # search word by word 
            # and loop based on name length
            for ($i = 0; $i < count($userName); $i++) {

                # looping at least two times
                if ($i <= 1)
                    $extquery = $extquery->whereRaw("CONCAT(first_name, ' ', last_name) like ?", ['%' . $userName[$i] . '%']);
            }
        })->orWhere('email', $userEmail)->first();
    }

    public function getUserByfirstName($first_name)
    {
        return User::where(DB::raw("SUBSTRING_INDEX(first_name, ' ', 1)"), $first_name)->first();
    }

    public function createUsers(array $userDetails)
    {
        return User::insert($userDetails);
    }

    public function createUser(array $userDetails)
    {
        return User::create($userDetails);
    }

    public function updateUser($userId, array $newDetails)
    {
        return User::find($userId)->update($newDetails);
    }

    public function updateStatusUser($userId, $newStatus)
    {
        # update status users
        $user = User::find($userId)->update(['active' => $newStatus]);

        # update status user type detail
        switch ($newStatus) {

            case 0: # deactivate
                return UserTypeDetail::where('user_id', $userId)->where('status', 1)->update([
                    'status' => 0,
                ]);
                break;

            case 1: # activate
                return UserTypeDetail::where('user_id', $userId)->where('status', 0)->whereNull('deactivated_at')->update([
                    'status' => 1,
                ]);
                break;
        }
    }

    public function updateExtendedId($newDetails)
    {
    }

    public function deleteUserType($userTypeId)
    {
        return UserTypeDetail::destroy($userTypeId);
    }

    public function getUserRoles($userId, $roleName)
    {
        return User::where('id', $userId)->whereHas('roles', function (Builder $query) use ($roleName) {
            $query->where('role_name', '=', $roleName);
        })->first();
    }

    public function cleaningUser()
    {
        User::where('last_name', '=', '')->update(
            [
                'last_name' => null
            ]
        );

        User::where('address', '=', '')->update(
            [
                'address' => null
            ]
        );

        User::where('emergency_contact', '=', '')->orWhere('emergency_contact', '=', '-')->update(
            [
                'emergency_contact' => null
            ]
        );

        User::where('password', '=', '')->update(
            [
                'password' => null
            ]
        );
    }

    public function createUserEducation(User $user, array $userEducationDetails)
    {
        for ($i = 0; $i < count($userEducationDetails['listGraduatedFrom']); $i++) {
            $user->educations()->attach($userEducationDetails['listGraduatedFrom'][$i], [
                'major_id' => $userEducationDetails['listMajor'][$i],
                'degree' => $userEducationDetails['listDegree'][$i],
                'graduation_date' => $userEducationDetails['listGraduationDate'][$i] ?? null
            ]);
        }
    }

    public function createUserRole(User $user, array $userRoleDetails)
    {
        for ($i = 0; $i < count($userRoleDetails['listRoles']); $i++) {
            $ext_id_with_label = null;
            if ($userRoleDetails['listRoles'][$i] == "Mentor") {
                # generate secondary extended_id 
                $last_id = UserRole::max('extended_id');
                $ext_id_without_label = $this->remove_primarykey_label($last_id, 3);
                $ext_id_with_label = 'MT-' . $this->add_digit((int)$ext_id_without_label + 1, 4);
            }

            $roleDetails = [
                'extended_id' => $ext_id_with_label,
                'tutor_subject' => $userRoleDetails['tutorSubject'],
                'feehours' => $userRoleDetails['feeHours'],
                'feesession' => $userRoleDetails['feeSession'],
            ];

            $user->roles()->attach($userRoleDetails['listRoles'][$i], $roleDetails);
        }
    }

    public function createUserType(User $user, array $userTypeDetails)
    {
        $user->user_type()->attach($userTypeDetails['listType'], [
            'department_id' => $userTypeDetails['departmentThatUserWorkedIn'],
            'start_date' => $userTypeDetails['startWorking'],
            'end_date' => $userTypeDetails['stopWorking'],
        ]);
    }
}
