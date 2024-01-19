<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Module\ClientController;
use App\Http\Requests\StoreClientRawTeacherRequest;
use App\Http\Requests\StoreClientTeacherCounselorRequest;
use App\Http\Requests\StoreImportExcelRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\LoggingTrait;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\ClientEventRepositoryInterface;
use App\Interfaces\CurriculumRepositoryInterface;
use App\Interfaces\EdufLeadRepositoryInterface;
use App\Interfaces\EventRepositoryInterface;
use App\Interfaces\LeadRepositoryInterface;
use App\Interfaces\SchoolCurriculumRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Http\Traits\SyncClientTrait;
use App\Imports\TeacherImport;
use App\Models\School;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Maatwebsite\Excel\Facades\Excel;

class ClientTeacherCounselorController extends ClientController
{
    use CreateCustomPrimaryKeyTrait;
    use StandardizePhoneNumberTrait;
    use LoggingTrait;
    use SyncClientTrait;
    protected SchoolRepositoryInterface $schoolRepository;
    protected CurriculumRepositoryInterface $curriculumRepository;
    protected LeadRepositoryInterface $leadRepository;
    protected EventRepositoryInterface $eventRepository;
    protected EdufLeadRepositoryInterface $edufLeadRepository;
    protected ClientRepositoryInterface $clientRepository;
    protected SchoolCurriculumRepositoryInterface $schoolCurriculumRepository;
    protected ClientEventRepositoryInterface $clientEventRepository;

    public function __construct(SchoolRepositoryInterface $schoolRepository, CurriculumRepositoryInterface $curriculumRepository, LeadRepositoryInterface $leadRepository, EventRepositoryInterface $eventRepository, EdufLeadRepositoryInterface $edufLeadRepository, ClientRepositoryInterface $clientRepository, SchoolCurriculumRepositoryInterface $schoolCurriculumRepository, ClientEventRepositoryInterface $clientEventRepository)
    {
        $this->schoolRepository = $schoolRepository;
        $this->curriculumRepository = $curriculumRepository;
        $this->leadRepository = $leadRepository;
        $this->eventRepository = $eventRepository;
        $this->edufLeadRepository = $edufLeadRepository;
        $this->schoolCurriculumRepository = $schoolCurriculumRepository;
        $this->clientRepository = $clientRepository;
        $this->clientEventRepository = $clientEventRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $statusClient = $request->get('st');

            switch ($statusClient) {

                case "inactive":
                    $model = $this->clientRepository->getInactiveTeacher(true);
                    break;

                default:
                    $model = $this->clientRepository->getTeachers(true);

            }

            return $this->clientRepository->getDataTables($model);
        }

        return view('pages.client.teacher.index');
    }

    public function indexRaw(Request $request)
    {
        if ($request->ajax()) {
            $model = $this->clientRepository->getAllRawClientDataTables('teacher/counselor', true, []);
            return $this->clientRepository->getDataTables($model, true);
        }

        return view('pages.client.teacher.raw.index');
    }

    public function create()
    {
        $schools = $this->schoolRepository->getAllSchools();
        $curriculums = $this->curriculumRepository->getAllCurriculums();
        $leads = $this->leadRepository->getAllMainLead();
        $events = $this->eventRepository->getAllEvents();
        $ext_edufair = $this->edufLeadRepository->getAllEdufairLead();
        $kols = $this->leadRepository->getAllKOLlead();
        $listReferral = $this->clientRepository->getAllClients();

        return view('pages.client.teacher.form')->with(
            [
                'schools' => $schools,
                'curriculums' => $curriculums,
                'leads' => $leads,
                'events' => $events,
                'ext_edufair' => $ext_edufair,
                'kols' => $kols,
                'listReferral' => $listReferral
            ]
        );
    }

    public function store(StoreClientTeacherCounselorRequest $request)
    {
        $data = $this->initializeVariablesForStoreAndUpdate('teacher', $request);

        DB::beginTransaction();
        try {

            # case 1
            # create new school
            # when sch_id is "add-new" 
            if (!$data['teacherDetails']['sch_id'] = $this->createSchoolIfAddNew($data['schoolDetails']))
                throw new Exception('Failed to store new school', 1);


            # case 2
            # create new user client as teacher / counselor
            if (!$newTeacher = $this->clientRepository->createClient('Teacher/Counselor', $data['teacherDetails']))
                throw new Exception('Failed to store new teacher / counselor', 2);

            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();

            switch ($e->getCode()) {
                case 1:
                    Log::error('Store school failed from teacher / counselor : ' . $e->getMessage());
                    break;

                case 2:
                    Log::error('Store a new client failed : ' . $e->getMessage());
                    break;
            }

            Log::error('Store a new teacher / counselor failed : ' . $e->getMessage());
            return Redirect::to('client/teacher-counselor/create')->withError($e->getMessage());
        }

        # store Success
        # create log success
        $this->logSuccess('store', 'Form Input', 'Parent', Auth::user()->first_name . ' '. Auth::user()->last_name, $newTeacher);

        return Redirect::to('client/teacher-counselor')->withSuccess('A new teacher / counselor has been registered.');
    }

    public function show(Request $request)
    {
        $teacher_counselorId = $request->route('teacher_counselor');
        $teacher_counselor = $this->clientRepository->getClientById($teacher_counselorId);

        return view('pages.client.teacher.view')->with(
            [
                'teacher_counselor' => $teacher_counselor
            ]
        );
    }

    public function edit(Request $request)
    {
        $teacher_counselorId = $request->route('teacher_counselor');
        $teacher_counselor = $this->clientRepository->getClientById($teacher_counselorId);
        $schools = $this->schoolRepository->getAllSchools();
        $curriculums = $this->curriculumRepository->getAllCurriculums();
        $leads = $this->leadRepository->getAllMainLead();
        $events = $this->eventRepository->getAllEvents();
        $ext_edufair = $this->edufLeadRepository->getAllEdufairLead();
        $kols = $this->leadRepository->getAllKOLlead();
        $listReferral = $this->clientRepository->getAllClients();

        return view('pages.client.teacher.form')->with(
            [
                'teacher_counselor' => $teacher_counselor,
                'schools' => $schools,
                'curriculums' => $curriculums,
                'leads' => $leads,
                'events' => $events,
                'ext_edufair' => $ext_edufair,
                'kols' => $kols,
                'listReferral' => $listReferral
            ]
        );
    }

    public function update(StoreClientTeacherCounselorRequest $request)
    {
        $newTeacherCounselorDetails = $request->only([
            'first_name',
            'last_name',
            'mail',
            'phone',
            'dob',
            'insta',
            'state',
            'city',
            'postal_code',
            'address',
            'sch_id',
            'lead_id',
            'eduf_id',
            'kol_lead_id',
            'event_id',
            'st_levelinterest',
            'referral_code'
        ]);

        $newTeacherCounselorDetails['phone'] = $this->setPhoneNumber($request->phone);

        # set lead_id based on lead_id & kol_lead_id
        # when lead_id is kol
        # then put kol_lead_id to lead_id
        # otherwise
        # when lead_id is not kol 
        # then lead_id is lead_id
        if ($request->lead_id == "kol") {

            unset($newTeacherCounselorDetails['lead_id']);
            $newTeacherCounselorDetails['lead_id'] = $request->kol_lead_id;
        }
        unset($newTeacherCounselorDetails['kol_lead_id']);
        // return $newTeacherCounselorDetails;
        // exit;

        DB::beginTransaction();
        try {

            # set referral code null if lead != referral
            if ($newTeacherCounselorDetails['lead_id'] != 'LS005'){
                $newTeacherCounselorDetails['referral_code'] = null;
            }

            # case 1
            # create new school
            # when sch_id is "add-new" 
            if ($request->sch_id == "add-new") {

                $schoolDetails = $request->only([
                    'sch_name',
                    // 'sch_location',
                    'sch_type',
                    'sch_score',
                ]);

                $last_id = School::max('sch_id');
                $school_id_without_label = $this->remove_primarykey_label($last_id, 4);
                $school_id_with_label = 'SCH-' . $this->add_digit($school_id_without_label + 1, 4);

                if (!$school = $this->schoolRepository->createSchool(['sch_id' => $school_id_with_label] + $schoolDetails))
                    throw new Exception('Failed to store new school', 1);

                # insert school curriculum
                if (!$this->schoolCurriculumRepository->createSchoolCurriculum($school_id_with_label, $request->sch_curriculum))
                    throw new Exception('Failed to store school curriculum', 1);


                # remove field sch_id from student detail if exist
                unset($newTeacherCounselorDetails['sch_id']);

                # create index sch_id to student details
                # filled with a new school id that was inserted before
                $newTeacherCounselorDetails['sch_id'] = $school->sch_id;
            }


            # case 2
            # create new user client as teacher / counselor
            $teacher_counselorId = $request->route('teacher_counselor');
            $oldTeacher = $this->clientRepository->getClientById($teacher_counselorId);
            if (!$this->clientRepository->updateClient($teacher_counselorId, $newTeacherCounselorDetails))
                throw new Exception('Failed to store new teacher / counselor', 2);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();

            switch ($e->getCode()) {
                case 1:
                    Log::error('Update school failed from teacher / counselor : ' . $e->getMessage());
                    break;

                case 2:
                    Log::error('Update a client failed : ' . $e->getMessage());
                    break;
            }

            Log::error('Update a new teacher / counselor failed : ' . $e->getMessage());
            return Redirect::to('client/teacher-counselor/')->withError($e->getMessage());
        }

        # Update success
        # create log success
        $this->logSuccess('update', 'Form Input', 'Parent', Auth::user()->first_name . ' '. Auth::user()->last_name, $newTeacherCounselorDetails, $oldTeacher);

        return Redirect::to('client/teacher-counselor/' . $teacher_counselorId)->withSuccess('A teacher / counselor\'s profile has been updated.');
    }

    public function updateStatus(Request $request)
    {
        $teacherId = $request->route('teacher');
        $newStatus = $request->route('status');

        # validate status
        if (!in_array($newStatus, [0, 1])) {

            return response()->json(
                [
                    'success' => false,
                    'message' => "Status is invalid"
                ]
            );
        }

        DB::beginTransaction();
        try {

            $this->clientRepository->updateActiveStatus($teacherId, $newStatus);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update active status client failed : ' . $e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage()
                ]
            );
        }

        return response()->json(
            [
                'success' => true,
                'message' => "Status has been updated",
            ]
        );
    }

    public function getClientEventByTeacherId(Request $request)
    {
        $teacherId = $request->route('teacher');
        return $this->clientEventRepository->getAllClientEventByClientIdDataTables($teacherId);
    }

    public function import(StoreImportExcelRequest $request)
    {

        $file = $request->file('file');

        // Excel::queueImport(new TeacherImport(Auth::user()->first_name . ' '. Auth::user()->last_name), $file);
        (new TeacherImport($this->clientRepository, Auth::user()))->queue($file)->allOnQueue('imports-teacher');

        // $import = new TeacherImport;
        // $import->import($file);

        return back()->withSuccess('Import teacher start progress');
    }

    public function cleaningData(Request $request)
    {
        $type = $request->route('type');
        $rawClientId = $request->route('rawclient_id');
        $clientId = $request->route('client_id');

        DB::beginTransaction();
        try {

            $schools = $this->schoolRepository->getVerifiedSchools();

            $rawClient = $this->clientRepository->getViewRawClientById($rawClientId);
            if (!isset($rawClient))
                return Redirect::to('client/teacher-counselor/raw')->withError('Data does not exist');

            if ($clientId != null){
                $client = $this->clientRepository->getViewClientById($clientId);
                if (!isset($client))
                    return Redirect::to('client/teacher-counselor/raw')->withError('Data does not exist');
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Fetch data raw client failed : ' . $e->getMessage() . ' ' . $e->getLine());
            return Redirect::to('client/teacher-counselor/raw')->withError('Something went wrong. Please try again or contact the administrator.');
        }

        switch ($type) {
            case 'comparison':
                return view('pages.client.teacher.raw.form-comparison')->with([
                    'rawClient' => $rawClient,
                    'client' => $client,
                    'schools' => $schools,
                ]);
                break;

            case 'new':
                return view('pages.client.teacher.raw.form-new')->with([
                    'rawClient' => $rawClient,
                    'schools' => $schools,
                ]);
                break;
        }
    }

    public function convertData(StoreClientRawTeacherRequest $request)
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
            'sch_id' => $request->schoolFinal,
            'is_verified' => 'Y'
        ];

        DB::beginTransaction();
        try {
            switch ($type) {
                case 'merge':

                    $this->clientRepository->updateClient($clientId, $clientDetails);

                    $rawTeacher = $this->clientRepository->getViewRawClientById($rawclientId);

                    # delete parent from raw client
                    $this->clientRepository->deleteClient($rawclientId);

                    break;

                case 'new':
                    $rawTeacher = $this->clientRepository->getViewRawClientById($rawclientId);
                    $lead_id = $rawTeacher->lead_id;
                    $register_as = $rawTeacher->register_as;

                    $clientDetails['lead_id'] = $lead_id;
                    $clientDetails['register_as'] = $register_as;

                    $newTeacher = $this->clientRepository->updateClient($rawclientId, $clientDetails);

                    break;
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Convert client failed : ' . $e->getMessage() . ' ' . $e->getLine());
            return Redirect::to('client/teacher-counselor/raw')->withError('Something went wrong. Please try again or contact the administrator.');
        }

        return Redirect::to('client/teacher-counselor/'. (isset($clientId) ? $clientId : $rawclientId))->withSuccess('Convert client successfully.');
    }

    public function destroyRaw(Request $request)
    {
        $rawclientId = $request->route('rawclient_id');
        $rawTeacher = $this->clientRepository->getViewRawClientById($rawclientId);

        DB::beginTransaction();
        try {

            if (!isset($rawTeacher))
                return Redirect::to('client/teacher-counselor/raw')->withError('Data does not exist');


            $this->clientRepository->deleteClient($rawclientId);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete raw client teacher failed : ' . $e->getMessage());
            return Redirect::to('client/teacher-counselor/raw')->withError('Failed to delete raw teacher');
        }

        # Delete success
        # create log success
        $this->logSuccess('delete', null, 'Raw Client', Auth::user()->first_name . ' '. Auth::user()->last_name, $rawTeacher);

        return Redirect::to('client/teacher-counselor/raw')->withSuccess('Raw teacher successfully deleted');
    }
}
