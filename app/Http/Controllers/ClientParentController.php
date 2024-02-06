<?php

namespace App\Http\Controllers;

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
use App\Models\School;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ParentTemplate;
use App\Http\Controllers\Module\ClientController;
use App\Http\Requests\StoreClientRawParentRequest;
use App\Http\Requests\StoreImportExcelRequest;
use App\Http\Traits\LoggingTrait;
use App\Http\Traits\SyncClientTrait;
use App\Imports\MasterParentImport;
use App\Imports\ParentImport;
use App\Interfaces\ClientEventRepositoryInterface;
use App\Jobs\RawClient\ProcessVerifyClient;
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

    public function __construct(ClientRepositoryInterface $clientRepository, SchoolRepositoryInterface $schoolRepository, LeadRepositoryInterface $leadRepository, EventRepositoryInterface $eventRepository, EdufLeadRepositoryInterface $edufLeadRepository, ProgramRepositoryInterface $programRepository, UniversityRepositoryInterface $universityRepository, MajorRepositoryInterface $majorRepository, CurriculumRepositoryInterface $curriculumRepository, TagRepositoryInterface $tagRepository, SchoolCurriculumRepositoryInterface $schoolCurriculumRepository, ClientEventRepositoryInterface $clientEventRepository)
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
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $asDatatables = true;
            $statusClient = $request->get('st');

            switch ($statusClient) {

                case "inactive":
                    $model = $this->clientRepository->getInactiveParent($asDatatables);
                    break;

                default:
                    $model = $this->clientRepository->getParents($asDatatables);
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
        if ($childId = $request->get('child'))
            $student = $this->clientRepository->getClientById($childId);

        $schools = $this->schoolRepository->getAllSchools();
        $curriculums = $this->curriculumRepository->getAllCurriculums();
        $childrens = $this->clientRepository->getAllClientByRole('Student');
        $leads = $this->leadRepository->getAllMainLead();
        $events = $this->eventRepository->getAllEvents();
        $ext_edufair = $this->edufLeadRepository->getAllEdufairLead();
        $kols = $this->leadRepository->getAllKOLlead();
        $programsB2BB2C = $this->programRepository->getAllProgramByType('B2B/B2C');
        $programsB2C = $this->programRepository->getAllProgramByType('B2C');
        $programs = $programsB2BB2C->merge($programsB2C);
        $countries = $this->tagRepository->getAllTags();
        $majors = $this->majorRepository->getAllMajors();

        return view('pages.client.parent.form')->with(
            [
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

    public function store(StoreClientParentRequest $request)
    {
        # request->queryChilId is the primary key for client student
        # request->queryClientProgId is the primary key for the client program
        $qChildrenId = isset($request->queryChildId) ? "?child=" . $request->queryChildId : null;
        $qClientProgId = isset($request->queryClientProgId) ? "&client_prog=" . $request->queryClientProgId : null;

        $query = $qChildrenId . $qClientProgId;

        $data = $this->initializeVariablesForStoreAndUpdate('parent', $request);

        $childrens = $request->child_id;

        DB::beginTransaction();
        try {

            # case 1
            # create new user client as parent
            if (!$parent = $this->clientRepository->createClient('Parent', $data['parentDetails']))
                throw new Exception('Failed to store new parent', 1);

            $newParentId = $parent->id;

            # case 2
            # add relation between parent and student
            # if they didn't insert parents which parentId = NULL
            # then assumed that register for student only
            # so no need to create parent children relation
            if ($childrens) {

                // return $this->clientRepository->createClientRelation($parentId, $childrenId);
                if (!$this->clientRepository->createManyClientRelation($newParentId, $childrens))
                    throw new Exception('Failed to store relation between student and parent', 2);
            }

            # case 3
            # create interested program
            # if they didn't insert interested program 
            # then skip this case
            // if (!$this->createInterestedProgram($data['interestPrograms'], $newParentId))
            //     throw new Exception('Failed to store interest program', 3);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();

            switch ($e->getCode()) {
                case 1:
                    Log::error('Store parent failed : ' . $e->getMessage());
                    break;

                case 2:
                    Log::error('Store relation between student and parent failed : ' . $e->getMessage());
                    break;

                case 3:
                    Log::error('Store destination country failed : ' . $e->getMessage());
                    break;
            }

            Log::error('Store a new parent failed : ' . $e->getMessage());
            return Redirect::to('client/parent/create' . $query)->withError($e->getMessage());
        }

        # store Success
        # create log success
        $this->logSuccess('store', 'Form Input', 'Parent', Auth::user()->first_name . ' ' . Auth::user()->last_name, $parent);

        if ($query != NULL) {
            if ($qChildrenId != NULL && $qClientProgId == NULL)
                $link = "client/student/" . $request->queryChildId . "/program/create/";
            elseif ($qChildrenId != NULL && $qClientProgId != NULL)
                $link = 'client/student/' . $request->queryChildId . '/program/' . $request->queryClientProgId;

            return Redirect::to($link)->withSuccess("Parent Information has been added.");
        }

        return Redirect::to('client/parent')->withSuccess('A new parent has been registered.');
    }

    public function show(Request $request)
    {
        $parentId = $request->route('parent');
        if ($request->ajax())
            return $this->clientEventRepository->getAllClientEventByClientIdDataTables($parentId);

        $parentId = $request->route('parent');
        $parent = $this->clientRepository->getClientById($parentId);

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

        $parentId = $request->route('parent');
        $parent = $this->clientRepository->getClientById($parentId);

        $schools = $this->schoolRepository->getAllSchools();
        $curriculums = $this->curriculumRepository->getAllCurriculums();
        $childrens = $this->clientRepository->getAllClientByRole('Student');
        $leads = $this->leadRepository->getAllMainLead();
        $events = $this->eventRepository->getAllEvents();
        $ext_edufair = $this->edufLeadRepository->getAllEdufairLead();
        $kols = $this->leadRepository->getAllKOLlead();
        $programsB2BB2C = $this->programRepository->getAllProgramByType('B2B/B2C');
        $programsB2C = $this->programRepository->getAllProgramByType('B2C');
        $programs = $programsB2BB2C->merge($programsB2C);
        $countries = $this->tagRepository->getAllTags();
        $majors = $this->majorRepository->getAllMajors();

        return view('pages.client.parent.form')->with(
            [
                'parent' => $parent,
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

    public function update(StoreClientParentRequest $request)
    {
        $data = $this->initializeVariablesForStoreAndUpdate('parent', $request);

        $childrens = $request->child_id;
        $parentId = $request->route('parent');
        $oldParent = $this->clientRepository->getClientById($parentId);

        DB::beginTransaction();
        try {

            # set referral code null if lead != referral
            if ($data['parentDetails']['lead_id'] != 'LS005'){
                $data['parentDetails']['referral_code'] = null;
            }

            # case 1
            # add relation between parent and student
            # if they didn't insert parents which parentId = NULL
            # then assumed that register for student only
            # so no need to create parent children relation
            if ($childrens !== NULL) {

                if (!$this->clientRepository->createManyClientRelation($parentId, $childrens))
                    throw new Exception('Failed to update relation between student and parent', 1);
            }


            # case 2
            # create interested program
            # if they didn't insert interested program 
            # then skip this case
            // if (!$this->createInterestedProgram($data['interestPrograms'], $parentId))
            //     throw new Exception('Failed to store interest program', 3);

            # removing the unnecessary information from the parentDetails
            unset($data['parentDetails']['pr_firstname']);
            unset($data['parentDetails']['pr_lastname']);
            unset($data['parentDetails']['pr_mail']);
            unset($data['parentDetails']['pr_phone']);
            unset($data['parentDetails']['pr_dob']);
            unset($data['parentDetails']['pr_insta']);

            # removing the kol_lead_id from the parentDetails array
            unset($data['parentDetails']['kol_lead_id']);

            # case 3
            # update parent's information
            if (!$this->clientRepository->updateClient($parentId, $data['parentDetails']))
                throw new Exception('Failed to update parent', 3);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();

            switch ($e->getCode()) {
                case 1:
                    Log::error('Update school failed from parent : ' . $e->getMessage());
                    break;

                case 2:
                    Log::error('Update student failed from parent : ' . $e->getMessage());
                    break;

                case 3:
                    Log::error('Update parent failed : ' . $e->getMessage());
                    break;
            }

            Log::error('Update a parent failed : ' . $e->getMessage());
            return Redirect::to('client/parent/' . $parentId . '/edit')->withError($e->getMessage());
        }

        # Update success
        # create log success
        $this->logSuccess('update', 'Form Input', 'Parent', Auth::user()->first_name . ' ' . Auth::user()->last_name, $data['parentDetails'], $oldParent);

        return Redirect::to('client/parent/' . $parentId)->withSuccess('A parent has been updated.');
    }

    public function import(StoreImportExcelRequest $request)
    {
        Cache::put('auth', Auth::user());
        Cache::put('import_id', Carbon::now()->timestamp . '-import-parent');

        $file = $request->file('file');

        // try {
            // Excel::queueImport(new ParentImport(Auth::user()->first_name . ' '. Auth::user()->last_name), $file);
            (new ParentImport())->queue($file)->allOnQueue('imports-parent');

            // $import = new ParentImport();
            // $import->import($file);

        // } catch (Exception $e) {
        //     return back()->withError('Something went wrong while processing the data. Please try again or contact the administrator.');
        // }

        return back()->withSuccess('Import parent start progress');
    }

    public function getDataParents()
    {
        $parents = $this->clientRepository->getAllClientByRole('Parent');
        return response()->json(
            [
                'success' => true,
                'data' => $parents
            ]
        );
    }

    public function cleaningData(Request $request)
    {
        $type = $request->route('type');
        $rawClientId = $request->route('rawclient_id');
        $clientId = $request->route('client_id');

        DB::beginTransaction();
        try {

            $rawClient = $this->clientRepository->getViewRawClientById($rawClientId);
            if (!isset($rawClient))
                return Redirect::to('client/parent/raw')->withError('Data does not exist');

            if ($clientId != null) {
                $client = $this->clientRepository->getViewClientById($clientId);
                if (!isset($client))
                    return Redirect::to('client/parent/raw')->withError('Data does not exist');
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Fetch data raw client failed : ' . $e->getMessage() . ' ' . $e->getLine());
            return Redirect::to('client/parent/raw')->withError('Something went wrong. Please try again or contact the administrator.');
        }

        switch ($type) {
            case 'comparison':
                return view('pages.client.parent.raw.form-comparison')->with([
                    'rawClient' => $rawClient,
                    'client' => $client
                ]);
                break;

            case 'new':
                return view('pages.client.parent.raw.form-new')->with([
                    'rawClient' => $rawClient,
                ]);
                break;
        }
    }

    public function convertData(StoreClientRawParentRequest $request)
    {

        $type = $request->route('type');
        $clientId = $request->route('client_id');
        $rawclientId = $request->route('rawclient_id');

        $name = $this->explodeName($request->nameFinal);

        $clientDetails = [
            'first_name' => $name['firstname'],
            'last_name' => isset($name['lastname']) ? $name['lastname'] : null,
            'mail' => $request->emailFinal,
            'phone' => $this->setPhoneNumber($request->phoneFinal),
            'is_verified' => 'Y'
        ];


        DB::beginTransaction();
        try {
            switch ($type) {
                case 'merge':

                    $this->clientRepository->updateClient($clientId, $clientDetails);

                    $rawParent = $this->clientRepository->getViewRawClientById($rawclientId);

                    # delete parent from raw client
                    $this->clientRepository->deleteClient($rawclientId);

                    break;

                case 'new':
                    $rawParent = $this->clientRepository->getViewRawClientById($rawclientId);
                    $lead_id = $rawParent->lead_id;
                    $register_as = $rawParent->register_as;

                    $clientDetails['lead_id'] = $lead_id;
                    $clientDetails['register_as'] = $register_as;

                    $newParent = $this->clientRepository->updateClient($rawclientId, $clientDetails);

                    break;
            }



            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Convert client failed : ' . $e->getMessage() . ' ' . $e->getLine());
            return Redirect::to('client/parent/raw')->withError('Something went wrong. Please try again or contact the administrator.');
        }

        // return Redirect::to('client/parent/' . (isset($clientId) ? $clientId : $rawclientId))->withSuccess('Convert client successfully.');
        return Redirect::to('client/student?st=new-leads')->withSuccess('Convert client successfully.');
    }

    public function destroyRaw(Request $request)
    {
        $rawclientId = $request->route('rawclient_id');
        $rawParent = $this->clientRepository->getViewRawClientById($rawclientId);

        DB::beginTransaction();
        try {

            if (!isset($rawParent))
                return Redirect::to('client/parent/raw')->withError('Data does not exist');

            $this->clientRepository->deleteClient($rawclientId);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete raw client parent failed : ' . $e->getMessage());
            return Redirect::to('client/parent/raw')->withError('Failed to delete raw parent');
        }

        # Delete success
        # create log success
        $this->logSuccess('delete', null, 'Raw Client', Auth::user()->first_name . ' ' . Auth::user()->last_name, $rawParent);

        return Redirect::to('client/parent/raw')->withSuccess('Raw parent successfully deleted');
    }

    public function disconnectStudent(Request $request)
    {
        $studentId = $request->route('student');
        $parentId = $request->route('parent');

        DB::beginTransaction();
        try {

            $this->clientRepository->removeClientRelation($parentId, $studentId);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Disconnect children failed : ' . $e->getMessage() . ' ' . $e->getLine());
            return Redirect::to('client/parent/' . $parentId)->withError('failed to be diconnect children.');
        }

        # Delete success
        # create log success
        $this->logSuccess('delete', null, 'relation children', Auth::user()->first_name . ' ' . Auth::user()->last_name, ['client_id' => $studentId]);

        return Redirect::to('client/parent/' . $parentId)->withSuccess('Successfully disconnect children.');
    }
}
