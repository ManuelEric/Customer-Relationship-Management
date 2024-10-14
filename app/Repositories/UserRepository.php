<?php

namespace App\Repositories;

use App\Enum\ContractUserType;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\UploadFileTrait;
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
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class UserRepository implements UserRepositoryInterface
{
    use UploadFileTrait;
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
                    'first_name',
                    'last_name',
                    DB::raw('CONCAT(first_name, " ", COALESCE(last_name, "")) as full_name'),
                    'email',
                    'phone',
                    'tbl_position.position_name',
                    DB::raw('(SELECT GROUP_CONCAT(tbl_univ.univ_name SEPARATOR ", ") FROM tbl_user_educations
                        JOIN tbl_univ on tbl_univ.univ_id = tbl_user_educations.univ_id
                        WHERE users.id = tbl_user_educations.user_id
                        GROUP BY tbl_user_educations.user_id ORDER BY tbl_user_educations.degree ASC) as graduation_from'),
                    DB::raw('(SELECT GROUP_CONCAT(tbl_major.name SEPARATOR ", ") FROM tbl_user_educations
                        JOIN tbl_major ON tbl_major.id = tbl_user_educations.major_id
                        WHERE users.id = tbl_user_educations.user_id
                        GROUP BY tbl_user_educations.user_id ORDER BY tbl_user_educations.degree ASC) as major_group'),
                    'datebirth',
                    'nik',
                    'npwp',
                    'account_no as bankacc',
                    'emergency_contact_phone as emergency_contact',
                    'address',
                    'active',

                ])
        )
            ->filterColumn('full_name', function ($query, $keyword) {
                $sql = 'CONCAT(first_name, " ", COALESCE(last_name, "")) like ?';
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->filterColumn('graduation_from', function ($query, $keyword) {
                $sql = '(SELECT GROUP_CONCAT(tbl_univ.univ_name SEPARATOR ", ") FROM tbl_user_educations
                        LEFT JOIN tbl_univ on tbl_univ.univ_id = tbl_user_educations.univ_id
                        WHERE users.id = tbl_user_educations.user_id
                        GROUP BY tbl_user_educations.user_id ORDER BY tbl_user_educations.degree ASC) like ?';
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->filterColumn('major_group', function ($query, $keyword) {
                $sql = '(SELECT GROUP_CONCAT(tbl_major.name SEPARATOR ", ") FROM tbl_user_educations
                        LEFT JOIN tbl_major ON tbl_major.id = tbl_user_educations.major_id
                        WHERE users.id = tbl_user_educations.user_id
                        GROUP BY tbl_user_educations.user_id ORDER BY tbl_user_educations.degree ASC) like ?';
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
        return User::whereNull('id')->get();
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

    //! new methods start

    public function rnFindExpiringContracts(ContractUserType $type)
    {
        return User::query()->
            when(ContractUserType::EDITOR, function ($query) {
                $expected_end_date = Carbon::now()->addMonth(2);
                $query->editor()->partTime($expected_end_date);
            })->
            when(ContractUserType::EXTERNAL_MENTOR, function ($query) {
                $expected_end_date = Carbon::now()->addMonth(2);
                $query->externalMentor()->partTime($expected_end_date);
            })->
            when(ContractUserType::TUTOR, function ($query) {
                $expected_end_date = Carbon::now()->addMonth(2);
                $query->tutor()->partTime($expected_end_date);
            })->
            when(ContractUserType::INTERNSHIP, function ($query) {
                $expected_end_date = Carbon::now()->addMonth(1);
                $query->internship($expected_end_date);
            })->
            when(ContractUserType::PROBATION, function ($query) {
                $expected_end_date = Carbon::now()->addWeek(2);
                $query->partTime($expected_end_date);
            })->
            with(['user_type'])->
            lazy();
    }

    //! new methods end

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
        return User::where('id', $userUUID)->first();
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

    public function createUserEducation(User $user, array $user_education_details)
    {
        if ( (array_key_exists('graduated_from', $user_education_details) && ($user_education_details['graduated_from'] !== [])) 
            && (array_key_exists('major', $user_education_details) && ($user_education_details['major'] !== []))
            && (array_key_exists('degree', $user_education_details) && ($user_education_details['degree'] !== []))
            && (array_key_exists('graduation_date', $user_education_details) && ($user_education_details['graduation_date'] !== []))
        )
        {
            for ($i = 0; $i < count($user_education_details['graduated_from']); $i++) 
            {
                $user->educations()->attach($user_education_details['graduated_from'][$i], [
                    'major_id' => $user_education_details['major'][$i],
                    'degree' => $user_education_details['degree'][$i],
                    'graduation_date' => $user_education_details['graduation_date'][$i] ?? null
                ]);
            }
        }

    }

    public function createOrUpdateUserSubject(User $user, Request $request)
    {
        # recollect user with user subjects
        $user = User::with(['roles', 'roles.subjects'])->find($user->id);

        # variables for tutor subject
        $new_tutor_subject_details = $request->only([
            'subject_id',
            'grade',
            'agreement',
            'fee_individual',
            'fee_group',
            'additional_fee',
            'head',
            'year',
        ]);


        if ( (!array_key_exists('subject_id', $new_tutor_subject_details) && ($new_tutor_subject_details['subject_id'] !== []))
            || (!array_key_exists('grade', $new_tutor_subject_details) && ($new_tutor_subject_details['grade'] !== []))
            || (!array_key_exists('agreement', $new_tutor_subject_details) && ($new_tutor_subject_details['agreement'] !== []))
            || (!array_key_exists('fee_individual', $new_tutor_subject_details) && ($new_tutor_subject_details['fee_individual'] !== []))
            || (!array_key_exists('head', $new_tutor_subject_details) && ($new_tutor_subject_details['head'] !== []))
        )
        {
            throw new Exception('Subject has to be provided.');
        }

        
        if ( !$user_tutor_identity = $user->roles()->where('role_name', 'Tutor')->first() )
        {
            Log::warning('Failed to add a subject for tutor!, User is not Tutor', ['id' => $user->id]);
            return;
        }
    

        for ($i = 0; $i < count($new_tutor_subject_details['subject_id']); $i++) 
        {

            if ( $user_subject = UserSubject::find($user_tutor_identity->pivot->id) )
                $agreement = $user_subject->agreement;
            else
                $agreement = $this->tnUploadFile($request, 'agreement.'.$i, 'Agreement-' . str_replace(' ', '_', $request->first_name . '_' . $request->last_name . '-' . $request->subject_id[$i] .  '-' . date('Y')), 'public/uploaded_file/user/' . $user->id);


            for($j = 0; $j < count($new_tutor_subject_details['grade'][$i]); $j++){

                $subjectDetails =  [
                    'fee_individual' => $new_tutor_subject_details['fee_individual'][$i][$j],
                    'fee_group' => $new_tutor_subject_details['fee_group'][$i][$j],
                    'additional_fee' => $new_tutor_subject_details['additional_fee'][$i][$j],
                    'head' => $new_tutor_subject_details['head'][$i][$j],
                    'agreement' => $agreement,
                ];
                $user->user_subjects()->updateOrCreate([
                    'user_role_id' => $user_tutor_identity->pivot->id,
                    'subject_id' => $new_tutor_subject_details['subject_id'][$i],
                    'grade' => $new_tutor_subject_details['grade'][$i][$j],
                    'year' => $new_tutor_subject_details['year'][$i]
                ], $subjectDetails);
            }
        }        
    }

    public function createUserRole(User $user, array $user_role_details)
    {
        if ( (!array_key_exists('role', $user_role_details) && ($user_role_details['role'] !== [])) )
            throw new Exception('Role has to be provided.');
        
        $user->roles()->attach($user_role_details['role']);
    }

    public function createUserType(User $user, array $user_type_details)
    {
        if ( (!array_key_exists('type', $user_type_details) && ($user_type_details['type'] !== []))
            || (!array_key_exists('department', $user_type_details) && ($user_type_details['department'] !== []))
            || (!array_key_exists('start_period', $user_type_details) && ($user_type_details['start_period'] !== []))
        )
            throw new Exception('Contract has to be provided.');
        
        $user->user_type()->attach($user_type_details['type'], [
            'department_id' => $user_type_details['department'],
            'start_date' => $user_type_details['start_period'],
            'end_date' => $user_type_details['end_period'],
        ]);
    }

    public function getUserSubjectById($user_subject_id)
    {
        return UserSubject::where('id', $user_subject_id)->first();
    }
}
