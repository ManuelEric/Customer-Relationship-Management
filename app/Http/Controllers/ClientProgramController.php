<?php

namespace App\Http\Controllers;

use App\Actions\ClientEvents\UpdateClientEventAction;
use App\Actions\ClientPrograms\CreateBundleProgramAction;
use App\Actions\ClientPrograms\CreateClientProgramAction;
use App\Actions\ClientPrograms\DeleteClientProgramAction;
use App\Actions\ClientPrograms\UpdateClientProgramAction;
use App\Enum\LogModule;
use App\Http\Requests\StoreClientProgramRequest;
use App\Http\Requests\StoreFormProgramEmbedRequest;
use App\Http\Traits\CheckExistingClient;
use App\Interfaces\ClientEventRepositoryInterface;
use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\CorporateRepositoryInterface;
use App\Interfaces\EdufLeadRepositoryInterface;
use App\Interfaces\EventRepositoryInterface;
use App\Interfaces\LeadRepositoryInterface;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\ReasonRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\LoggingTrait;
use App\Interfaces\ClientLeadTrackingRepositoryInterface;
use App\Interfaces\ClientProgramLogMailRepositoryInterface;
use App\Interfaces\TagRepositoryInterface;
use App\Jobs\Client\ProcessDefineCategory;
use App\Jobs\Client\ProcessInsertLogClient;
use App\Models\Bundling;
use App\Models\BundlingDetail;
use App\Models\Program;
use App\Models\School;
use App\Models\UserClient;
use App\Models\ViewClientProgram;
use App\Services\Log\LogService;
use App\Services\Master\ProgramService;
use App\Services\Program\ClientProgramService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ClientProgramController extends Controller
{
    use CheckExistingClient;
    use LoggingTrait;
    private ClientRepositoryInterface $clientRepository;
    private ProgramRepositoryInterface $programRepository;
    private LeadRepositoryInterface $leadRepository;
    private EventRepositoryInterface $eventRepository;
    private EdufLeadRepositoryInterface $edufLeadRepository;
    private UserRepositoryInterface $userRepository;
    private CorporateRepositoryInterface $corporateRepository;
    private ReasonRepositoryInterface $reasonRepository;
    private ClientProgramRepositoryInterface $clientProgramRepository;
    private ClientEventRepositoryInterface $clientEventRepository;
    private SchoolRepositoryInterface $schoolRepository;
    private TagRepositoryInterface $tagRepository;
    private ClientProgramLogMailRepositoryInterface $clientProgramLogMailRepository;
    private ClientLeadTrackingRepositoryInterface $clientLeadTrackingRepository;
    private ClientProgramService $clientProgramService;
    private ProgramService $programService;
    private $admission_prog_list;
    private $tutoring_prog_list;
    private $satact_prog_list;

    use CreateCustomPrimaryKeyTrait;

    public function __construct(ClientRepositoryInterface $clientRepository, ProgramRepositoryInterface $programRepository, LeadRepositoryInterface $leadRepository, EventRepositoryInterface $eventRepository, EdufLeadRepositoryInterface $edufLeadRepository, UserRepositoryInterface $userRepository, CorporateRepositoryInterface $corporateRepository, ReasonRepositoryInterface $reasonRepository, ClientProgramRepositoryInterface $clientProgramRepository, ClientEventRepositoryInterface $clientEventRepository, SchoolRepositoryInterface $schoolRepository, TagRepositoryInterface $tagRepository, ClientProgramLogMailRepositoryInterface $clientProgramLogMailRepository, ClientLeadTrackingRepositoryInterface $clientLeadTrackingRepository, ClientProgramService $clientProgramService, ProgramService $programService)
    {
        $this->clientRepository = $clientRepository;
        $this->programRepository = $programRepository;
        $this->leadRepository = $leadRepository;
        $this->eventRepository = $eventRepository;
        $this->edufLeadRepository = $edufLeadRepository;
        $this->userRepository = $userRepository;
        $this->corporateRepository = $corporateRepository;
        $this->reasonRepository = $reasonRepository;
        $this->clientProgramRepository = $clientProgramRepository;
        $this->clientEventRepository = $clientEventRepository;
        $this->schoolRepository = $schoolRepository;
        $this->tagRepository = $tagRepository;
        $this->clientProgramLogMailRepository = $clientProgramLogMailRepository;
        $this->clientLeadTrackingRepository = $clientLeadTrackingRepository;
        $this->clientProgramService = $clientProgramService;
        $this->programService = $programService;

        $this->admission_prog_list = Program::admissionProgList()->pluck('prog_id')->toArray();

        $this->tutoring_prog_list = Program::tutoringProgList()->pluck('prog_id')->toArray();

        $this->satact_prog_list = Program::SATACTProgList()->pluck('prog_id')->toArray();
    }

    public function index(Request $request)
    {

        $data_filter = $this->clientProgramService->snSetFilterDataIndex($request);

        if ($request->ajax()) {
            return $this->clientProgramRepository->getAllClientProgramDataTables($data_filter);
        }

        # advanced filter data
        $programs = $this->clientProgramRepository->getAllProgramOnClientProgram();
        $main_programs = $this->clientProgramRepository->getAllMainProgramOnClientProgram();
        $schools = $this->schoolRepository->getAllSchools();
        $mentor_tutors = $this->clientProgramRepository->getAllMentorTutorOnClientProgram();
        $pics = $this->clientProgramRepository->getAllPICOnClientProgram();
        $main_leads = $this->leadRepository->getAllMainLead();
        $main_leads = $this->clientProgramService->snMappingLeads($main_leads, 'main_lead');
        $sub_leads = $this->leadRepository->getAllKOLlead();
        $sub_leads = $this->clientProgramService->snMappingLeads($sub_leads, 'sub_lead');
        $conversion_leads = $main_leads->merge($sub_leads);

        return view('pages.program.client-program.index')->with(
            [
                'programs' => $programs,
                'mainPrograms' => $main_programs,
                'schools' => $schools,
                'conversion_leads' => $conversion_leads,
                'mentor_tutors' => $mentor_tutors,
                'pics' => $pics,
                'request' => $request,
                'status_decrypted' => $data_filter['status'],
                'mentor_tutor_decrypted' => $data_filter['userId'],
                'picUUID_arr' => $data_filter['emplUUID'],
            ]
        );
    }

    public function show(Request $request)
    {
        if ($request->route('student') !== null)
            $student_id = $request->route('student');
        elseif ($request->route('client') !== null)
            $student_id = $request->route('client');
        // $studentId = isset($request->route('student')) ? $request->route('student') : isset($request->route('client')) ? $request->route('client') : null;
        $client_program_id = $request->route('program');

        $student = $this->clientRepository->getClientById($student_id);
        // $viewStudent = $this->clientRepository->getViewClientById($student_id);
        $client_program = $this->clientProgramRepository->getClientProgramById($client_program_id);

        # If status program success && program is mentoring then fetch program bought
        if($client_program->status == 1 && $client_program->program->main_prog_id == 1){
            $program_bought = $this->clientProgramRepository->getProgramBought($client_program_id);
        }

        # programs
        $programs = $this->programService->snGetProgramsB2c();

        # main leads
        $leads = $this->leadRepository->getAllMainLead();
        $client_events = $this->clientEventRepository->getAllClientEventByClientId($student_id);
        $external_edufair = $this->edufLeadRepository->getAllEdufairLead();
        $kols = $this->leadRepository->getAllKOLlead();
        $partners = $this->corporateRepository->getAllCorporate();
        $internal_pic = $this->userRepository->rnGetAllUsersByRole('Employee');

        $tutors = $this->userRepository->rnGetAllUsersByRole('Tutor');
        $mentors = $this->userRepository->rnGetAllUsersByRole('Mentor');

        $reasons = $this->reasonRepository->getReasonByType('Program');

        return view('pages.program.client-program.form')->with(
            [
                'student' => $student,
                'clientProgram' => $client_program,
                'programs' => $programs,
                'leads' => $leads,
                'clientEvents' => $client_events,
                'external_edufair' => $external_edufair,
                'kols' => $kols,
                'partners' => $partners,
                'internalPIC' => $internal_pic,
                'tutors' => $tutors,
                'mentors' => $mentors,
                'reasons' => $reasons,
                'programBought' => $program_bought ?? null
            ]
        );
    }

    public function create(Request $request)
    {
        # identifier from interested program
        $p = $request->get('p') !== NULL ? $request->get('p') : null;

        $student_id = $request->route('student');
        $student = $this->clientRepository->getClientById($student_id);
        $view_student = $this->clientRepository->getViewClientById($student_id);

        # programs
        $programs = $this->programService->snGetProgramsB2c();

        # main leads
        $leads = $this->leadRepository->getAllMainLead();
        $client_events = $this->clientEventRepository->getAllClientEventByClientId($student_id);
        $external_edufair = $this->edufLeadRepository->getAllEdufairLead();
        $kols = $this->leadRepository->getAllKOLlead();
        $partners = $this->corporateRepository->getAllCorporate();
        $internal_pic = $this->userRepository->rnGetAllUsersByDepartmentAndRole('Employee', 'Client Management');

        $tutors = $this->userRepository->rnGetAllUsersByRole('Tutor');
        $mentors = $this->userRepository->rnGetAllUsersByRole('Mentor');

        $reasons = $this->reasonRepository->getReasonByType('Program');

        return view('pages.program.client-program.form')->with(
            [
                'p' => $p,
                'edit' => true,
                'student' => $student,
                'viewStudent' => $view_student,
                'programs' => $programs,
                'leads' => $leads,
                'clientEvents' => $client_events,
                'external_edufair' => $external_edufair,
                'kols' => $kols,
                'partners' => $partners,
                'internalPIC' => $internal_pic,
                'tutors' => $tutors,
                'mentors' => $mentors,
                'reasons' => $reasons,
            ]
        );
    }

    public function store(StoreClientProgramRequest $request, CreateClientProgramAction $createClientProgramAction, LogService $log_service)
    {

        $student_id = $request->route('student');
        $student = $this->clientRepository->getClientById($student_id);
        if ($student->st_statusact != 1)
            return Redirect::back()->withError('The student is no longer active');

        $prog_id = $request->prog_id;

        # initialize
        $client_program_details = $request->only([
            'lead_id',
            'prog_id',
            'clientevent_id',
            'eduf_lead_id',
            'kol_lead_id',
            'partner_id',
            'first_discuss_date',
            'meeting_notes',
            'status',
            'referral_code',
            'empl_id'
        ]);

        $client_program_details = $this->clientProgramService->snSetAttributeLead($client_program_details);
        
        DB::beginTransaction();
        try {

            $client_program = $createClientProgramAction->execute($request, $client_program_details, $student, $this->admission_prog_list, $this->tutoring_prog_list, $this->satact_prog_list);
            
            $file_path = $client_program['file_path'];
            $new_client_program = $client_program['new_client_program'];
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::STORE_CLIENT_PROGRAM, $e->getMessage(), $e->getLine(), $e->getFile(), $client_program_details);

            # if failed storing the data into the database
            # remove the uploaded file from storage
            if ($request->hasFile('agreement')) {

                # setting up the agreement request file
                $file_name = "agreement_".str_replace(' ', '_', trim($student->full_name))."_".$request->prog_id;
                $file_format = $request->file('agreement')->getClientOriginalExtension();
                
                # generate the file path
                $file_path = $file_name.'.'.$file_format;

                if (Storage::disk('s3')->exists('project/crm/agreement/'.$file_path) && $file_path !== null) {
                    Storage::disk('s3')->delete('project/crm/agreement/'.$file_path);
                }
            }

            return Redirect::back()->withError('Failed to store a new program.');
        }

        # store Success
        # create log success
        $log_service->createSuccessLog(LogModule::STORE_CLIENT_PROGRAM, 'New client program has been added', $new_client_program->toArray());

        return Redirect::to('client/student/' . $student_id)->withSuccess('A new program has been submitted for ' . $student->fullname);
    }

    public function edit(Request $request)
    {
        $student_id = $request->route('student');
        $client_program_id = $request->route('program');

        $student = $this->clientRepository->getClientById($student_id);
        $view_student = $this->clientRepository->getViewClientById($student_id);
        $client_program = $this->clientProgramRepository->getClientProgramById($client_program_id);

        # programs
        $programs = $this->programService->snGetProgramsB2c();

        # main leads
        $leads = $this->leadRepository->getAllMainLead();
        $client_events = $this->clientEventRepository->getAllClientEventByClientId($student_id);
        $external_edufair = $this->edufLeadRepository->getAllEdufairLead();
        $kols = $this->leadRepository->getAllKOLlead();
        $partners = $this->corporateRepository->getAllCorporate();
        $internal_pic = $this->userRepository->rnGetAllUsersByDepartmentAndRole('Employee', 'Client Management');

        $tutors = $this->userRepository->rnGetAllUsersByRole('Tutor');
        $mentors = $this->userRepository->rnGetAllUsersByRole('Mentor');

        $reasons = $this->reasonRepository->getReasonByType('Program');
        // $reasons = $this->reasonRepository->getAllReasons();
      
        return view('pages.program.client-program.form')->with(
            [
                'edit' => true,
                'student' => $student,
                'viewStudent' => $view_student,
                'clientProgram' => $client_program,
                'programs' => $programs,
                'leads' => $leads,
                'clientEvents' => $client_events,
                'external_edufair' => $external_edufair,
                'kols' => $kols,
                'partners' => $partners,
                'internalPIC' => $internal_pic,
                'tutors' => $tutors,
                'mentors' => $mentors,
                'reasons' => $reasons
            ]
        );
    }

    public function update(StoreClientProgramRequest $request, UpdateClientProgramAction $updateClientProgramAction, LogService $log_service)
    {
        $client_program_id = $request->route('program');
        $student_id = $request->route('student');
        $student = $this->clientRepository->getClientById($student_id);

        # initialize
        $client_program_details = $request->only([
            'lead_id',
            'prog_id',
            'clientevent_id',
            'eduf_lead_id',
            'kol_lead_id',
            'partner_id',
            'first_discuss_date',
            'meeting_notes',
            'status',
            'empl_id',
            'referral_code',
        ]);

        DB::beginTransaction();
        try {

            $updated_client_program = $updateClientProgramAction->execute($request, $client_program_id, $client_program_details, $student, $this->admission_prog_list, $this->tutoring_prog_list, $this->satact_prog_list);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();

            $log_service->createErrorLog(LogModule::UPDATE_CLIENT_PROGRAM, $e->getMessage(), $e->getLine(), $e->getFile(), $client_program_details);

            return Redirect::back()->withError($e->getMessage());
        }

        # Update success
        # create log success
        $log_service->createSuccessLog(LogModule::UPDATE_CLIENT_PROGRAM, 'Client program has been updated', $updated_client_program->toArray());

        return Redirect::to('client/student/' . $student_id . '/program/' . $client_program_id)->withSuccess('A program has been updated for ' . $student->fullname);
    }

    public function destroy(Request $request, DeleteClientProgramAction $deleteClientProgramAction, LogService $log_service)
    {
        $student_id = $request->route('student');
        $client_program_id = $request->route('program');
        $old_client_program = $this->clientProgramRepository->getClientProgramById($client_program_id);

        DB::beginTransaction();
        try {

            $deleteClientProgramAction->execute($old_client_program, $client_program_id, $student_id);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_CLIENT_PROGRAM, $e->getMessage(), $e->getLine(), $e->getFile(), $old_client_program->toArray());

            return Redirect::to('client/student/' . $student_id . '/program/' . $client_program_id)->withError('Failed to delete client program');
        }
    
        # Delete success
        # create log success
        $log_service->createSuccessLog(LogModule::DELETE_CLIENT_PROGRAM, 'Client program has been deleted', $old_client_program->toArray());

        return Redirect::to('client/student/' . $student_id)->withSuccess('Client program has been deleted');
    }

    public function fnCreateFormEmbed(Request $request)
    {
        $program_name = $request->get('program_name');
        if ($program_name == null)
            abort(404);
        
        if (!$program = $this->programRepository->getProgramByName($program_name))
            abort(404);

        $leads = $this->leadRepository->getLeadForFormEmbedEvent();
        $schools = $this->schoolRepository->getAllSchools();
        $tags = $this->tagRepository->getAllTags();

        return view('form-embed.form-programs')->with(
            [
                'program' => $program,
                'leads' => $leads,
                'schools' => $schools,
                'tags' => $tags,
            ]
        );
    }

    // ! Bisa dicek lagi, kemungkinan sudah tidak pakai
    // public function storeFormEmbed(StoreFormProgramEmbedRequest $request)
    // {
    //     $programId = $request->program;
    //     $program = $this->programRepository->getProgramById($programId);
    //     $leadId = $request->leadsource;
    //     $schoolId = $request->school;
    //     $choosen_role = $request->role;

    //     DB::beginTransaction();
    //     try {

    //         # when sch_id is "add-new" 
    //         // $choosen_school = $request->school;
    //         if (!$this->schoolRepository->getSchoolById($request->school) && $request->school !== NULL) {

    //             $last_id = School::max('sch_id');
    //             $school_id_without_label = $last_id ? $this->remove_primarykey_label($last_id, 4) : '0000';
    //             $school_id_with_label = 'SCH-' . $this->add_digit($school_id_without_label + 1, 4);

    //             $school = [
    //                 'sch_id' => $school_id_with_label,
    //                 'sch_name' => $request->school,
    //             ];

    //             # create a new school
    //             $school = $this->schoolRepository->createSchool($school);
    //             $schoolId = $school->sch_id;
    //         }

    //         $index = 0;
    //         while($index < 2) 
    //         {   
    //             # initialize raw variable
    //             # why newClientDetails[$loop] should be array?
    //             # because to make easier for system to differentiate between parents and students like for example if user registered as a parent 
    //             # then index 0 is for parent data and index 1 is for children data, otherwise 
    //             $newClientDetails[$index] = [
    //                 'name' => $request->fullname[$index],
    //                 'email' => $request->email[$index],
    //                 'phone' => $request->fullnumber[$index]
    //             ];

    //             # check if the client exist in our databases
    //             $existingClient = $this->checkExistingClient($newClientDetails[$index]['phone'], $newClientDetails[$index]['email']);
    //             if (!$existingClient['isExist']) {

    //                 # get firstname & lastname from fullname
    //                 $fullname = explode(' ', $newClientDetails[$index]['name']);
    //                 $fullname_words = count($fullname);

    //                 $firstname = $lastname = null;
    //                 if ($fullname_words > 1) {
    //                     $lastname = $fullname[$fullname_words - 1];
    //                     unset($fullname[$fullname_words - 1]);
    //                     $firstname = implode(" ", $fullname);
    //                 } else {
    //                     $firstname = implode(" ", $fullname);
    //                 }

    //                 # all client basic info (whatever their role is)
    //                 $clientDetails = [
    //                     'first_name' => $firstname,
    //                     'last_name' => $lastname,
    //                     'mail' => $newClientDetails[$index]['email'],
    //                     'phone' => $newClientDetails[$index]['phone'],
    //                     'lead_id' => "LS001", # hardcode for lead website
    //                     'register_by' => $choosen_role
    //                 ];

    //                 switch ($choosen_role) {

    //                     case "parent":
    //                         $role = $index == 0 ? 'parent' : 'student';
    //                         break;

    //                     case "student":
    //                         $role = $index == 1 ? 'parent' : 'student';
    //                         break;


    //                 }

    //                 # additional info that should be stored when role is student and parent
    //                 # because all of the additional info are for the student
    //                 if ($choosen_role == 'parent' && $index == 1) {

    //                     $additionalInfo = [
    //                         'st_grade' => 12 - ($request->graduation_year - date('Y')),
    //                         'graduation_year' => $request->graduation_year,
    //                         'lead' => $request->leadsource,
    //                         'sch_id' => $schoolId != null ? $schoolId : $request->school,
    //                     ];

    //                     $clientDetails = array_merge($clientDetails, $additionalInfo);
                    
    //                 } else if ($choosen_role == 'student' && $index == 0) {

    //                     $additionalInfo = [
    //                         'st_grade' => 12 - ($request->graduation_year - date('Y')),
    //                         'graduation_year' => $request->graduation_year,
    //                         'lead' => $request->leadsource,
    //                         'sch_id' => $schoolId != null ? $schoolId : $request->school,
    //                     ];

    //                     $clientDetails = array_merge($clientDetails, $additionalInfo);

    //                 }

    //                 # stored a new client information
    //                 $newClient[$index] = $this->clientRepository->createClient($role, $clientDetails);
                    
    //             }

    //             $clientArrayIds[$index] = $existingClient['isExist'] ? $existingClient['id'] : $newClient[$index]->id;
    //             $index++;
    //         }

    //         switch ($choosen_role) {

    //             case "parent":
    //                 $parentId = $newClientDetails[0]['id'] = $clientArrayIds[0];
    //                 $childId = $clientArrayIds[1];
    //                 break;

    //             case "student":
    //                 $parentId = $clientArrayIds[1];
    //                 $childId = $newClientDetails[0]['id'] = $clientArrayIds[0];
    //                 break;

    //         }

    //         # store the destination country if registrant either parent or student
    //         $this->clientRepository->createDestinationCountry($childId, $request->destination_country);
            
    //         # attaching parent and student
    //         $this->clientRepository->createManyClientRelation($parentId, $childId);

    //         # initiate variables for client program
    //         $clientProgramDetails = [
    //             'client_id' => $childId,
    //             'prog_id' => $programId,
    //             'lead_id' => $leadId,
    //             'first_discuss_date' => Carbon::now(),
    //             'status' => 0,
    //             'registration_type' => 'FE'
    //         ];
            
    //         # store to client program
    //         if ($storedClientProgram = $this->clientProgramRepository->createClientProgram($clientProgramDetails))
    //         {

    //             # send thanks mail
    //             $this->sendMailThanks($storedClientProgram, $parentId, $childId);
    //         }

    //         # trigger define category client
    //         ProcessDefineCategory::dispatch([$childId])->onQueue('define-category-client');

    //         DB::commit();
        
    //     } catch (Exception $e) {

    //         DB::rollBack();
    //         Log::error('Failed to register client from form program embed | error : '.$e->getMessage().' | Line : '.$e->getLine());
    //         return Redirect::to('form/program?program_name='.$program->prog_program)->withErrors('Something went wrong. Please try again or contact our administrator.');
        
    //     }

    //     # store Success
    //     # create log success
    //     $this->logSuccess('store', 'Form Embed', 'Client Program', 'Guest', $storedClientProgram);

    //     return Redirect::to('form/thanks');
    // }    

    // ! Bisa dicek lagi, kemungkinan sudah tidak pakai
    // public function sendMailThanks($clientProgram, $parentId, $childId, $update = false)
    // {
    //     $subject = 'Your registration is confirmed';
    //     $mail_resources = 'mail-template.thanks-email-program';

    //     $parent = $this->clientRepository->getClientById($parentId);
    //     $children = $this->clientRepository->getClientById($childId);
        
    //     $recipientDetails = [
    //         'name' => $parent->mail != null ? $parent->full_name : $children->full_name,  
    //         'mail' => $parent->mail != null ? $parent->mail : $children->mail,
    //         'children_details' => [
    //             'name' => $children->full_name
    //         ]
    //     ];
        
    //     $program = [
    //         'name' => $clientProgram->program->program_name
    //     ];

    //     try {
    //         Mail::send($mail_resources, ['client' => $recipientDetails, 'program' => $program], function ($message) use ($subject, $recipientDetails) {
    //             $message->to($recipientDetails['mail'], $recipientDetails['name'])
    //                 ->subject($subject);
    //         });
    //         $sent_mail = 1;
            
    //     } catch (Exception $e) {
            
    //         $sent_mail = 0;
    //         Log::error('Failed send email thanks to client that register using form program | error : '.$e->getMessage().' | Line '.$e->getLine());

    //     }

    //     # if update is true 
    //     # meaning that this function being called from scheduler
    //     # that updating the client event log mail, so the system no longer have to create the client event log mail
    //     if ($update === true) {
    //         return true;    
    //     }

    //     $logDetails = [
    //         'clientprog_id' => $clientProgram->clientprog_id,
    //         'sent_status' => $sent_mail
    //     ];

    //     return $this->clientProgramLogMailRepository->createClientProgramLogMail($logDetails);
    // }

    public function addBundleProgram(Request $request, ClientProgramService $clientProgramService, LogService $log_service)
    {
        DB::beginTransaction();

        try {
            $client_program = $client_program_details = [];
            $uuid = (string) Str::uuid();
    
            $set_data_bundle_program_before_create = $clientProgramService->snSetDataBundleProgramBeforeCreate($request, $client_program, $client_program_details, $uuid);
            $client_program = $set_data_bundle_program_before_create['client_program'];
            $client_program_details = $set_data_bundle_program_before_create['client_program_details'];
          
            $rules = [
                '*.clientprog_id' => ['required', 'exists:tbl_client_prog,clientprog_id'],
                '*.status' => ['required', 'in:1'],
                '*.HasInvoice' => function($attribute, $value, $fail) {
                    if((int)$value > 0){
                        $fail('This program already has an invoice');
                    }
                },
                '*.HasBundling' => function($attribute, $value, $fail) {
                    if((int)$value > 0){
                        $fail('This program is already in the bundle package');
                    }
                },

            ];
    
            $validator = Validator::make($client_program, $rules);
    
            # threw error if validation fails
            if ($validator->fails()) {
                Log::warning($validator->errors());
    
                return response()->json([
                    'success' => false,
                    'error' => $validator->errors()
                ]);
            }
    
            $bundle_program = $this->clientProgramRepository->createBundleProgram($uuid, $client_program_details);
    
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            $log_service->createErrorLog(LogModule::STORE_BUNDLE_PROGRAM, $e->getMessage(), $e->getLine(), $e->getFile(), $client_program_details);
            return response()->json([
                'success' => false,
                'error' => 'Something went wrong. Please try again'
            ], 500);
        }
     
        # create log success
        $log_service->createSuccessLog(LogModule::STORE_BUNDLE_PROGRAM, 'New bundle program has been added', $client_program_details);

        return response()->json([
            'success' => true,
            'data' => $bundle_program
        ]);

    }

    public function cancelBundleProgram(Request $request, ClientProgramService $clientProgramService, LogService $log_service){

        DB::beginTransaction();

        try {
            $client_program = [];
            $bundling_id = $request->bundlingId;
    
            $set_data_bundle_program_before_delete = $clientProgramService->snSetDataBundleProgramBeforeDelete($request, $client_program);
            $client_program = $set_data_bundle_program_before_delete['client_program'];
    
            $rules = [
                '*.clientprog_id' => ['required', 'exists:tbl_client_prog,clientprog_id'],
                '*.status' => ['required', 'in:1'],
                '*.HasInvoice' => function($attribute, $value, $fail) {
                    if((int)$value > 0){
                        $fail('This program already has an invoice');
                    }
                },
                '*.HasBundling' => function($attribute, $value, $fail) {
                    if((int)$value == 0){
                        $fail('This is not a bundle program');
                    }
                },
            ];
    
            $validator = Validator::make($client_program, $rules);
    
            # threw error if validation fails
            if ($validator->fails()) {
                Log::warning($validator->errors());
    
                return response()->json([
                    'success' => false,
                    'error' => $validator->errors()
                ]);
            }
    
            $deleted_bundle_program = $this->clientProgramRepository->deleteBundleProgram($bundling_id);
    
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            $log_service->createErrorLog(LogModule::DELETE_BUNDLE_PROGRAM, $e->getMessage(), $e->getLine(), $e->getFile(), $client_program);

            return response()->json([
                'success' => false,
                'error' => 'Something went wrong. Please try again'
            ], 500);
        }
     
        # create log success
        $log_service->createSuccessLog(LogModule::DELETE_BUNDLE_PROGRAM, 'Bundle program has been deleted', $deleted_bundle_program->toArray());

        return response()->json([
            'success' => true,
            'data' => $deleted_bundle_program
        ]);
    }
}
