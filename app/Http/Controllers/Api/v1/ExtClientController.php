<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Traits\CalculateGradeTrait;
use App\Http\Traits\CheckExistingClient;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\SplitNameTrait;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Interfaces\ClientEventRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Jobs\RawClient\ProcessVerifyClient;
use App\Jobs\RawClient\ProcessVerifyClientParent;
use App\Models\School;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ExtClientController extends Controller
{

    use SplitNameTrait;
    use CheckExistingClient;
    use CalculateGradeTrait;
    use StandardizePhoneNumberTrait;
    use CreateCustomPrimaryKeyTrait;
    private ClientRepositoryInterface $clientRepository;
    private SchoolRepositoryInterface $schoolRepository;
    private ClientEventRepositoryInterface $clientEventRepository;

    public function __construct(ClientRepositoryInterface $clientRepository, SchoolRepositoryInterface $schoolRepository, ClientEventRepositoryInterface $clientEventRepository)
    {
        $this->clientRepository = $clientRepository;
        $this->schoolRepository = $schoolRepository;
        $this->clientEventRepository = $clientEventRepository;
    }

    public function getClientFromAdmissionMentoring()
    {
        $existingMentees = $this->clientRepository->getExistingMenteesAPI();
        
        return response()->json(
            [
                'success' => true,
                'message' => 'Mentee data found.',
                'data' => $existingMentees
            ]
        );

    }

    public function getMentors()
    {
        # get the active mentors
        $existingMentors = $this->clientRepository->getExistingMentorsAPI();
        if ($existingMentors->count() == 0) {
            return response()->json([
                'success' => true,
                'message' => 'No mentor found.'
            ]);
        }

        # map the data that being shown to the user
        $mappedExistingMentors = $existingMentors->map(function ($value) {
            $trimmedFullname = trim($value->full_name);

            return [
                'fullname' => $trimmedFullname,
                'id' => $value->id,
                'extended_id' => $value->extended_id,
                'formatted' => $trimmedFullname.' | '.$value->id
            ];
        });

        return response()->json(
            [
                'success' => true,
                'message' => 'Mentors data found.',
                'data' => $mappedExistingMentors
            ]
        );
    }

    public function getAlumnis()
    {
        $existingAlumnis = $this->clientRepository->getExistingAlumnisAPI();

        return response()->json(
            [
                'success' => true,
                'message' => 'Alumnis data found.',
                'data' => $existingAlumnis
            ]
        );
    }

    public function getClientById(int $id)
    {
        $client = $this->clientRepository->getClientById($id);

        return response()->json(
            [
                'success' => true,
                'message' => 'Client data found.',
                'data' => $client
            ]
        );
    }

    public function store(Request $request)
    {
        # validation
        $rules = [
            'role' => 'required|in:parent,student,teacher/counsellor',
            'user' => 'nullable',
            'fullname' => 'required',
            'mail' => 'required|email',
            'phone' => 'required',
            'secondary_name' => 'required_if:role,parent',
            'secondary_email' => 'nullable|required_if:role,parent|email',
            'secondary_phone' => 'required_if:role,parent',
            'school_id' => 'nullable|required_if:other_school,null|exists:tbl_sch,sch_id',
            'other_school' => 'nullable',
            'graduation_year' => 'nullable|required_if:role,student|gte:'.date('Y'),
            'destination_country' => 'nullable|required_unless:role,teacher/counsellor|required_if:have_child,true|array|exists:tbl_tag,id', # the ids from tbl_tag
            'scholarship' => 'required|in:yes,no',
            'lead_source_id' => 'required|exists:tbl_lead,lead_id',
            'event_id' => 'required|exists:tbl_events,event_id',
            'attend_status' => 'nullable|in:attend',
            'attend_party' => 'nullable',
            'event_type' => 'nullable|in:offline',
            'status' => 'nullable|in:ots,pr',
            'referral' => 'nullable|exists:tbl_client,id',
            'have_child' => 'required|in:true,false'
        ];

        $incomingRequest = $request->only([
            'role', 'user', 'fullname', 'mail', 'phone', 'secondary_name', 'secondary_email', 'secondary_phone', 'school_id', 'other_school', 'graduation_year', 'destination_country', 'scholarship', 'lead_source_id', 'event_id', 'attend_status', 'attend_party', 'event_type', 'status', 'referral', 'have_child'
        ]);

        $messages = [
            'school_id.required_if' => 'The school field is required.',
            'school_id.exists' => 'The school field is not valid.',
            'lead_source_id.required' => 'The lead field is required.',
            'lead_source_id.exists' => 'The lead field is not valid.',
            'event_id.required' => 'The event field is required.'
        ];

        $validator = Validator::make($incomingRequest, $rules, $messages);
        

        # threw error if validation fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()
            ]);
        }


        # after validating incoming request data, then retrieve the incoming request data
        $validated = $request->collect();


        # declaration of default variables that will be used 
        $studentId = null;

        DB::beginTransaction();
        try {


            # separate the incoming request data
            switch ($validated['role']) {
    
                case "student":
                    $client = $this->storeStudent($validated);
                    $clientId = $client->id;

                    # attach interest programs if any
                    //? where is the request

                    # attach destination countries if any
                    $this->attachDestinationCountry($clientId, $validated['destination_country']);

                    break;
    
                case "parent":
                    $parent = $client = $this->storeParent($validated);
                    
                    if ($validated['have_child'] == true) {

                        $validatedStudent = $request->except(['fullname', 'email', 'phone']);
                        $validatedStudent['fullname'] = $validated['secondary_name'];
                        $validatedStudent['mail'] = $validated['secondary_email'];
                        $validatedStudent['phone'] = $validated['secondary_phone'];

                        $student = $this->storeStudent($validatedStudent);
                        $studentId = $student->id;

                        $this->storeRelationship($parent, $student);
                        
                        $this->attachDestinationCountry($studentId, $validated['destination_country']);

                    }

                    break;
    
                case "teacher/counsellor":
                    $client = $this->storeTeacher($validated);
                    break;

                default:
                    abort(404);
    
            }


            # check if registered client has already join the event
            if ($existing = $this->clientEventRepository->getClientEventByClientIdAndEventId($client->id, $validated['event_id'])) {

                return response()->json([
                    'success' => true,
                    'message' => 'They have joined the event.',
                    'code' => 'EXT', # existing / has joined
                    'data' => $existing
                ]);
            }

            # declare variables for client events
            $clientEventDetails = [
                'client_id' => $client->id, # it comes from query to database, so it should be a collection
                'child_id' => $studentId,
                'parent_id' => null,
                'event_id' => $validated['event_id'],
                'lead_id' => $validated['lead_source_id'],
                'registration_type' => isset($validated['status']) ? strtoupper($validated['status']) : 'PR', # default is PR means Pra-Reg
                'number_of_attend' => isset($validated['attend_party']) ? $validated['attend_party'] : 1,
                'notes' => null, # previously, notes filled with VIP & VVIP
                'referral_code' => null,
                'status' => $validated['attend_status'] == 'attend' ? 1 : 0,
                'joined_date' => Carbon::now(),
            ];


            # store client event
            $storedClientEvent = $this->clientEventRepository->createClientEvent($clientEventDetails);

            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Registration Event Failed | ' . $e->getMessage(). ' | '.$e->getFile().' on line '.$e->getLine());
            return response()->json([
                'success' => false,
                'code' => 'ERR',
                'message' => "We encountered an issue completing your registration. Please check for any missing information or errors and try again. If you're still having trouble, feel free to contact our support team for assistance."
            ]);

        }


        Log::notice('New client has been registered (event) with Id: '.$storedClientEvent->clientevent_id);


        try {

            # send an registration success email
            $this->sendEmailRegistrationSuccess($validated);
            Log::notice('Email registration sent sucessfully to '. $incomingRequest['mail']);

        } catch (Exception $e) {

            Log::error('Failed to send email registration to '.$incomingRequest['mail'].' | ' . $e->getMessage());

        }

        

        return response()->json([
            'success' => true,
            'message' => "Welcome aboard! Your registration is complete. Don't forget to check your email for exciting updates and next steps.",
            'code' => 'SCS',
            'data' => [
                'client' => [
                    'name' => $validated['fullname'],
                    'email' => $validated['mail']
                ],
            ]
        ]);

    }

    private function storeStudent($incomingRequest)
    {
        # check if the client exists in crm database
        $existingClient = $this->checkExistingClient($this->setPhoneNumber($incomingRequest['phone']), $incomingRequest['mail']);


        # if the client is exists
        if ($existingClient['isExist']) 
            return $this->clientRepository->getClientById($existingClient['id']);


        # declare some variables
        $splitNames = $this->split($incomingRequest['fullname']);
        $schoolId = $this->getSchoolId($incomingRequest);


        # create a new client > student
        $newClientDetails = [
            'first_name' => $splitNames['first_name'],
            'last_name' => $splitNames['last_name'],
            'mail' => $incomingRequest['mail'],
            'phone' => $this->setPhoneNumber($incomingRequest['phone']),
            'register_as' => $incomingRequest['role'],
            'st_grade' => $this->getGradeByGraduationYear($incomingRequest['graduation_year']),
            'graduation_year' => $incomingRequest['graduation_year'],
            'lead_id' => 'LS001', # lead is hardcoded into website
            'sch_id' => $schoolId
        ];

        $client = $this->clientRepository->createClient('Student', $newClientDetails);
        $clientId = $client->id;

        # trigger to verify student / children
        ProcessVerifyClient::dispatch([$clientId])->onQueue('verifying_client');

        return $client;
    
    }

    private function attachDestinationCountry($clientId, array $destinationCountries)
    {
        if (count($destinationCountries) > 0)
            $this->clientRepository->createDestinationCountry($clientId, $destinationCountries);

    }

    private function storeParent($incomingRequest)
    {
        # check if the client exists in crm database
        $existingClient = $this->checkExistingClient($this->setPhoneNumber($incomingRequest['phone']), $incomingRequest['mail']);

        # if the client is exists
        if ($existingClient['isExist']) 
            return $this->clientRepository->getClientById($existingClient['id']);

        # declare some variables
        $splitNames = $this->split($incomingRequest['fullname']);

        # create a new client > student
        $newClientDetails = [
            'first_name' => $splitNames['first_name'],
            'last_name' => $splitNames['last_name'],
            'mail' => $incomingRequest['mail'],
            'phone' => $this->setPhoneNumber($incomingRequest['phone']),
            'register_as' => $incomingRequest['role'],
            'lead_id' => 'LS001', # lead is hardcoded into website
        ];

        $client = $this->clientRepository->createClient('Parent', $newClientDetails);
        $clientId = $client->id;

        # trigger to verify parent
        ProcessVerifyClientParent::dispatch([$clientId])->onQueue('verifying_client_parent');

        return $client;
    }

    private function storeRelationship($parent, $children)
    {
        $this->clientRepository->createManyClientRelation($parent->id, $children->id);
    }

    private function storeTeacher($incomingRequest)
    {
        # check if the client exists in crm database
        $existingClient = $this->checkExistingClient($this->setPhoneNumber($incomingRequest['phone']), $incomingRequest['mail']);
        
        # if the client is exists
        if ($existingClient['isExist']) 
            return $this->clientRepository->getClientById($existingClient['id']);

        # declare some variables
        $splitNames = $this->split($incomingRequest['fullname']);
        $schoolId = $this->getSchoolId($incomingRequest);


        # create a new client > student
        $newClientDetails = [
            'first_name' => $splitNames['first_name'],
            'last_name' => $splitNames['last_name'],
            'mail' => $incomingRequest['mail'],
            'phone' => $this->setPhoneNumber($incomingRequest['phone']),
            'register_as' => $incomingRequest['role'],
            'sch_id' => $schoolId,
            'lead_id' => 'LS001', # lead is hardcoded into website
        ];

        $client = $this->clientRepository->createClient('Teacher/Counselor', $newClientDetails);
        $clientId = $client->id;

        # trigger to verify teacher
        ProcessVerifyClient::dispatch([$clientId])->onQueue('verifying_client_teacher');

        return $client;

    }

    private function sendEmailRegistrationSuccess($incomingRequest)
    {
        # there are two ways of procedure depends on where the user registered (ots, pra-reg)

        switch ($incomingRequest['status']) 
        {
            case "ots":
                # thanks mail with a ticket and link to access EduApp
                $template = 'mail-template/registration/event/ots-mail-registration';
                break;

            default:
                # thanks mail with a ticket only
                $template = 'mail-template/registration/event/pra-reg-mail-registration';

        }

        $subject = 'Lorem ipsum dolor sit amet';
        $recipient = $incomingRequest['mail'];
        $passingParameter = [
            'name' => $incomingRequest['fullname']
        ];

        Mail::send($template, $passingParameter, function ($message) use ($subject, $recipient) {
            $message->to($recipient)
                ->subject($subject);
        });
    }

    private function getSchoolId($incomingRequest) 
    {
        $schoolId = $incomingRequest['school_id'];


        if ($incomingRequest['school_id'] === NULL) {
            # store a new school or get the existing one

            if (!$schoolExistOnDB = $this->schoolRepository->getSchoolByName($incomingRequest['other_school'])) {
                # if user did write down the school name outside our school collection
                # and the school name does not exist in our database then store it to CRM database
    
                $last_id = School::max('sch_id');
                $school_id_without_label = $last_id ? $this->remove_primarykey_label($last_id, 4) : '0000';
                $school_id_with_label = 'SCH-' . $this->add_digit($school_id_without_label + 1, 4);
    
                $school = [
                    'sch_id' => $school_id_with_label,
                    'sch_name' => $incomingRequest['other_school'],
                ];
    
                # create a new school
                $school = $this->schoolRepository->createSchool($school);
                $schoolId = $school->sch_id;
            } 
    
            # if user did write down the school name outside our school collection
            # but the school name have been stored previously then get the existing school
    
            $schoolId = $schoolExistOnDB->sch_id;
        }

        return $schoolId;
    }

}
