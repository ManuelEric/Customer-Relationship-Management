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

    public function rnGetAllUsersByRoleDataTables($role)
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

    public function rnGetAllUsers()
    {
        return User::all();
    }

    public function rnGetAllUsersByRole($role)
    {
        return User::with('department')->role($role)->active()->orderBy('first_name', 'asc')->orderBy('last_name', 'asc')->get();
    }

    public function rnGetAllUsersByDepartmentAndRole($role, $department)
    {
        return User::role($role)->department($department)->active()->orderBy('first_name', 'asc')->orderBy('last_name', 'asc')->get();
    }
    
    public function rnGetUserById($userId)
    {
        return User::with('roles')->findOrFail($userId);
    }

    public function rnCreateUser(array $userDetails)
    {
        return User::create($userDetails);
    }

    public function rnUpdateUser($user_id, array $new_details)
    {
        return tap(User::find($user_id))->update($new_details);
    }

    public function rnUpdateStatusUser(User $user, array $new_status_details)
    {
        # update status users
        $user->update(['active' => $new_status_details['active']]);

        # update status user type detail
        switch ($new_status_details['active']) {

            case 0: # deactivate
                if($new_status_details['department'] != null && $new_status_details['department'] == 'Client Management')
                {
                    $pic_clients = PicClient::where('user_id', $user->id)->get();
    
                    foreach ($pic_clients as $pic_client) {
                        $pic_detail = [
                            'client_id' => $pic_client->client_id,
                            'user_id' => $new_status_details['new_pic'],
                            'created_at' => $new_status_details['deactivated_at'],
                            'updated_at' => $new_status_details['deactivated_at'],
                        ];
    
                        $this->clientRepository->updatePicClient($pic_client->id, $pic_detail);
                    }

                }

                UserTypeDetail::where('user_id', $user->id)->where('status', 1)->update([
                    'status' => 0,
                    'deactivated_at' => $new_status_details['deactivated_at']
                ]);
                break;

            case 1: # activate

                UserTypeDetail::where('user_id', $user->id)->where('status', 0)->update([
                    'status' => 1,
                    'deactivated_at' => null
                ]);
                break;
        }

        return $user;
    }

    public function rnDeleteUserType($user_type_id)
    {
        # store the soon deleted user type variable and returned it
        $user_type_detail = UserTypeDetail::find($user_type_id);
        UserTypeDetail::destroy($user_type_id);
        return $user_type_detail;
    }

    public function rnCreateUserEducation(User $user, array $user_education_details)
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
    
    public function rnUpdateUserEducation(User $user, array $new_user_education_details)
    {
        if ( (array_key_exists('graduated_from', $new_user_education_details) && ($new_user_education_details['graduated_from'] !== [null])) 
            && (array_key_exists('major', $new_user_education_details) && ($new_user_education_details['major'] !== [null]))
            && (array_key_exists('degree', $new_user_education_details) && ($new_user_education_details['degree'] !== [null]))
            && (array_key_exists('graduation_date', $new_user_education_details) && ($new_user_education_details['graduation_date'] !== [null]))
        )
        {
            for ($i = 0; $i < count($new_user_education_details['graduated_from']); $i++) {
                $detailEducations[] = [
                    'univ_id' => $new_user_education_details['graduated_from'][$i],
                    'major_id' => $new_user_education_details['major'][$i],
                    'degree' => $new_user_education_details['degree'][$i],
                    'graduation_date' => $new_user_education_details['graduation_date'][$i] ?? null
                ];
            }

            $user->educations()->sync($detailEducations);
        }
    }

    public function rnCreateOrUpdateUserSubject(User $user, Request $request)
    {
        # recollect user with user subjects
        $user = User::with('roles')->find($user->id);

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


        if ( ( (!array_key_exists('subject_id', $new_tutor_subject_details) && ($new_tutor_subject_details['subject_id'] !== []))
            || (!array_key_exists('fee_individual', $new_tutor_subject_details) && ($new_tutor_subject_details['fee_individual'] !== []))
            || (!array_key_exists('grade', $new_tutor_subject_details) && ($new_tutor_subject_details['grade'] !== []))
            || (!array_key_exists('head', $new_tutor_subject_details) && ($new_tutor_subject_details['head'] !== []))
            || (!array_key_exists('year', $new_tutor_subject_details) && ($new_tutor_subject_details['year'] !== [])) )
                && in_array(4, $user->roles()->pluck('tbl_roles.id')->toArray() )
        )
        {
            throw new Exception('Tutor subject information has to be provided when add a tutor.');
        }

        
        if ( !$user_tutor_identity = $user->roles()->where('role_name', 'Tutor')->first() )
        {
            Log::warning('Failed to add a subject for tutor!, User is not a Tutor', ['id' => $user->id]);
            return;
        }        
    

        for ($i = 0; $i < count($new_tutor_subject_details['subject_id']); $i++) 
        {
            if ( $user_subject = UserSubject::where('user_role_id', $user_tutor_identity->pivot->id)->where('subject_id', $new_tutor_subject_details['subject_id'][$i])->where('year', $new_tutor_subject_details['year'][$i])->first() )
            {
                $agreement = $user_subject->agreement ?? $this->tnUploadFile($request, 'agreement.'.$i, 'Agreement-' . str_replace(' ', '_', $request->first_name . '_' . $request->last_name . '-' . $request->subject_id[$i] .  '-' . date('Y')), 'public/uploaded_file/user/' . $user->id);
            }
            
            for($j = 0; $j < count($new_tutor_subject_details['grade'][$i]); $j++){

                $subject_details =  [
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
                ], $subject_details);
            }
        }        
    }

    public function rnCreateUserRole(User $user, array $user_role_details)
    {
        if ( (!array_key_exists('role', $user_role_details) && ($user_role_details['role'] !== [])) )
            throw new Exception('Role has to be provided.');
        
        $user->roles()->attach($user_role_details['role']);
    }

    public function rnUpdateUserRole(User $user, array $new_user_role_details)
    {
        /**
         * Developers notes:
         * we are not using sync method from laravel built-in functions
         * because if we are using sync method, the user_role_id will be changed and it will disrupt the process of rnCreateOrUpdateUserSubject
         */
        
        if ( (!array_key_exists('role', $new_user_role_details) && ($new_user_role_details['role'] !== [])) )
            throw new Exception('Role has to be provided.');

        # new incoming role
        $new_roles = $new_user_role_details['role'];

        # get existing user role
        $existing_roles = $user->roles()->pluck('tbl_roles.id')->toArray();

        if ( count($new_roles) == $user->roles()->whereIn('tbl_roles.id', $new_roles)->count() )
            return;


        # get the different new one
        $new_roles = array_values(array_diff($new_roles, $existing_roles));
        if ( count($new_roles) > 0 )
        {
            # attach the new roles
            $user->roles()->attach($new_roles);
        }


        if ( count($existing_roles) > 0 )
        {
            # get the roles that need to be removed
            $removed_role = array_values(array_diff($existing_roles, $new_roles));
            # detach the unused roles
            $user->roles()->detach($removed_role);
        }
    }

    public function rnCreateUserType(User $user, array $user_type_details)
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

    public function rnUpdateUserType(User $user, array $new_user_type_details)
    {
        if ( (!array_key_exists('type', $new_user_type_details) && ($new_user_type_details['type'] !== []))
            || (!array_key_exists('department', $new_user_type_details) && ($new_user_type_details['department'] !== []))
            || (!array_key_exists('start_period', $new_user_type_details) && ($new_user_type_details['start_period'] !== []))
            || (!array_key_exists('end_period', $new_user_type_details) && ($new_user_type_details['end_period'] !== []))
        )
            throw new Exception('Contract has to be provided.');

        # validate
        # in order to avoid double data
        $new_user_type = $new_user_type_details['type'];
        $new_department = $new_user_type_details['department'];
        $start_period = $new_user_type_details['start_period'];
        $end_period = $new_user_type_details['end_period'];


        if ( $user->user_type()->wherePivot('user_type_id', $new_user_type)->wherePivot('status', 1)->wherePivot('deactivated_at', NULL)->wherePivot('start_date', $start_period)->wherePivot('end_date', $end_period)->count() == 0 )
        {
            # deactivate the latest active type
            $active_type = $user->user_type()->where('tbl_user_type_detail.status', 1)->wherePivot('deactivated_at', NULL)->pluck('tbl_user_type_detail.user_type_id')->toArray();
            foreach ($active_type as $key => $value) {
                $user->user_type()->updateExistingPivot($value, ['status' => 0, 'deactivated_at' => Carbon::now()]);
            }

            # store new user type to tbl_user_type
            $user->user_type()->syncWithoutDetaching([[
                'user_type_id' => $new_user_type,
                'department_id' => $new_department,
                'start_date' => $start_period,
                'end_date' => $end_period,
            ]]);
        } else {
            $user->user_type()->updateExistingPivot($new_user_type, ['status' => 1, 'department_id' => $new_department, 'deactivated_at' => NULL]);
        }
    }

    public function rnGetUserSubjectById($user_subject_id)
    {
        return UserSubject::where('id', $user_subject_id)->first();
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
}
