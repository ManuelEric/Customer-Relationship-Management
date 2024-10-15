<?php

namespace App\Http\Controllers;

use App\Actions\Users\CreateUserAction;
use App\Actions\Users\UpdateUserAction;
use App\Enum\LogModule;
use App\Http\Requests\StoreUserRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\LoggingTrait;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Http\Traits\UploadFileTrait;
use App\Interfaces\DepartmentRepositoryInterface;
use App\Interfaces\MajorRepositoryInterface;
use App\Interfaces\PositionRepositoryInterface;
use App\Interfaces\SubjectRepositoryInterface;
use App\Interfaces\UniversityRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\UserTypeRepositoryInterface;
use App\Models\pivot\UserSubject;
use App\Services\Log\LogService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class UserController extends Controller
{
    use CreateCustomPrimaryKeyTrait;
    use StandardizePhoneNumberTrait;
    use LoggingTrait;
    use UploadFileTrait;

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

    public function index(Request $request): mixed
    {
        $role = $request->route('user_role');
        try {
            if ($request->ajax()){
               return $this->userRepository->getAllUsersByRoleDataTables($role);
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }

        return view('pages.user.employee.index');
    }

    public function store(
        StoreUserRequest $request,
        CreateUserAction $createUserAction,
        LogService $log_service,
        ): RedirectResponse
    {
        # INITIALIZE VARIABLES START
        $new_user_details = $request->only([
            'first_name',
            'last_name',
            'email',
            'phone',
            'emergency_contact_phone',
            'emergency_contact_relation_name',
            'datebirth',
            'address',
            'hiredate',
            'nik',
            'bank_name',
            'account_name',
            'account_no',
            'npwp',
            'password',
            'position_id'
        ]);

        # variables for user educations
        $new_user_education_details = $request->safe()->only([
            'graduated_from',
            'major',
            'degree',
            'graduation_date',
        ]);
        
        # variables for user roles
        $new_user_role_details = $request->safe()->only([
            'role',
        ]);

        # variables for user contract
        # user type more like full-time, probation, part-time, etc.
        $new_user_type_details = $request->safe()->only([
            'type',
            'department',
            'start_period',
            'end_period'
        ]);
        
        # INITIALIZE VARIABLES END

        DB::beginTransaction();
        try {
            
            $new_user = $createUserAction->execute($request, $new_user_details, $new_user_education_details, $new_user_role_details, $new_user_type_details);            
            DB::commit();
            
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::STORE_USER, $e->getMessage(), $e->getLine(), $e->getFile(), $new_user_details);
            return Redirect::back()->withError('Failed to create a new ' . $request->route('user_role'));
        }

        $log_service->createSuccessLog(LogModule::STORE_USER, 'New user has been added', $new_user->toArray());
        return Redirect::to('user/' . $request->route('user_role'))->withSuccess('New ' . $request->route('user_role') . ' has been created');
    }

    public function create(): View
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

    public function update(
        StoreUserRequest $request,
        UpdateUserAction $updateUserAction,
        LogService $log_service,
        ): RedirectResponse
    {
        $new_user_details = $request->only([
            'first_name',
            'last_name',
            'email',
            'phone',
            'emergency_contact_phone',
            'emergency_contact_relation_name',
            'datebirth',
            'address',
            'hiredate',
            'nik',
            'bank_name',
            'account_name',
            'account_no',
            'npwp',
            'password',
            'position_id'
        ]);

        # variables for user educations
        $new_user_education_details = $request->safe()->only([
            'graduated_from',
            'major',
            'degree',
            'graduation_date',
        ]);

        # variables for user roles
        $new_user_role_details = $request->safe()->only([
            'role',
        ]);

        # variables for user contract
        # user type more like full-time, probation, part-time, etc.
        $new_user_type_details = $request->safe()->only([
            'type',
            'department',
            'start_period',
            'end_period'
        ]);

        DB::beginTransaction();
        try {

            $the_user = $updateUserAction->execute($request, $new_user_details, $new_user_education_details, $new_user_role_details, $new_user_type_details);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::UPDATE_USER, $e->getMessage(), $e->getLine(), $e->getFile(), $new_user_details);
            return Redirect::back()->withError('Failed to update the user ' . $request->route('user_role'));
        }

        $log_service->createSuccessLog(LogModule::UPDATE_USER, 'The user has been updated', $the_user->toArray());
        return Redirect::to('user/' . $request->route('user_role') . '/' . $request->route('user') . '/edit')->withSuccess(ucfirst($request->route('user_role')) . ' has been updated');
    }

    public function edit(Request $request): View
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
