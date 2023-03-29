<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientTeacherCounselorRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\ClientEventRepositoryInterface;
use App\Interfaces\CurriculumRepositoryInterface;
use App\Interfaces\EdufLeadRepositoryInterface;
use App\Interfaces\EventRepositoryInterface;
use App\Interfaces\LeadRepositoryInterface;
use App\Interfaces\SchoolCurriculumRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Models\School;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class ClientTeacherCounselorController extends Controller
{
    use CreateCustomPrimaryKeyTrait;
    use StandardizePhoneNumberTrait;
    private SchoolRepositoryInterface $schoolRepository;
    private CurriculumRepositoryInterface $curriculumRepository;
    private LeadRepositoryInterface $leadRepository;
    private EventRepositoryInterface $eventRepository;
    private EdufLeadRepositoryInterface $edufLeadRepository;
    private ClientRepositoryInterface $clientRepository;
    private SchoolCurriculumRepositoryInterface $schoolCurriculumRepository;
    private ClientEventRepositoryInterface $clientEventRepository;

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
        if ($request->ajax())
            return $this->clientRepository->getAllClientByRoleAndStatusDataTables('Teacher/Counselor');


        return view('pages.client.teacher.index');
    }

    public function create()
    {
        $schools = $this->schoolRepository->getAllSchools();
        $curriculums = $this->curriculumRepository->getAllCurriculums();
        $leads = $this->leadRepository->getAllMainLead();
        $events = $this->eventRepository->getAllEvents();
        $ext_edufair = $this->edufLeadRepository->getAllEdufairLead();
        $kols = $this->leadRepository->getAllKOLlead();

        return view('pages.client.teacher.form')->with(
            [
                'schools' => $schools,
                'curriculums' => $curriculums,
                'leads' => $leads,
                'events' => $events,
                'ext_edufair' => $ext_edufair,
                'kols' => $kols
            ]
        );
    }

    public function store(StoreClientTeacherCounselorRequest $request)
    {
        $teacherCounselorDetails = $request->only([
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
        ]);

        $teacherCounselorDetails['phone'] = $this->setPhoneNumber($request->phone);

        # set lead_id based on lead_id & kol_lead_id
        # when lead_id is kol
        # then put kol_lead_id to lead_id
        # otherwise
        # when lead_id is not kol 
        # then lead_id is lead_id
        if ($request->lead_id == "kol") {

            unset($teacherCounselorDetails['lead_id']);
            $teacherCounselorDetails['lead_id'] = $request->kol_lead_id;
        }
        unset($newTeacherCounselorDetails['kol_lead_id']);

        DB::beginTransaction();
        try {

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
                unset($teacherCounselorDetails['sch_id']);

                # create index sch_id to student details
                # filled with a new school id that was inserted before
                $teacherCounselorDetails['sch_id'] = $school->sch_id;
            }


            # case 2
            # create new user client as teacher / counselor
            if (!$this->clientRepository->createClient('Teacher/Counselor', $teacherCounselorDetails))
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

        return Redirect::to('client/teacher-counselor')->withSuccess('A new teacher / counselor has been registered.');
    }

    public function show(Request $request)
    {
        $teacher_counselorId = $request->route('teacher_counselor');
        $teacher_counselor = $this->clientRepository->getClientById($teacher_counselorId);

        if ($request->ajax()) {
            $data['client_events'] = $this->clientEventRepository->getAllClientEventByUserIdDataTables($teacher_counselorId);
            return $data;
        }

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

        return view('pages.client.teacher.form')->with(
            [
                'teacher_counselor' => $teacher_counselor,
                'schools' => $schools,
                'curriculums' => $curriculums,
                'leads' => $leads,
                'events' => $events,
                'ext_edufair' => $ext_edufair,
                'kols' => $kols
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
        ]);

        $teacherCounselorDetails['phone'] = $this->setPhoneNumber($request->phone);

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
}
