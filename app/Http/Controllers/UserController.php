<?php

namespace App\Http\Controllers;

use App\Actions\Users\CreateUserAction;
use App\Actions\Users\UpdateUserAction;
use App\Actions\Users\UserDocumentDownloadAction;
use App\Enum\LogModule;
use App\Http\Requests\ChangeUserStatusRequest;
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
    private $role_type_mentors;

    public function __construct(UserRepositoryInterface $userRepository, UniversityRepositoryInterface $universityRepository, MajorRepositoryInterface $majorRepository, DepartmentRepositoryInterface $departmentRepository, PositionRepositoryInterface $positionRepository, UserTypeRepositoryInterface $userTypeRepository, SubjectRepositoryInterface $subjectRepository)
    {
        $this->userRepository = $userRepository;
        $this->universityRepository = $universityRepository;
        $this->majorRepository = $majorRepository;
        $this->departmentRepository = $departmentRepository;
        $this->positionRepository = $positionRepository;
        $this->userTypeRepository = $userTypeRepository;
        $this->subjectRepository = $subjectRepository;

        $this->role_type_mentors = [
            'Competition Project Mentorship',
            'Research Project Mentorship',
            'Passion Project Mentorship',
            'Professional Sharing Session Speaker',
            'Part Time Subject Mentor'
        ];
    }

    public function index(Request $request): mixed
    {
        $role = $request->route('user_role');
        if ($request->ajax())
            return $this->userRepository->rnGetAllUsersByRoleDataTables($role);

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
                'is_external_mentor' => false,
                'is_editor' => false,
                'is_professional' => false,
                'role_type_mentors' => $this->role_type_mentors,
            ]
        );
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
        $user = $this->userRepository->rnGetUserById($userId);

        $universities = $this->universityRepository->getAllUniversities();
        $univ_countries = $this->universityRepository->getCountryNameFromUniversity();
        $majors = $this->majorRepository->getAllMajors();
        $departments = $this->departmentRepository->getAllDepartment();
        $positions = $this->positionRepository->getAllPositions();
        $user_types = $this->userTypeRepository->getAllUserType();
        $salesTeams = $this->userRepository->rnGetAllUsersByDepartmentAndRole('Employee', 'Client Management');
        $subjects = $this->subjectRepository->getAllSubjects();
        $is_tutor = $user->roles()->where('role_name', 'Tutor')->first() != null ? true : false;
        $is_external_mentor = $user->roles()->where('role_name', 'External Mentor')->first() != null ? true : false;
        $is_editor = $user->roles()->where('role_name', 'Editor')->first() != null ? true : false;
        $is_professional = $user->roles()->where('role_name', 'Individual Professional')->first() != null ? true : false;


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
                'is_tutor' => $is_tutor,
                'is_external_mentor' => $is_external_mentor,
                'is_editor' => $is_editor,
                'is_professional' => $is_professional,
                'role_type_mentors' => $this->role_type_mentors,
            ]
        );
    }

    public function destroy(
        Request $request,
        LogService $log_service,
        )
    {
        $user_id = $request->user;
        $new_status = 0; # inactive

        $new_status_detail = [
            'active' => $new_status,
            'deactivated_at' => Carbon::now(),
            'new_pic' => null,
            'department' => null
        ];

        $selected_user = $this->userRepository->rnGetUserById($user_id);

        DB::beginTransaction();
        try {


            $the_user = $this->userRepository->rnUpdateStatusUser($selected_user, $new_status_detail);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_USER, $e->getMessage(), $e->getLine(), $e->getFile(), $new_status_detail);
            return Redirect::back()->withError('Failed to temporarily delete the user');
        }

        # Delete success
        # create log success
        $log_service->createSuccessLog(LogModule::DELETE_USER, 'The user has been temporarily deleted', $the_user->toArray());
        return Redirect::back()->withSuccess('User successfully temporarily deleted');
    }

    
    /**
     * below are functions outside of resources functions
     */

    public function changeStatus(
        ChangeUserStatusRequest $request,
        LogService $log_service)
    {
        $selected_user_id = $request->route('user');
        $selected_user = $this->userRepository->rnGetUserById($selected_user_id);
        $new_status_details = $request->only(['active', 'deactivated_at', 'new_pic', 'department']);

        DB::beginTransaction();
        try {

            # update on users table
            $this->userRepository->rnUpdateStatusUser($selected_user, $new_status_details);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::CHANGE_USER_ACTIVE_STATUS, $e->getMessage(), $e->getLine(), $e->getFile(), $new_status_details);
            return response()->json(['message' => 'Failed to update user active status of ' . $selected_user->full_name], 422);
        }

        $log_service->createSuccessLog(LogModule::CHANGE_USER_ACTIVE_STATUS, 'The user status has been updated', $selected_user->toArray());
        return response()->json(['message' => 'The user active status of ' . ucwords($selected_user->full_name) . ' has been updated']);
    }

    public function download(
        Request $request,
        UserDocumentDownloadAction $document_download_action,
        LogService $log_service,
        )
    {
        $user_id = $request->route('user');
        $file_type = $request->route('filetype');
        try {

            [$file_path, $file_name] = $document_download_action->execute($user_id, $file_type);

        } catch (Exception $e) {

            $log_service->createErrorLog(LogModule::DOWNLOAD_USER_DOCUMENT, $e->getMessage(), $e->getLine(), $e->getFile(), compact('user_id', 'file_type'));
            return response()->json([
                'Cannot download the document.'
            ], 400);

        }

        $log_service->createSuccessLog(LogModule::DOWNLOAD_USER_DOCUMENT, 'The user document has been downloaded', compact('user_id', 'file_type'));
        return Storage::download($file_path, $file_name, [
            'Content-Type' => 'application/pdf'
        ]);
        
    }

    public function destroyUserType(
        Request $request,
        LogService $log_service,
        )
    {
        $user_id = $request->route('user');
        $user_type_id = $request->route('user_type');

        DB::beginTransaction();
        try {

            $deleted_user_type = $this->userRepository->rnDeleteUserType($user_type_id);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_USER_CONTRACT, $e->getMessage(), $e->getLine(), $e->getFile(), compact('user_id', 'user_type_id'));
            return Redirect::back()->withError('Failed to delete user contract');
        }

        $log_service->createSuccessLog(LogModule::DELETE_USER_CONTRACT, 'The user contract has been deleted', $deleted_user_type);
        return Redirect::to('user/' . $request->route('user_role') . '/' . $user_id . '/edit')->withSuccess(ucfirst($request->route('user_role')) . ' has been updated');
    }

    public function setPassword(
        Request $request,
        LogService $log_service
        )
    {
        $user_id = $request->route('user');
        $new_password = ['password' => Hash::make('12345678')];

        DB::beginTransaction();
        try {

            $updated_user = $this->userRepository->rnUpdateUser($user_id, $new_password);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::SET_USER_PASSWORD, $e->getMessage(), $e->getLine(), $e->getFile(), compact('user_id', 'new_password'));
            return response()->json(['message' => 'Failed to set password'], 422);
        }

        $log_service->createSuccessLog(LogModule::SET_USER_PASSWORD, 'The user password has been reset', $updated_user);
        return response()->json(['message' => 'Password has been set'], 200);
    }

    public function getSalesTeam()
    {
        DB::beginTransaction();
        try {

            # update on users table
            $salesTeam = $this->userRepository->rnGetAllUsersByDepartmentAndRole('Employee', 'Client Management');
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
        $user = $this->userRepository->rnGetUserById($userId);

        $userSubjectId = $request->route('user_subject');
        $userSubject = $this->userRepository->rnGetUserSubjectById($userSubjectId);

        $file = Storage::disk('local')->get($userSubject->agreement);

        # Download success
        # create log success
        $this->logSuccess('download', null, 'User', Auth::user()->first_name . ' '. Auth::user()->last_name, ['user' => $user->first_name . ' ' . $user->last_name]);

        return response($file)->header('Content-Type', 'application/pdf');
    }

    public function cnStoreUserAgreement(Request $request, LogService $log_service)
    {
        $user_id = $request->route('user');
        $user = $this->userRepository->rnGetUserById($user_id);

        DB::beginTransaction();
        try {
            $user_agreement = $this->userRepository->rnCreateOrUpdateUserSubject($user, $request);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();

            $log_service->createErrorLog(LogModule::STORE_USER_AGREEMENT, $e->getMessage(), $e->getLine(), $e->getFile(), compact('user'));
            return redirect()->route('user.edit', ['user_role' => $request->route('user_role'), 'user' => $user_id])->withErrors('Failed store user agreement!');
        }

        $log_service->createSuccessLog(LogModule::STORE_USER_AGREEMENT, 'Successfully store/update user agreement', $user_agreement);
        
        return redirect()->route('user.edit', ['user_role' => $request->route('user_role'), 'user' => $user_id])->withSuccess('Successfully store/update user agreement!');
    }
    
    public function cnEditUserAgreement(Request $request)
    {
        $user_id = $request->route('user');
        $subject_id = $request->route('subject');
        $year = $request->route('year');
        $user = $this->userRepository->rnGetUserById($user_id);
        
        $user_subject = $user->user_subjects->where('subject_id', $subject_id)->where('year', $year);

        try {
            if(!$user_subject){
                return response()->json([
                    'success' => false,
                    'message' => 'User subject not found.'
                ]);
            }

        } catch (Exception $e) {
            Log::error('Failed get user subject' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'There are user agreement found.',
            'data' => $user_subject
        ]);
    }

    public function cnDestroyUserAgreement(
        Request $request,
        LogService $log_service,
        )
    {
        $user_id = $request->route('user');
        $subject_id = $request->route('subject');
        $year = $request->route('year');

        DB::beginTransaction();
        try {

            $deleted_user_subject = $this->userRepository->rnDeleteUserAgreementBySubjectIdAndYear($subject_id, $year);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_USER_AGREEMENT, $e->getMessage(), $e->getLine(), $e->getFile(), compact('user_id', 'user_subject_id'));
            return Redirect::back()->withError('Failed to delete user agreement');
        }

        $log_service->createSuccessLog(LogModule::DELETE_USER_AGREEMENT, 'The user agreement has been deleted', $deleted_user_subject->toArray());
        return Redirect::to('user/' . $request->route('user_role') . '/' . $user_id . '/edit')->withSuccess('Successfully deleted the user agreement');
    }

}
