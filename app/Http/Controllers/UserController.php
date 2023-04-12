<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Interfaces\DepartmentRepositoryInterface;
use App\Interfaces\MajorRepositoryInterface;
use App\Interfaces\PositionRepositoryInterface;
use App\Interfaces\UniversityRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\UserTypeRepositoryInterface;
use App\Models\pivot\UserRole;
use App\Models\pivot\UserTypeDetail;
use App\Models\User;
use App\Models\UserType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    use CreateCustomPrimaryKeyTrait;
    use StandardizePhoneNumberTrait;

    private UserRepositoryInterface $userRepository;
    private UniversityRepositoryInterface $universityRepository;
    private MajorRepositoryInterface $majorRepository;
    private DepartmentRepositoryInterface $departmentRepository;
    private PositionRepositoryInterface $positionRepository;
    private UserTypeRepositoryInterface $userTypeRepository;

    public function __construct(UserRepositoryInterface $userRepository, UniversityRepositoryInterface $universityRepository, MajorRepositoryInterface $majorRepository, DepartmentRepositoryInterface $departmentRepository, PositionRepositoryInterface $positionRepository, UserTypeRepositoryInterface $userTypeRepository)
    {
        $this->userRepository = $userRepository;
        $this->universityRepository = $universityRepository;
        $this->majorRepository = $majorRepository;
        $this->departmentRepository = $departmentRepository;
        $this->positionRepository = $positionRepository;
        $this->userTypeRepository = $userTypeRepository;
    }

    public function index(Request $request)
    {
        $role = $request->route('user_role');
        if ($request->ajax())
            return $this->userRepository->getAllUsersByRoleDataTables($role);

        return view('pages.user.employee.index');
    }

    public function store(StoreUserRequest $request)
    {
        $userDetails = $request->only([
            'first_name',
            'last_name',
            'email',
            'phone',
            'emergency_contact',
            'datebirth',
            'address',
            'hiredate',
            'nik',
            'bankname',
            'bankacc',
            'npwp',
        ]);
        unset($userDetails['phone']);
        unset($userDetails['emergency_contact']);
        $userDetails['phone'] = $this->setPhoneNumber($request->phone);
        $userDetails['emergency_contact'] = $this->setPhoneNumber($request->emergency_contact);

        # generate default password which is 12345678
        $userDetails['password'] = Hash::make('12345678'); # update
        $userDetails['position_id'] = $request->position;

        # generate extended_id
        $last_id = User::max('extended_id');
        $user_id_without_label = $this->remove_primarykey_label($last_id, 5);
        $user_id_with_label = 'EMPL-' . $this->add_digit((int)$user_id_without_label + 1, 4);
        $userDetails['extended_id'] = $user_id_with_label;

        # when generated user id with label is exists on database
        # then generate a new one
        if ($this->userRepository->getUserByExtendedId($user_id_with_label)) {
            $user_id_without_label = $this->remove_primarykey_label($user_id_with_label, 5);
            $user_id_with_label = 'EMPL-' . $this->add_digit((int)$user_id_without_label + 1, 4);
            $userDetails['extended_id'] = $user_id_with_label;
        }

        DB::beginTransaction();
        try {

            # store new user
            $newUser = $this->userRepository->createUser($userDetails);
            $newUserId = $newUser->id;

            # store new user education to tbl_user_education
            for ($i = 0; $i < count($request->graduated_from); $i++) {
                $newUser->educations()->attach($request->graduated_from[$i], [
                    'major_id' => $request->major[$i],
                    'degree' => $request->degree[$i],
                    'graduation_date' => $request->graduation_date[$i] ?? null
                ]);
            }

            # store new user role to tbl_user_roles
            for ($i = 0; $i < count($request->role); $i++) {
                $ext_id_with_label = null;
                if ($request->role[$i] == "mentor") {
                    # generate secondary extended_id 
                    $last_id = UserRole::max('extended_id');
                    $ext_id_without_label = $this->remove_primarykey_label($last_id, 3);
                    $ext_id_with_label = 'MT-' . $this->add_digit((int)$ext_id_without_label + 1, 4);
                }

                $roleDetails = [
                    'extended_id' => $ext_id_with_label,
                    'tutor_subject' => isset($request->tutor_subject) ? $request->tutor_subject : null,
                    'feehours' => isset($request->feehours) ? $request->feehours : null,
                    'feesession' => isset($request->feesession) ? $request->feesession : null,
                ];

                $newUser->roles()->attach($request->role[$i], $roleDetails);
            }

            # store new user type to tbl_user_type
            $newUser->user_type()->attach($request->type, [
                'department_id' => $request->department,
                'start_date' => $request->start_period,
                'end_date' => $request->end_period,
            ]);

            # upload curriculum vitae
            $CV_file_path = null;
            if ($request->hasFile('curriculum_vitae')) {
                $CV_file_format = $request->file('curriculum_vitae')->getClientOriginalExtension();
                $CV_file_name = 'CV-' . str_replace(' ', '_', $request->first_name . '_' . $request->last_name);
                $CV_file_path = $request->file('curriculum_vitae')->storeAs('public/uploaded_file/user/' . $user_id_with_label, $CV_file_name . '.' . $CV_file_format);
            }


            # upload KTP / idcard
            if ($request->hasFile('idcard')) {
                $ID_file_format = $request->file('idcard')->getClientOriginalExtension();
                $ID_file_name = 'ID-' . str_replace(' ', '_', $request->first_name . '_' . $request->last_name);
                $ID_file_path = $request->file('idcard')->storeAs('public/uploaded_file/user/' . $user_id_with_label, $ID_file_name . '.' . $ID_file_format);
            }

            # upload tax
            if ($request->hasFile('tax')) {
                $TX_file_format = $request->file('tax')->getClientOriginalExtension();
                $TX_file_name = 'TAX-' . str_replace(' ', '_', $request->first_name . '_' . $request->last_name);
                $TX_file_path = $request->file('tax')->storeAs('public/uploaded_file/user/' . $user_id_with_label, $TX_file_name . '.' . $TX_file_format);
            }

            # upload bpjs kesehatan / health insurance
            if ($request->hasFile('health_insurance')) {
                $HI_file_format = $request->file('health_insurance')->getClientOriginalExtension();
                $HI_file_name = 'HI-' . str_replace(' ', '_', $request->first_name . '_' . $request->last_name);
                $HI_file_path = $request->file('health_insurance')->storeAs('public/uploaded_file/user/' . $user_id_with_label, $HI_file_name . '.' . $HI_file_format);
            }

            # upload bpjs ketenagakerjaan / empl insurance
            if ($request->hasFile('empl_insurance')) {
                $EI_file_format = $request->file('empl_insurance')->getClientOriginalExtension();
                $EI_file_name = 'EI-' . str_replace(' ', '_', $request->first_name . '_' . $request->last_name);
                $EI_file_path = $request->file('empl_insurance')->storeAs('public/uploaded_file/user/' . $user_id_with_label, $EI_file_name . '.' . $EI_file_format);
            }

            # update uploaded data to user table
            $this->userRepository->updateUser($newUserId, [
                'idcard' => $ID_file_path,
                'cv' => $CV_file_path,
                'tax' => $TX_file_path,
                'health_insurance' => $HI_file_path,
                'empl_insurance' => $EI_file_path
            ]);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store user ' . $request->route('user_role') . ' failed : ' . $e->getMessage());
            return Redirect::back()->withError('Failed to create a new ' . $request->route('user_role'));
        }

        return Redirect::to('user/' . $request->route('user_role'))->withSuccess('New ' . $request->route('user_role') . ' has been created');
    }

    public function create(Request $request)
    {
        $universities = $this->universityRepository->getAllUniversities();
        $univ_countries = $this->universityRepository->getCountryNameFromUniversity();
        $majors = $this->majorRepository->getAllMajors();
        $departments = $this->departmentRepository->getAllDepartment();
        $positions = $this->positionRepository->getAllPositions();
        $user_types = $this->userTypeRepository->getAllUserType();

        return view('pages.user.employee.form')->with(
            [
                'universities' => $universities,
                'univ_countries' => $univ_countries,
                'majors' => $majors,
                'departments' => $departments,
                'positions' => $positions,
                'user_types' => $user_types,
            ]
        );
    }

    public function changeStatus(Request $request)
    {
        $userId = $request->route('user');
        $user = $this->userRepository->getUserById($userId);
        $data = $request->params;
        $status = $data['new_status'];
        $newStatus = $status == "activate" ? 1 : 0;


        DB::beginTransaction();
        try {

            # update on users table
            $this->userRepository->updateStatusUser($userId, $newStatus);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error(ucfirst($status) . ' ' . $user->full_name . ' failed : ' . $e->getMessage());
            return response()->json(['message' => 'Failed to ' . $status . ' ' . $user->full_name], 422);
        }

        return response()->json(['message' => ucwords($user->full_name) . ' has been ' . $status], 200);
    }

    public function update(StoreUserRequest $request)
    {
        $userId = $request->route('user');
        $user = $this->userRepository->getUserById($userId);

        $user_id_with_label = $user->extended_id;

        $newDetails = $request->only([
            'first_name',
            'last_name',
            'email',
            'phone',
            'emergency_contact',
            'datebirth',
            'address',
            'hiredate',
            'nik',
            'bankname',
            'bankacc',
            'npwp',
        ]);
        unset($newDetails['phone']);
        unset($newDetails['emergency_contact']);
        $newDetails['phone'] = $this->setPhoneNumber($request->phone);
        $newDetails['emergency_contact'] = $this->setPhoneNumber($request->emergency_contact);

        $newDetails['position_id'] = $request->position;

        DB::beginTransaction();
        try {

            # update user
            $newUser = $this->userRepository->updateUser($userId, $newDetails);

            # update user education to tbl_user_education
            for ($i = 0; $i < count($request->graduated_from); $i++) {
                $detailEducations[] = [
                    'univ_id' => $request->graduated_from[$i],
                    'major_id' => (int) $request->major[$i],
                    'degree' => $request->degree[$i],
                    'graduation_date' => $request->graduation_date[$i] ?? null
                ];
            }

            $user->educations()->sync($detailEducations);


            # update user role to tbl_user_roles
            for ($i = 0; $i < count($request->role); $i++) {

                $ext_id_with_label = null;
                if ($request->role[$i] == 2) { # 2 means mentor

                    # if user has the requested role
                    # then save the extended_id
                    if ($existingRoleInfo = $user->roles()->where('tbl_roles.id', $request->role[$i])->first()) {

                        $ext_id_with_label = $existingRoleInfo->pivot->extended_id;
                    } else {
                        # generate secondary extended_id 
                        $last_id = UserRole::max('extended_id');
                        $ext_id_without_label = $this->remove_primarykey_label($last_id, 3);
                        $ext_id_with_label = 'MT-' . $this->add_digit((int)$ext_id_without_label + 1, 4);
                    }
                }


                $roleDetails[] = [
                    'role_id' => $request->role[$i],
                    'extended_id' => $ext_id_with_label,
                    'tutor_subject' => isset($request->tutor_subject) ? $request->tutor_subject : null,
                    'feehours' => isset($request->feehours) ? $request->feehours : null,
                    'feesession' => isset($request->feesession) ? $request->feesession : null,
                ];
            }
            $user->roles()->syncWithoutDetaching($roleDetails);

            # validate
            # in order to avoid double data
            $newUserType = $request->type;
            $newDepartment = $request->department;
            if ($user->user_type()->wherePivot('user_type_id', $newUserType)->wherePivot('department_id', $newDepartment)->wherePivot('status', 1)->wherePivot('deactivated_at', NULL)->count() == 0) {
                // if ($user->user_type()->wherePivot('user_type_id', 2)->wherePivot('department_id', 3)->wherePivot('start_date', '2022-12-01')->wherePivot('end_date', '2023-01-01')->count() == 0) {

                # deactivate the latest active type
                $activeType = $user->user_type()->where('tbl_user_type_detail.status', 1)->whereNull('deactivated_at')->pluck('tbl_user_type_detail.user_type_id')->toArray();
                foreach ($activeType as $key => $value) {

                    $user->user_type()->updateExistingPivot($activeType[$key], ['status' => 0, 'deactivated_at' => Carbon::now()]);
                }

                # store new user type to tbl_user_type
                $user->user_type()->syncWithoutDetaching([[
                    'user_type_id' => $request->type,
                    'department_id' => $request->department,
                    'start_date' => $request->start_period,
                    'end_date' => $request->type == 1 ? null : $request->end_period,
                ]]);
            }

            # upload curriculum vitae
            $CV_file_path = $user->cv;
            if ($request->hasFile('curriculum_vitae')) {
                $CV_file_format = $request->file('curriculum_vitae')->getClientOriginalExtension();
                $CV_file_name = 'CV-' . str_replace(' ', '_', $request->first_name . '_' . $request->last_name);
                $CV_file_path = $request->file('curriculum_vitae')->storeAs('public/uploaded_file/user/' . $user_id_with_label, $CV_file_name . '.' . $CV_file_format);
            }

            # upload KTP / idcard
            $ID_file_path = $user->idcard;
            if ($request->hasFile('idcard')) {
                $ID_file_format = $request->file('idcard')->getClientOriginalExtension();
                $ID_file_name = 'ID-' . str_replace(' ', '_', $request->first_name . '_' . $request->last_name);
                $ID_file_path = $request->file('idcard')->storeAs('public/uploaded_file/user/' . $user_id_with_label, $ID_file_name . '.' . $ID_file_format);
            }

            # upload tax
            $TX_file_path = $user->tax;
            if ($request->hasFile('tax')) {
                $TX_file_format = $request->file('tax')->getClientOriginalExtension();
                $TX_file_name = 'TAX-' . str_replace(' ', '_', $request->first_name . '_' . $request->last_name);
                $TX_file_path = $request->file('tax')->storeAs('public/uploaded_file/user/' . $user_id_with_label, $TX_file_name . '.' . $TX_file_format);
            }

            # upload bpjs kesehatan / health insurance
            $HI_file_path = $user->health_insurance;
            if ($request->hasFile('health_insurance')) {
                $HI_file_format = $request->file('health_insurance')->getClientOriginalExtension();
                $HI_file_name = 'HI-' . str_replace(' ', '_', $request->first_name . '_' . $request->last_name);
                $HI_file_path = $request->file('health_insurance')->storeAs('public/uploaded_file/user/' . $user_id_with_label, $HI_file_name . '.' . $HI_file_format);
            }

            # upload bpjs ketenagakerjaan / empl insurance
            $EI_file_path = $user->empl_insurance;
            if ($request->hasFile('empl_insurance')) {
                $EI_file_format = $request->file('empl_insurance')->getClientOriginalExtension();
                $EI_file_name = 'EI-' . str_replace(' ', '_', $request->first_name . '_' . $request->last_name);
                $EI_file_path = $request->file('empl_insurance')->storeAs('public/uploaded_file/user/' . $user_id_with_label, $EI_file_name . '.' . $EI_file_format);
            }

            # update uploaded data to user table
            if ($request->hasFile('curriculum_vitae') || $request->hasFile('idcard') || $request->hasFile('tax') || $request->hasFile('health_insurance') || $request->hasFile('empl_insurance')) {
                $this->userRepository->updateUser($userId, [
                    'idcard' => $ID_file_path,
                    'cv' => $CV_file_path,
                    'tax' => $TX_file_path,
                    'health_insurance' => $HI_file_path,
                    'empl_insurance' => $EI_file_path
                ]);
            }
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update user ' . $request->route('user_role') . ' failed : ' . $e->getMessage());
            return Redirect::back()->withError('Failed to update ' . $request->route('user_role'));
        }

        return Redirect::to('user/' . $request->route('user_role') . '/' . $userId . '/edit')->withSuccess(ucfirst($request->route('user_role')) . ' has been updated');
    }

    public function edit(Request $request)
    {
        $userId = $request->route('user');
        $user = $this->userRepository->getUserById($userId);

        $universities = $this->universityRepository->getAllUniversities();
        $univ_countries = $this->universityRepository->getCountryNameFromUniversity();
        $majors = $this->majorRepository->getAllMajors();
        $departments = $this->departmentRepository->getAllDepartment();
        $positions = $this->positionRepository->getAllPositions();
        $user_types = $this->userTypeRepository->getAllUserType();

        return view('pages.user.employee.form')->with(
            [
                'universities' => $universities,
                'univ_countries' => $univ_countries,
                'majors' => $majors,
                'departments' => $departments,
                'positions' => $positions,
                'user_types' => $user_types,
                'user' => $user
            ]
        );
    }

    public function download(Request $request)
    {
        $userId = $request->route('user');
        $user = $this->userRepository->getUserById($userId);

        switch ($request->route('filetype')) {

            case "CV":
                $file = Storage::disk('local')->get($user->cv);
                break;

            case "ID":
                $file = Storage::disk('local')->get($user->idcard);
                break;

            case "TX":
                $file = Storage::disk('local')->get($user->tax);
                break;

            case "HI":
                $file = Storage::disk('local')->get($user->health_insurance);
                break;

            case "EI":
                $file = Storage::disk('local')->get($user->empl_insurance);
                break;
        }

        return response($file)->header('Content-Type', 'application/pdf');
    }

    public function destroy(Request $request)
    {
        $userId = $request->user;
        $newStatus = 0; # inactive
        DB::beginTransaction();
        try {

            $this->userRepository->updateStatusUser($userId, $newStatus);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Deactive user failed : ' . $e->getMessage());
            return Redirect::back()->withError('Failed to deactive user');
        }

        return Redirect::back()->withSuccess('User successfully deactivated');
    }

    public function destroyUserType(Request $request)
    {
        $userId = $request->route('user');
        $userTypeId = $request->route('user_type');

        DB::beginTransaction();
        try {

            $this->userRepository->deleteUserType($userTypeId);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete user type failed : ' . $e->getMessage());
            return Redirect::back()->withError('Failed to delete user type');
        }

        return Redirect::to('user/' . $request->route('user_role') . '/' . $userId . '/edit')->withSuccess(ucfirst($request->route('user_role')) . ' has been updated');
    }

    public function setPassword(Request $request)
    {
        $userId = $request->route('user');
        $userDetails['password'] = Hash::make('12345678');

        DB::beginTransaction();
        try {

            # update on users table
            $this->userRepository->updateUser($userId, $userDetails);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('failed to set password' . $e->getMessage());
            return response()->json(['message' => 'Failed to set password'], 422);
        }

        return response()->json(['message' => 'Password has been set'], 200);
    }
}
