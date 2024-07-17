<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\LoggingTrait;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Interfaces\DepartmentRepositoryInterface;
use App\Interfaces\MajorRepositoryInterface;
use App\Interfaces\PositionRepositoryInterface;
use App\Interfaces\SubjectRepositoryInterface;
use App\Interfaces\UniversityRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\UserTypeRepositoryInterface;
use App\Models\pivot\UserRole;
use App\Models\pivot\UserSubject;
use App\Models\pivot\UserTypeDetail;
use App\Models\User;
use App\Models\UserType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
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
    use LoggingTrait;

    private UserRepositoryInterface $userRepository;
    private UniversityRepositoryInterface $universityRepository;
    private MajorRepositoryInterface $majorRepository;
    private DepartmentRepositoryInterface $departmentRepository;
    private PositionRepositoryInterface $positionRepository;
    private UserTypeRepositoryInterface $userTypeRepository;
    private SubjectRepositoryInterface $subjectRepository;

    public function __construct(UserRepositoryInterface $userRepository, UniversityRepositoryInterface $universityRepository, MajorRepositoryInterface $majorRepository, DepartmentRepositoryInterface $departmentRepository, PositionRepositoryInterface $positionRepository, UserTypeRepositoryInterface $userTypeRepository, SubjectRepositoryInterface $subjectRepository)
    {
        $this->userRepository = $userRepository;
        $this->universityRepository = $universityRepository;
        $this->majorRepository = $majorRepository;
        $this->departmentRepository = $departmentRepository;
        $this->positionRepository = $positionRepository;
        $this->userTypeRepository = $userTypeRepository;
        $this->subjectRepository = $subjectRepository;
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
        # INITIALIZE VARIABLES START
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
        $userDetails['emergency_contact'] = $request->emergency_contact != null ? $this->setPhoneNumber($request->emergency_contact) : null;

        # generate default password which is 12345678
        $userDetails['password'] = Hash::make('12345678'); # update
        $userDetails['position_id'] = $request->position;

        # generate extended_id
        $last_id = User::where('extended_id', 'like', '%EMPL%')->max('extended_id');
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

        # variables for user educations
        $listGraduatedFrom = $request->graduated_from;
        $listMajor = $request->major;
        $listDegree = $request->degree;
        $listGraduationDate = $request->graduation_date;

        $userEducationDetails = [];
        if ($request->graduated_from[0] != null) {
            $userEducationDetails = [
                'listGraduatedFrom' => $listGraduatedFrom,
                'listMajor' => $listMajor,
                'listDegree' => $listDegree,
                'listGraduationDate' => $listGraduationDate,
            ];
        }
        
        $userSubjectDetails = [];
        if ($request->subject_id[0] != null) {
            $userSubjectDetails = [
                'listSubjectId' => $request->subject_id,
                'listGrade' => $request->grade,
                'listAgreement' => $request->agreement,
                'listFeeIndividual' => $request->fee_individual,
                'listFeeGroup' => $request->fee_group,
                'listAdditionalFee' => $request->additional_fee,
                'listHead' => $request->head,
            ];
        }

        # variables for user roles
        $listRoles = $request->role;
        $tutorSubject = $request->tutor_subject ??= NULL;
        $feeHours = $request->feehours ??= NULL;
        $feeSession = $request->feesession ??= NULL;
        $userRoleDetails = [
            'listRoles' => $listRoles,
            'tutorSubject' => $tutorSubject,
            'feeHours' => $feeHours,
            'feeSession' => $feeSession,
        ];

        # variables for user types
        # user type more like full-time, probation, part-time, etc.
        $listType = $request->type;
        $departmentThatUserWorkedIn = $request->department;
        $startWorking = $request->start_period;
        $stopWorking = $request->end_period;
        $userTypeDetails = [
            'listType' => $listType,
            'departmentThatUserWorkedIn' => $departmentThatUserWorkedIn,
            'startWorking' => $startWorking,
            'stopWorking' => $stopWorking
        ];
        # INITIALIZE VARIABLES END

        DB::beginTransaction();
        try {

            # store new user
            $newUser = $this->userRepository->createUser($userDetails, $userEducationDetails);
            $newUserId = $newUser->id;

            if ($request->graduated_from[0] != null) {
                # store new user education to tbl_user_education
                $this->userRepository->createUserEducation($newUser, $userEducationDetails);
            }

            # store new user role to tbl_user_roles
            $this->userRepository->createUserRole($newUser, $userRoleDetails);

            # store new user type to tbl_user_type
            $this->userRepository->createUserType($newUser, $userTypeDetails);

            if ($request->subject_id[0] != null) {
                # store new user subject to tbl_user_subjects
                $checkUserSubject = $this->userRepository->createOrUpdateUserSubject($newUser, $request, $user_id_with_label);
                
                if($checkUserSubject[0]){
                    return back()->withErrors(["agreement.".$checkUserSubject[1] => "The Agreement field is required"])->withInput();
                }
            }

            # upload curriculum vitae
            $CV_file_path = null;
            if ($request->hasFile('curriculum_vitae')) {
                $CV_file_format = $request->file('curriculum_vitae')->getClientOriginalExtension();
                $CV_file_name = 'CV-' . str_replace(' ', '_', $request->first_name . '_' . $request->last_name);
                $CV_file_path = $request->file('curriculum_vitae')->storeAs('public/uploaded_file/user/' . $user_id_with_label, $CV_file_name . '.' . $CV_file_format);
            }


            # upload KTP / idcard
            $ID_file_path = null;
            if ($request->hasFile('idcard')) {
                $ID_file_format = $request->file('idcard')->getClientOriginalExtension();
                $ID_file_name = 'ID-' . str_replace(' ', '_', $request->first_name . '_' . $request->last_name);
                $ID_file_path = $request->file('idcard')->storeAs('public/uploaded_file/user/' . $user_id_with_label, $ID_file_name . '.' . $ID_file_format);
            }

            # upload tax
            $TX_file_path = null;
            if ($request->hasFile('tax')) {
                $TX_file_format = $request->file('tax')->getClientOriginalExtension();
                $TX_file_name = 'TAX-' . str_replace(' ', '_', $request->first_name . '_' . $request->last_name);
                $TX_file_path = $request->file('tax')->storeAs('public/uploaded_file/user/' . $user_id_with_label, $TX_file_name . '.' . $TX_file_format);
            }

            # upload bpjs kesehatan / health insurance
            $HI_file_path = null;
            if ($request->hasFile('health_insurance')) {
                $HI_file_format = $request->file('health_insurance')->getClientOriginalExtension();
                $HI_file_name = 'HI-' . str_replace(' ', '_', $request->first_name . '_' . $request->last_name);
                $HI_file_path = $request->file('health_insurance')->storeAs('public/uploaded_file/user/' . $user_id_with_label, $HI_file_name . '.' . $HI_file_format);
            }

            # upload bpjs ketenagakerjaan / empl insurance
            $EI_file_path = null;
            if ($request->hasFile('empl_insurance')) {
                $EI_file_format = $request->file('empl_insurance')->getClientOriginalExtension();
                $EI_file_name = 'EI-' . str_replace(' ', '_', $request->first_name . '_' . $request->last_name);
                $EI_file_path = $request->file('empl_insurance')->storeAs('public/uploaded_file/user/' . $user_id_with_label, $EI_file_name . '.' . $EI_file_format);
            }

            # update uploaded data to user table
            if ($request->hasFile('curriculum_vitae') || $request->hasFile('idcard') || $request->hasFile('tax') || $request->hasFile('health_insurance') || $request->hasFile('empl_insurance')) {
                $this->userRepository->updateUser($newUserId, [
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
            Log::error('Store user ' . $request->route('user_role') . ' failed : ' . $e->getMessage());
            return Redirect::back()->withError('Failed to create a new ' . $request->route('user_role'));
        }

        # store Success
        # create log success
        $this->logSuccess('store', 'Form Input', 'User', Auth::user()->first_name . ' '. Auth::user()->last_name, ['user_id' => $user_id_with_label]);

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
        $subjects = $this->subjectRepository->getAllSubjects();

        return view('pages.user.employee.form')->with(
            [
                'universities' => $universities,
                'univ_countries' => $univ_countries,
                'majors' => $majors,
                'departments' => $departments,
                'positions' => $positions,
                'user_types' => $user_types,
                'subjects' => $subjects,
                'is_tutor' => false,
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

        $detail = [
            'status' => $newStatus,
            'deativated_at' => $data['deactivated_at'],
            'new_pic' => $data['new_pic'],
            'department' => $data['department']
        ];


        DB::beginTransaction();
        try {

            # update on users table
            $this->userRepository->updateStatusUser($userId, $detail);
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
        $newDetails['emergency_contact'] = $request->emergency_contact != null ? $this->setPhoneNumber($request->emergency_contact) : null;

        $newDetails['position_id'] = $request->position;

        DB::beginTransaction();
        try {

            # update user
            $newUser = $this->userRepository->updateUser($userId, $newDetails);

            $detailEducations = [];

            if ($request->graduated_from[0] != null) {
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
            }


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
            $user->roles()->sync($roleDetails);

            if ($request->subject_id[0] != null) {
                # update user subject to tbl_user_subjects
                $checkUserSubject = $this->userRepository->createOrUpdateUserSubject($user, $request, $user_id_with_label);
                 
                if($checkUserSubject[0]){
                    return back()->withErrors(["agreement.".$checkUserSubject[1]=> "The Agreement field is required"])->withInput();
                }

            }else{
                if(in_array(4, $request->role) && $user->user_subjects()->count() > 0){
                    $user_role_id = $user->roles()->where('role_name', 'Tutor')->first()->pivot->id;
                    UserSubject::where('user_role_id', $user_role_id)->delete();
                }
            }

            # validate
            # in order to avoid double data
            $newUserType = $request->type;
            $newDepartment = $request->department;
            # wherePivot('department_id, $newDepartment) old
            if ($user->user_type()->wherePivot('user_type_id', $newUserType)->wherePivot('status', 1)->wherePivot('deactivated_at', NULL)->wherePivot('start_date', $request->start_period)->wherePivot('end_date', $request->end_period)->count() == 0) {
                // if ($user->user_type()->wherePivot('user_type_id', 2)->wherePivot('department_id', 3)->wherePivot('start_date', '2022-12-01')->wherePivot('end_date', '2023-01-01')->count() == 0) {

                # deactivate the latest active type
                $activeType = $user->user_type()->where('tbl_user_type_detail.status', 1)->wherePivot('deactivated_at', NULL)->pluck('tbl_user_type_detail.user_type_id')->toArray();
                foreach ($activeType as $key => $value) {
                    $user->user_type()->updateExistingPivot($value, ['status' => 0, 'deactivated_at' => Carbon::now()]);
                }

                # store new user type to tbl_user_type
                $user->user_type()->syncWithoutDetaching([[
                    'user_type_id' => $request->type,
                    'department_id' => $request->department,
                    'start_date' => $request->start_period,
                    'end_date' => $request->type == 1 ? null : $request->end_period,
                ]]);
            } else {
                $user->user_type()->updateExistingPivot($newUserType, ['status' => 1, 'department_id' => $newDepartment, 'deactivated_at' => NULL]);
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
            return Redirect::back()->withError('Failed to update ' . $request->route('user_role') . ' | Line ' . $e->getLine());
        }

        # Update success
        # create log success
        $this->logSuccess('update', 'Form Input', 'User', Auth::user()->first_name . ' '. Auth::user()->last_name, $newDetails, $user);

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
        $salesTeams = $this->userRepository->getAllUsersByDepartmentAndRole('Employee', 'Client Management');
        $subjects = $this->subjectRepository->getAllSubjects();
        $is_tutor = $user->roles()->where('role_name', 'Tutor')->first() != null ? true : false;


        return view('pages.user.employee.form')->with(
            [
                'universities' => $universities,
                'univ_countries' => $univ_countries,
                'majors' => $majors,
                'departments' => $departments,
                'positions' => $positions,
                'user_types' => $user_types,
                'user' => $user,
                'salesTeams' => $salesTeams->whereNotIn('id', [$userId]),
                'subjects' => $subjects,
                'is_tutor' => $is_tutor
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

        # Download success
        # create log success
        $this->logSuccess('download', null, 'User', Auth::user()->first_name . ' '. Auth::user()->last_name, ['user' => $user, 'file' => $request->route('filetype')]);

        return response($file)->header('Content-Type', 'application/pdf');
    }

    public function destroy(Request $request)
    {
        $userId = $request->user;
        $newStatus = 0; # inactive

        $detail = [
            'status' => $newStatus,
            'deativated_at' => Carbon::now(),
            'new_pic' => null,
            'department' => null
        ];

        DB::beginTransaction();
        try {

            $this->userRepository->updateStatusUser($userId, $detail);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Deactive user failed : ' . $e->getMessage());
            return Redirect::back()->withError('Failed to deactive user');
        }

        # Delete success
        # create log success
        $this->logSuccess('delete', null, 'User', Auth::user()->first_name . ' '. Auth::user()->last_name, ['user_id' => $userId]);

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

        # Delete success
        # create log success
        $this->logSuccess('delete', null, 'User Type', Auth::user()->first_name . ' '. Auth::user()->last_name, ['user_id' => $userId, 'user_type_id' => $userTypeId]);

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

    public function getSalesTeam()
    {
        DB::beginTransaction();
        try {

            # update on users table
            $salesTeam = $this->userRepository->getAllUsersByDepartmentAndRole('Employee', 'Client Management');
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('failed to get sales team' . $e->getMessage());
            return response()->json(['message' => 'Failed to get sales team'], 422);
        }

        return response()->json(
            [
                'success' => true,
                'data' => $salesTeam
            ]
        );    
    } 

    public function downloadAgreement(Request $request)
    {
        $userId = $request->route('user');
        $user = $this->userRepository->getUserById($userId);

        $userSubjectId = $request->route('user_subject');
        $userSubject = $this->userRepository->getUserSubjectById($userSubjectId);

        $file = Storage::disk('local')->get($userSubject->agreement);

        # Download success
        # create log success
        $this->logSuccess('download', null, 'User', Auth::user()->first_name . ' '. Auth::user()->last_name, ['user' => $user->first_name . ' ' . $user->last_name]);

        return response($file)->header('Content-Type', 'application/pdf');
    }
}
