<?php

namespace App\Repositories;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Models\ClientEvent;
use App\Models\ClientProgram;
use App\Models\PicClient;
use App\Models\pivot\UserRole;
use App\Models\pivot\UserSubject;
use App\Models\pivot\UserTypeDetail;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use DataTables;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class UserRepository implements UserRepositoryInterface
{
    use CreateCustomPrimaryKeyTrait;
    private ClientRepositoryInterface $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

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

    public function getPICs()
    {
        return User::isPic();
    }

    public function getUserById($userId)
    {
        return User::findOrFail($userId);
    }

    public function getUserByUUID($userUUID)
    {
        return User::where('uuid', $userUUID)->first();
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

    public function updateStatusUser($userId, array $detail)
    {
        # update status users
        $user = User::find($userId)->update(['active' => $detail]);

        # update status user type detail
        switch ($detail['status']) {

            case 0: # deactivate
                if($detail['department'] != null && $detail['department'] == 'Client Management')
                {
                    $picClients = PicClient::where('user_id', $userId)->get();
    
                    foreach ($picClients as $picClient) {
                        $picDetail = [
                            'client_id' => $picClient->client_id,
                            'user_id' => $detail['new_pic'],
                            'created_at' => $detail['deativated_at'],
                            'updated_at' => $detail['deativated_at'],
                        ];
    
                        $this->clientRepository->updatePicClient($picClient->id, $picDetail);
                    }

                }

                $this->updateUser($userId, ['active' => 0]);

                return UserTypeDetail::where('user_id', $userId)->where('status', 1)->update([
                    'status' => 0,
                    'deactivated_at' => $detail['deativated_at']
                ]);
                break;

            case 1: # activate
                
                $this->updateUser($userId, ['active' => 1]);

                return UserTypeDetail::where('user_id', $userId)->where('status', 0)->update([
                    'status' => 1,
                    'deactivated_at' => null
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

    public function createOrUpdateUserSubject(User $user, $request, $user_id_with_label)
    {
        $user_role_id = $user->roles()->where('role_name', 'Tutor')->first()->pivot->id;
        $subjectDetails = [];
        $agreement_file_path = null;
        
        $isErrorAgreement = [false, 0];

        if($user_role_id == null){
            Log::warning('Failed to create user subject!, User is not Tutor', ['id' => $user->id]);
            return;
        }

        for ($i = 0; $i < count($request->subject_id); $i++) {
            if($request->hasFile('agreement.'.$i)){
                $agreement_file_format = $request->file('agreement.'.$i)->getClientOriginalExtension();
                $agreement_file_name = 'Agreement-' . str_replace(' ', '_', $request->first_name . '_' . $request->last_name . '-' . $request->subject_id[$i] .  '-' . date('Y'));
                $agreement_file_path = $request->file('agreement.'.$i)->storeAs('public/uploaded_file/user/' . $user_id_with_label, $agreement_file_name . '.' . $agreement_file_format);

                for($j = 0; $j < count($request->grade[$i]); $j++){
                    $subjectDetails =  [
                        'fee_individual' => $request->fee_individual[$i][$j],
                        'fee_group' => $request->fee_group[$i][$j],
                        'additional_fee' => $request->additional_fee[$i][$j],
                        'head' => $request->head[$i][$j],
                        'agreement' => $agreement_file_path,
                    ];
                    $user->user_subjects()->updateOrCreate([
                        'user_role_id' => $user_role_id,
                        'subject_id' => $request->subject_id[$i],
                        'grade' => $request->grade[$i][$j],
                        'year' => $request->year[$i]
                    ], $subjectDetails);
                }
            }else{
                if($request->isMethod('POST')){
                    return $isErrorAgreement = [true, $i];
                }
                for($j = 0; $j < count($request->grade[$i]); $j++){
                    $subjectDetails =  [
                        'fee_individual' => $request->fee_individual[$i][$j],
                        'fee_group' => $request->fee_group[$i][$j],
                        'additional_fee' => $request->additional_fee[$i][$j],
                        'head' => $request->head[$i][$j],
                        'agreement' => isset($request->agreement_text) && $request->agreement_text[$i] != null ? $request->agreement_text[$i] : null
                    ];
                    $user->user_subjects()->updateOrCreate([
                        'user_role_id' => $user_role_id,
                        'subject_id' => $request->subject_id[$i],
                        'grade' => $request->grade[$i][$j],
                        'year' => $request->year[$i]
                    ], $subjectDetails);
                }
            }  
        }
        
        return $isErrorAgreement;
        
    }

    public function createUserRole(User $user, array $userRoleDetails)
    {
        for ($i = 0; $i < count($userRoleDetails['listRoles']); $i++) {
            $ext_id_with_label = null;
            if ($userRoleDetails['listRoles'][$i] == 2) {
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

    public function getUserSubjectById($user_subject_id)
    {
        return UserSubject::where('id', $user_subject_id)->first();
    }
}
