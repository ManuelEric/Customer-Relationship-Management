<?php

namespace App\Http\Controllers;

use App\Enum\LogModule;
use App\Http\Requests\StoreClientParentRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\FindStatusClientTrait;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\CurriculumRepositoryInterface;
use App\Interfaces\EdufLeadRepositoryInterface;
use App\Interfaces\EventRepositoryInterface;
use App\Interfaces\LeadRepositoryInterface;
use App\Interfaces\MajorRepositoryInterface;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\SchoolCurriculumRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Interfaces\TagRepositoryInterface;
use App\Interfaces\UniversityRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Module\ClientController;
use App\Http\Requests\StoreClientRawParentRequest;
use App\Http\Requests\StoreImportExcelRequest;
use App\Http\Traits\LoggingTrait;
use App\Http\Traits\SyncClientTrait;
use App\Imports\ParentImport;
use App\Interfaces\ClientEventRepositoryInterface;
use App\Services\Log\LogService;
use App\Services\Master\ProgramService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ClientParentController extends ClientController
{

    use CreateCustomPrimaryKeyTrait;
    use FindStatusClientTrait;
    use StandardizePhoneNumberTrait;
    use LoggingTrait;
    use SyncClientTrait;

    protected ClientRepositoryInterface $clientRepository;
    protected ClientEventRepositoryInterface $clientEventRepository;
    protected SchoolRepositoryInterface $schoolRepository;
    protected LeadRepositoryInterface $leadRepository;
    protected EventRepositoryInterface $eventRepository;
    protected EdufLeadRepositoryInterface $edufLeadRepository;
    protected ProgramRepositoryInterface $programRepository;
    protected UniversityRepositoryInterface $universityRepository;
    protected MajorRepositoryInterface $majorRepository;
    protected CurriculumRepositoryInterface $curriculumRepository;
    protected TagRepositoryInterface $tagRepository;
    protected SchoolCurriculumRepositoryInterface $schoolCurriculumRepository;
    protected ProgramService $programService;

    public function __construct(ClientRepositoryInterface $clientRepository, SchoolRepositoryInterface $schoolRepository, LeadRepositoryInterface $leadRepository, EventRepositoryInterface $eventRepository, EdufLeadRepositoryInterface $edufLeadRepository, ProgramRepositoryInterface $programRepository, UniversityRepositoryInterface $universityRepository, MajorRepositoryInterface $majorRepository, CurriculumRepositoryInterface $curriculumRepository, TagRepositoryInterface $tagRepository, SchoolCurriculumRepositoryInterface $schoolCurriculumRepository, ClientEventRepositoryInterface $clientEventRepository, ProgramService $programService)
    {
        $this->clientRepository = $clientRepository;
        $this->schoolRepository = $schoolRepository;
        $this->leadRepository = $leadRepository;
        $this->eventRepository = $eventRepository;
        $this->edufLeadRepository = $edufLeadRepository;
        $this->programRepository = $programRepository;
        $this->universityRepository = $universityRepository;
        $this->majorRepository = $majorRepository;
        $this->curriculumRepository = $curriculumRepository;
        $this->tagRepository = $tagRepository;
        $this->schoolCurriculumRepository = $schoolCurriculumRepository;
        $this->clientEventRepository = $clientEventRepository;
        $this->programService = $programService;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $as_datatables = true;
            $status_client = $request->get('st');

            # advanced filter purpose
            $have_siblings = $request->get('have_siblings');

            # array for advanced filter request
            $advanced_filter = [
                'have_siblings' => $have_siblings,
            ];

            switch ($status_client) {

                case "inactive":
                    $model = $this->clientRepository->getInactiveParent($as_datatables);
                    break;

                default:
                    $model = $this->clientRepository->getParents($as_datatables, null, $advanced_filter);
            }

            return $this->clientRepository->getDataTables($model);
        }


        return view('pages.client.parent.index');
    }

    public function indexRaw(Request $request)
    {
        if ($request->ajax()) {
            $model = $this->clientRepository->getAllRawClientDataTables('parent', true, []);
            return $this->clientRepository->getDataTables($model, true);
        }

        return view('pages.client.parent.raw.index');
    }

    public function create(Request $request)
    {
        # ajax
        # to get university by selected country
        if ($request->ajax()) {
            if (isset($request->country) && count($request->country) > 0) {
                $universities = $this->universityRepository->getAllUniversitiesByTag($request->country);
                return response()->json($universities);
            }
        }

        $student = null;
        if ($child_id = $request->get('child'))
            $student = $this->clientRepository->getClientById($child_id);

    
        $deleted_kids = $kids = [];

        $schools = $this->schoolRepository->getAllSchools();
        $curriculums = $this->curriculumRepository->getAllCurriculums();
        $childrens = $this->clientRepository->getAllClientByRole('Student');
        $leads = $this->leadRepository->getAllMainLead();
        $events = $this->eventRepository->getAllEvents();
        $ext_edufair = $this->edufLeadRepository->getAllEdufairLead();
        $kols = $this->leadRepository->getAllKOLlead();
        $programs = $this->programService->snGetProgramsB2c();
        $countries = $this->tagRepository->getAllTags();
        $majors = $this->majorRepository->getAllMajors();

        return view('pages.client.parent.form')->with(
            [
                'deleted_kids' => $deleted_kids,
                'kids' => $kids,
                'schools' => $schools,
                'curriculums' => $curriculums,
                'childrens' => $childrens,
                'leads' => $leads,
                'events' => $events,
                'ext_edufair' => $ext_edufair,
                'kols' => $kols,
                'programs' => $programs,
                'countries' => $countries,
                'majors' => $majors,
                'student' => $student
            ]
        );
    }

    public function store(StoreClientParentRequest $request, LogService $log_service)
    {
        # request->queryChilId is the primary key for client student
        # request->queryClientProgId is the primary key for the client program
        $q_children_id = isset($request->queryChildId) ? "?child=" . $request->queryChildId : null;
        $q_client_prog_id = isset($request->queryClientProgId) ? "&client_prog=" . $request->queryClientProgId : null;

        $query = $q_children_id . $q_client_prog_id;

        $data = $this->initializeVariablesForStoreAndUpdate('parent', $request);

        $childrens = $request->child_id;

        DB::beginTransaction();
        try {

            # case 1
            # create new user client as parent
            if (!$parent = $this->clientRepository->createClient('Parent', $data['parent_details']))
                throw new Exception('Failed to store new parent', 1);

            $new_parent_id = $parent->id;

            # case 2
            # add relation between parent and student
            # if they didn't insert parents which parentId = NULL
            # then assumed that register for student only
            # so no need to create parent children relation
            if ($childrens) {

                // return $this->clientRepository->createClientRelation($parent_id, $childrenId);
                if (!$this->clientRepository->createManyClientRelation($new_parent_id, $childrens))
                    throw new Exception('Failed to store relation between student and parent', 2);
            }

            # case 3
            # create interested program
            # if they didn't insert interested program 
            # then skip this case
            // if (!$this->createInterestedProgram($data['interestPrograms'], $new_parent_id))
            //     throw new Exception('Failed to store interest program', 3);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();

            switch ($e->getCode()) {
                case 1:
                    $log_service->createErrorLog(LogModule::STORE_PARENT, $e->getMessage(), $e->getLine(), $e->getFile(), $data['parent_details']);
                    break;

                case 2:
                    $log_service->createErrorLog(LogModule::STORE_RELATION_FROM_PARENT, $e->getMessage(), $e->getLine(), $e->getFile(), $data['parent_details']);
                    break;

                case 3:
                    $log_service->createErrorLog(LogModule::STORE_DESTINATION_COUNTRY_FROM_PARENT, $e->getMessage(), $e->getLine(), $e->getFile(), $data['parent_details']);
                    break;
            }

            $log_service->createErrorLog(LogModule::STORE_PARENT, $e->getMessage(), $e->getLine(), $e->getFile(), $data['parent_details']);
            return Redirect::to('client/parent/create' . $query)->withError($e->getMessage());
        }

        # store Success
        # create log success
        $log_service->createSuccessLog(LogModule::STORE_PARENT, 'New parent has been added', $data['parent_details']);

        if ($query != NULL) {
            if ($q_children_id != NULL && $q_client_prog_id == NULL)
                $link = "client/student/" . $request->queryChildId . "/program/create/";
            elseif ($q_children_id != NULL && $q_client_prog_id != NULL)
                $link = 'client/student/' . $request->queryChildId . '/program/' . $request->queryClientProgId;

            return Redirect::to($link)->withSuccess("Parent Information has been added.");
        }

        return Redirect::to('client/parent')->withSuccess('A new parent has been registered.');
    }

    public function show(Request $request)
    {
        $parent_id = $request->route('parent');
        if ($request->ajax())
            return $this->clientEventRepository->getAllClientEventByClientIdDataTables($parent_id);

        $parent_id = $request->route('parent');
        $parent = $this->clientRepository->getClientById($parent_id);

        return view('pages.client.parent.view')->with(
            [
                'parent' => $parent,
            ]
        );
    }

    public function edit(Request $request)
    {
        # ajax
        # to get university by selected country
        if ($request->ajax()) {
            $universities = $this->universityRepository->getAllUniversitiesByTag($request->country);
            return response()->json($universities);
        }

        $parent_id = $request->route('parent');
        $parent = $this->clientRepository->getClientById($parent_id);
        $deleted_kids = $parent->childrens()->onlyTrashed()->pluck('tbl_client.id')->toArray();
        $kids = $parent->childrens()->pluck('tbl_client.id')->toArray();

        $schools = $this->schoolRepository->getAllSchools();
        $curriculums = $this->curriculumRepository->getAllCurriculums();
        $childrens = $this->clientRepository->getAllClientByRole('Student');
        $leads = $this->leadRepository->getAllMainLead();
        $events = $this->eventRepository->getAllEvents();
        $ext_edufair = $this->edufLeadRepository->getAllEdufairLead();
        $kols = $this->leadRepository->getAllKOLlead();
        $programs = $this->programService->snGetProgramsB2c();
        $countries = $this->tagRepository->getAllTags();
        $majors = $this->majorRepository->getAllMajors();

        return view('pages.client.parent.form')->with(
            [
                'parent' => $parent,
                'deleted_kids' => $deleted_kids,
                'kids' => $kids,
                'schools' => $schools,
                'curriculums' => $curriculums,
                'childrens' => $childrens,
                'leads' => $leads,
                'events' => $events,
                'ext_edufair' => $ext_edufair,
                'kols' => $kols,
                'programs' => $programs,
                'countries' => $countries,
                'majors' => $majors
            ]
        );
    }

    public function update(StoreClientParentRequest $request, LogService $log_service)
    {
        $data = $this->initializeVariablesForStoreAndUpdate('parent', $request);

        $childrens = $request->child_id;
        $parent_id = $request->route('parent');
        $old_parent = $this->clientRepository->getClientById($parent_id);

        DB::beginTransaction();
        try {

            # set referral code null if lead != referral
            if ($data['parent_details']['lead_id'] != 'LS005'){
                $data['parent_details']['referral_code'] = null;
            }

            # case 1
            # add relation between parent and student
            # if they didn't insert parents which parentId = NULL
            # then assumed that register for student only
            # so no need to create parent children relation
            if ($childrens !== NULL) {

                if (!$this->clientRepository->createManyClientRelation($parent_id, $childrens))
                    throw new Exception('Failed to update relation between student and parent', 1);
            }


            # case 2
            # create interested program
            # if they didn't insert interested program 
            # then skip this case
            // if (!$this->createInterestedProgram($data['interestPrograms'], $parent_id))
            //     throw new Exception('Failed to store interest program', 3);

            # removing the unnecessary information from the parent_details
            unset($data['parent_details']['pr_firstname']);
            unset($data['parent_details']['pr_lastname']);
            unset($data['parent_details']['pr_mail']);
            unset($data['parent_details']['pr_phone']);
            unset($data['parent_details']['pr_dob']);
            unset($data['parent_details']['pr_insta']);

            # removing the kol_lead_id from the parent_details array
            unset($data['parent_details']['kol_lead_id']);

            # case 3
            # update parent's information
            if (!$this->clientRepository->updateClient($parent_id, $data['parent_details']))
                throw new Exception('Failed to update parent', 3);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();

            switch ($e->getCode()) {
                case 1:
                    $log_service->createErrorLog(LogModule::UPDATE_SCHOOL_FROM_PARENT, $e->getMessage(), $e->getLine(), $e->getFile(), $data['parent_details']);
                    break;

                case 2:
                    $log_service->createErrorLog(LogModule::UPDATE_STUDENT_FROM_PARENT, $e->getMessage(), $e->getLine(), $e->getFile(), $data['parent_details']);
                    break;

                case 3:
                    $log_service->createErrorLog(LogModule::UPDATE_PARENT, $e->getMessage(), $e->getLine(), $e->getFile(), $data['parent_details']);
                    break;
            }

            $log_service->createErrorLog(LogModule::UPDATE_PARENT, $e->getMessage(), $e->getLine(), $e->getFile(), $data['parent_details']);
            return Redirect::to('client/parent/' . $parent_id . '/edit')->withError($e->getMessage());
        }

        # Update success
        # create log success
        $log_service->createSuccessLog(LogModule::UPDATE_PARENT, 'Parent has been updated', $data['parent_details']);

        return Redirect::to('client/parent/' . $parent_id)->withSuccess('A parent has been updated.');
    }

    public function updateStatus(Request $request, LogService $log_service)
    {
        $parent_id = $request->route('parent');
        $new_status = $request->route('status');

        # validate status
        if (!in_array($new_status, [0, 1])) {

            return response()->json(
                [
                    'success' => false,
                    'message' => "Status is invalid"
                ]
            );
        }

        DB::beginTransaction();
        try {

            $this->clientRepository->updateActiveStatus($parent_id, $new_status);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::UPDATE_STATUS_PARENT, $e->getMessage(), $e->getLine(), $e->getFile(), ['parent_id'  => $parent_id, 'new_status' => $new_status]);

            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage()
                ]
            );
        }

        # Upload success
        # create log success
        $log_service->createSuccessLog(LogModule::UPDATE_STATUS_PARENT, 'Status parent has been updated', ['parent_id'  => $parent_id, 'new_status' => $new_status]);

        return response()->json(
            [
                'success' => true,
                'message' => "Status has been updated",
            ]
        );
    }

    public function getDataParents(Request $request)
    {
        # use for modal reminder invoice bundle
        # get data parents from child id
        $child_id = $request->get('child_id');

        if($child_id !== null){
            $parents = $this->clientRepository->getDataParentsByChildId($child_id);
        }else{
            $parents = $this->clientRepository->getAllClientByRole('Parent');
        }

        return response()->json(
            [
                'success' => true,
                'data' => $parents
            ]
        );
    }

    public function cleaningData(Request $request, LogService $log_service)
    {
        $type = $request->route('type');
        $raw_client_id = $request->route('rawclient_id');
        $client_id = $request->route('client_id');

        DB::beginTransaction();
        try {

            $raw_client = $this->clientRepository->getViewRawClientById($raw_client_id);
            if (!isset($raw_client))
                return Redirect::to('client/parent/raw')->withError('Data does not exist');

            if ($client_id != null) {
                $client = $this->clientRepository->getViewClientById($client_id);
                if (!isset($client))
                    return Redirect::to('client/parent/raw')->withError('Data does not exist');
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            $log_service->createErrorLog(LogModule::SELECT_RAW_PARENT, $e->getMessage(), $e->getLine(), $e->getFile(), ['type' => $type, 'client_id' => $client_id, 'raw_client_id' => $raw_client_id]);

            return Redirect::to('client/parent/raw')->withError('Something went wrong. Please try again or contact the administrator.');
        }

        switch ($type) {
            case 'comparison':
                return view('pages.client.parent.raw.form-comparison')->with([
                    'rawClient' => $raw_client,
                    'client' => $client
                ]);
                break;

            case 'new':
                return view('pages.client.parent.raw.form-new')->with([
                    'rawClient' => $raw_client,
                ]);
                break;
        }
    }

    public function convertData(StoreClientRawParentRequest $request, LogService $log_service)
    {

        $type = $request->route('type');
        $client_id = $request->route('client_id');
        $raw_client_id = $request->route('rawclient_id');

        $name = $this->explodeName($request->nameFinal);

        $client_details = [
            'first_name' => $name['firstname'],
            'last_name' => isset($name['lastname']) ? $name['lastname'] : null,
            'mail' => $request->emailFinal,
            'phone' => $this->tnSetPhoneNumber($request->phoneFinal),
            'is_verified' => 'Y'
        ];


        DB::beginTransaction();
        try {
            switch ($type) {
                case 'merge':

                    $this->clientRepository->updateClient($client_id, $client_details);

                    $raw_parent = $this->clientRepository->getViewRawClientById($raw_client_id);

                    # delete parent from raw client
                    $this->clientRepository->deleteClient($raw_client_id);

                    break;

                case 'new':
                    $raw_parent = $this->clientRepository->getViewRawClientById($raw_client_id);
                    $lead_id = $raw_parent->lead_id;
                    $register_by = $raw_parent->register_by;

                    $client_details['lead_id'] = $lead_id;
                    $client_details['register_by'] = $register_by;

                    $newParent = $this->clientRepository->updateClient($raw_client_id, $client_details);

                    break;
            }



            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            $log_service->createErrorLog(LogModule::VERIFIED_RAW_PARENT, $e->getMessage(), $e->getLine(), $e->getFile(), $client_details);

            return Redirect::to('client/parent/raw')->withError('Something went wrong. Please try again or contact the administrator.');
        }

        $log_service->createSuccessLog(LogModule::VERIFIED_RAW_PARENT, 'Raw parent has been verified', $client_details);

        return Redirect::to('client/student?st=new-leads')->withSuccess('Convert client successfully.');
    }

    public function destroy(Request $request, LogService $log_service)
    {
        $client_id = $request->route('parent');
        $client = $this->clientRepository->getClientById($client_id);

        DB::beginTransaction();
        try {

            if (!isset($client))
                return Redirect::to('client/parent')->withError('Data does not exist');
    
            $this->clientRepository->deleteClient($client_id);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_PARENT, $e->getMessage(), $e->getLine(), $e->getFile(), $client->toArray());

            return Redirect::to('client/parent')->withError('Failed to delete parent');
        }

        # Delete success
        # create log success
        $log_service->createSuccessLog(LogModule::DELETE_PARENT, 'Parent has been deleted', $client->toArray());

        return Redirect::to('client/student?st=new-leads')->withSuccess('Client student successfully deleted');
    }

    public function destroyRaw(Request $request, LogService $log_service)
    {
        $raw_client_id = $request->route('rawclient_id');
        $raw_parent = $this->clientRepository->getViewRawClientById($raw_client_id);

        DB::beginTransaction();
        try {

            if (!isset($raw_parent))
                return Redirect::to('client/parent/raw')->withError('Data does not exist');

            $this->clientRepository->deleteClient($raw_client_id);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_RAW_PARENT, $e->getMessage(), $e->getLine(), $e->getFile(), $raw_parent->toArray());

            return Redirect::to('client/parent/raw')->withError('Failed to delete raw parent');
        }

        # Delete success
        # create log success
        $log_service->createSuccessLog(LogModule::DELETE_RAW_PARENT, 'Raw parent has been deleted', $raw_parent->toArray());

        return Redirect::to('client/parent/raw')->withSuccess('Raw parent successfully deleted');
    }

    public function disconnectStudent(Request $request, LogService $log_service)
    {
        $student_id = $request->route('student');
        $parent_id = $request->route('parent');

        DB::beginTransaction();
        try {

            $this->clientRepository->removeClientRelation($parent_id, $student_id);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::DISCONNECT_STUDENT, $e->getMessage(), $e->getLine(), $e->getFile(), ['student_id' => $student_id, 'parent_id' => $parent_id]);

            return Redirect::to('client/parent/' . $parent_id)->withError('failed to be diconnect children.');
        }

        # Delete success
        # create log success
        $log_service->createSuccessLog(LogModule::DISCONNECT_STUDENT, 'Successfully disconnect student', ['student_id' => $student_id, 'parent_id' => $parent_id]);

        return Redirect::to('client/parent/' . $parent_id)->withSuccess('Successfully disconnect children.');
    }
}
