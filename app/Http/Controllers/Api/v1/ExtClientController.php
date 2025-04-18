<?php

namespace App\Http\Controllers\Api\v1;

use App\Enum\LogModule;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\UpdateMenteeGDriveRequest;
use App\Http\Requests\Client\Registration\Public\PublicRegistrationRequest;
use App\Http\Traits\CalculateGradeTrait;
use App\Http\Traits\CheckExistingClient;
use App\Http\Traits\ClientMentorTrait;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\LoggingTrait;
use App\Http\Traits\SplitLeadEdufairTrait;
use App\Http\Traits\SplitNameTrait;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Http\Traits\TranslateProgramStatusTrait;
use App\Interfaces\ClientEventLogMailRepositoryInterface;
use App\Interfaces\ClientEventRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\EventRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Jobs\Client\ProcessInsertLogClient;
use App\Models\ClientEvent;
use App\Models\Event;
use App\Models\Phase;
use App\Models\School;
use App\Models\UserClient;
use App\Repositories\ProgramRepository;
use App\Services\Log\LogService;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ExtClientController extends Controller
{

    use SplitNameTrait;
    use CheckExistingClient;
    use CalculateGradeTrait;
    use StandardizePhoneNumberTrait;
    use CreateCustomPrimaryKeyTrait;
    use LoggingTrait;
    use SplitLeadEdufairTrait;
    use ClientMentorTrait;
    use TranslateProgramStatusTrait;

    private ClientRepositoryInterface $clientRepository;
    private SchoolRepositoryInterface $schoolRepository;
    private ClientEventRepositoryInterface $clientEventRepository;
    private EventRepositoryInterface $eventRepository;
    private ClientEventLogMailRepositoryInterface $clientEventLogMailRepository;
    private ProgramRepository $programRepository;

    public function __construct(ClientRepositoryInterface $clientRepository, SchoolRepositoryInterface $schoolRepository, ClientEventRepositoryInterface $clientEventRepository, EventRepositoryInterface $eventRepository, ClientEventLogMailRepositoryInterface $clientEventLogMailRepository, ProgramRepository $programRepository)
    {
        $this->clientRepository = $clientRepository;
        $this->schoolRepository = $schoolRepository;
        $this->clientEventRepository = $clientEventRepository;
        $this->eventRepository = $eventRepository;
        $this->clientEventLogMailRepository = $clientEventLogMailRepository;
        $this->programRepository = $programRepository;
    }

    public function getParentMentee()
    {
        $existingMentees = $this->clientRepository->getExistingMentees(false, null, []);

        return response()->json(
            [
                'success' => true,
                'message' => 'Parent Mentee data found.',
                'data' => $existingMentees
            ]
        );
    }

    public function getAlumniMentees()
    {
        $alumniMentees = $this->clientRepository->getAlumniMentees(false, false, null);

        return response()->json(
            [
                'success' => true,
                'message' => 'Alumni mentees data found.',
                'data' => $alumniMentees
            ]
        );
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
                /* essay editing purposes */
                'first_name' => $value->first_name,
                'last_name' => $value->last_name,
                'phone' => $value->phone,
                'email' => $value->email,
                'address' => $value->address,
                'roles' => $value->roles,
                'educations' => $value->educations,
                /* end */

                'fullname' => $trimmedFullname,
                'id' => $value->id,
                'extended_id' => $value->extended_id,
                'formatted' => $trimmedFullname . ' | ' . $value->id,
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

    public function getClientById(string $id)
    {
        echo 'a';exit;
        $client = $this->clientRepository->getClientById($id);

        return response()->json(
            [
                'success' => true,
                'message' => 'Client data found.',
                'data' => $client
            ]
        );
    }

    public function store_express(Request $request)
    {

        $main_client = $request->main_client;

        # Second Client digunakan untuk menampung id anak jika ada parent
        # jika tidak ada parent maka second id nya null
        $second_client = $request->second_client ?? null;

        $event_id = $request->EVT;
        $notes = $request->notes;

        $is_site = $request->is_site ?? null;
        $is_confirm = $request->is_confirm ?? null;

        $urlRegistration = 'https://registration.edu-all.com';

        $logDetails = [
            'main_client' => $main_client,
            'second_client' => $second_client,
            'notes' => $notes
        ];

        if (!$event = $this->eventRepository->getEventById($event_id)) {
            Log::warning("Register express: Event not found!", $logDetails);
            return Redirect::to($urlRegistration . '/error/404');
        }

        // if (Carbon::now() < $event->event_startdate){
        //     Log::warning("Register express: Event not started!", $logDetails);
        //     return Redirect::to($urlRegistration . '/error/not-started');
        // }

        // if ($is_site == null || $is_site == false){
        //     Log::warning("Register express: Access denied!", $logDetails);
        //     return Redirect::to($urlRegistration . '/error/access-denied');
        // }

        // if (Carbon::now() == $event->event_startdate && ($is_site == null || !$is_site)){
        //     Log::warning("Register express: Access denied!", $logDetails);
        //     return Redirect::to($urlRegistration . '/error/access-denied');
        // }

        if (!$client = $this->clientRepository->getClientById($main_client)) {
            Log::warning("Register express: Main client not register!", $logDetails);
            return Redirect::to($urlRegistration . '/error/not-register');
        }

        $allowable_role = ['parent', 'student'];
        if (!$client->roles()->whereIn('role_name', $allowable_role)->exists()) {
            # Role main client is not parent or student
            Log::warning("Register express: Client not parent or student!", $logDetails);
            return Redirect::to($urlRegistration . '/error/not-vip');
        }

        $student_id = $second_client != null ? $second_client : $main_client;
        if (!$this->clientRepository->checkIfClientIsMentee($student_id)) {
            # Client has not mentee
            Log::warning("Register express: Client is not mentee!", $logDetails);
            return Redirect::to($urlRegistration . '/error/not-vip');
        }

        switch ($notes) {
            case 'VIP':
            case 'WxSFs0LGh': # Mean VIP
                $notes = 'VIP';
                break;

            default:
                Log::warning("Register express: not vip!", $logDetails);
                return Redirect::to($urlRegistration . '/error/not-vip');
                break;
        }

        DB::beginTransaction();
        try {

            # check if registered client has already join the event
            if ($existing = $this->clientEventRepository->getClientEventByMultipleIdAndEventId($main_client, $event_id, $second_client)) {

                $dataMail = [
                    'fullname' => $existing->client->full_name,
                    'mail' => $existing->client->mail
                ];

                if ($second_client != null) {

                    $dataResponseClient['parent'] = [
                        'name' => $existing->client->full_name,
                        'first_name' => $existing->client->first_name,
                        'last_name' => $existing->client->last_name,
                        'mail' => $existing->client->mail,
                        'phone' => $existing->client->phone,
                    ];

                    $dataResponseClient['student'] = [
                        'name' => $existing->children->full_name,
                        'first_name' => $existing->children->first_name,
                        'last_name' => $existing->children->last_name,
                        'mail' => $existing->children->mail,
                        'phone' => $existing->children->phone,
                    ];

                    $destinationCountries = $existing->children->destinationCountries;
                } else {

                    $dataResponseClient['student'] = [
                        'name' => $existing->client->full_name,
                        'first_name' => $existing->client->first_name,
                        'last_name' => $existing->client->last_name,
                        'mail' => $existing->client->mail,
                        'phone' => $existing->client->phone,
                    ];

                    $destinationCountries = $existing->client->destinationCountries;
                }

                if (count($destinationCountries) > 0) {
                    foreach ($destinationCountries as $key => $country) {
                        $dataDestinationCountries[$key] = [

                            'country_id' => $country->id,
                            'country_name' => $country->name
                        ];
                    }
                    $dataResponseClient['dreams_countries'] = $dataDestinationCountries;
                    unset($dataDestinationCountries);
                } else {
                    $dataResponseClient['dreams_countries'] = [];
                }

                if ($second_client != null) {
                    $dataResponseClient['education'] = [
                        'school_id' => $existing->children->sch_id,
                        'school_name' => isset($existing->children->school->sch_name) ? $existing->children->school->sch_name : null,
                        'graduation_year' => $existing->children->graduation_year,
                        'grade' => $existing->children->st_grade,
                    ];
                } else {
                    $dataResponseClient['education'] = [
                        'school_id' => $existing->client->sch_id,
                        'school_name' => isset($existing->client->school->sch_name) ? $existing->client->school->sch_name : null,
                        'graduation_year' => $existing->client->graduation_year,
                        'grade' => $existing->client->st_grade,
                    ];
                }


                if ($is_site == null || $is_site == false) {
                    return Redirect::to($urlRegistration . '/thanks/event/vip');
                }


                return response()->json([
                    'success' => true,
                    'message' => 'Client event was found.',
                    'data' => $dataResponseClient +
                        [
                            'role' => $second_client != null ? 'parent' : 'student',
                            'is_vip' => true,
                            'lead' => [
                                'lead_id' => $existing->lead_id,
                                'lead_name' => $existing->lead->main_lead,
                            ],
                            'joined_event' => [
                                'clientevent_id' => $existing->clientevent_id,
                                'event_id' => $existing->event->event_id,
                                'event_name' => $existing->event->event_title,
                                'attend_status' => $existing->status,
                                'attend_party' => $existing->number_of_attend,
                                'event_type' => 'offline',
                                'status' => $existing->registration_type,
                                'referral' => $existing->referral_code,
                                'client_type' => $existing->notes,
                            ],


                        ]
                ]);
            }

            if ($is_site == null || $is_site == false) {
                if ($is_confirm == null || $is_confirm == false) {
                    $linkRegist = route('register-express-event', ['main_client' => $main_client, 'notes' => 'WxSFs0LGh', 'second_client' => $second_client, 'EVT' => 'EVT-0014']);
                    return Redirect::to($urlRegistration . '/confirmation/VIP?url=' . $linkRegist);
                }
            }

            $clientEventDetails = [
                'ticket_id' => $this->generateTicketID(),
                'client_id' => $main_client, # it comes from query to database, so it should be a collection
                'child_id' => $second_client,
                'parent_id' => null,
                'event_id' => $event_id,
                'lead_id' => 'LS040',
                'registration_type' => Carbon::now() < $event->event_startdate ? 'PR' : 'OTS',
                'notes' => $notes, # previously, notes filled with VIP & VVIP
                'status' => 0,
                'joined_date' => Carbon::now(),
            ];

            # store client event
            $storedClientEvent = $this->clientEventRepository->createClientEvent($clientEventDetails);

            $dataMail = [
                'fullname' => $storedClientEvent->client->full_name,
                'mail' => $storedClientEvent->client->mail
            ];

            $dataMail['registration_type'] = 'ots';
            $dataMail['notes'] = $notes;

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Registration Event Failed | ' . $e->getMessage() . ' | ' . $e->getFile() . ' on line ' . $e->getLine());
            return Redirect::to($urlRegistration . '/error/registration-failed');
        }

        # create log success
        $this->logSuccess('store', 'Form Embed', 'Client Event Register Express', 'Guest', $clientEventDetails);


        if ($second_client != null) {

            $dataResponseClient = [
                'parent' => [
                    'name' => $storedClientEvent->client->full_name,
                    'first_name' => $storedClientEvent->client->first_name,
                    'last_name' => $storedClientEvent->client->last_name,
                    'mail' => $storedClientEvent->client->mail,
                    'phone' => $storedClientEvent->client->phone,
                ],
                'student' => [
                    'name' => $storedClientEvent->children->full_name,
                    'first_name' => $storedClientEvent->children->first_name,
                    'last_name' => $storedClientEvent->children->last_name,
                    'mail' => $storedClientEvent->children->mail,
                    'phone' => $storedClientEvent->children->phone,
                ],
            ];

            $destinationCountries = $storedClientEvent->children->destinationCountries;
        } else {

            $dataResponseClient = [
                'student' => [
                    'name' => $storedClientEvent->client->full_name,
                    'first_name' => $storedClientEvent->client->first_name,
                    'last_name' => $storedClientEvent->client->last_name,
                    'mail' => $storedClientEvent->client->mail,
                    'phone' => $storedClientEvent->client->phone,
                ],
            ];

            $destinationCountries = $storedClientEvent->client->destinationCountries;
        }

        if (count($destinationCountries) > 0) {
            foreach ($destinationCountries as $key => $country) {
                $dataDestinationCountries[$key] = [

                    'country_id' => $country->id,
                    'country_name' => $country->name
                ];
            }
            $dataResponseClient['dreams_countries'] = $dataDestinationCountries;
            unset($dataDestinationCountries);
        } else {
            $dataResponseClient['dreams_countries'] = [];
        }

        if ($second_client != null) {
            $dataResponseClient['education'] = [
                'school_id' => $storedClientEvent->children->sch_id,
                'school_name' => isset($storedClientEvent->children->school->sch_name) ? $storedClientEvent->children->school->sch_name : null,
                'graduation_year' => $storedClientEvent->children->graduation_year,
                'grade' => $storedClientEvent->children->st_grade,
            ];
        } else {
            $dataResponseClient['education'] = [
                'school_id' => $storedClientEvent->client->sch_id,
                'school_name' => isset($storedClientEvent->client->school->sch_name) ? $storedClientEvent->client->school->sch_name : null,
                'graduation_year' => $storedClientEvent->client->graduation_year,
                'grade' => $storedClientEvent->client->st_grade,
            ];
        }

        if ($is_site == null || $is_site == false) {
            if (isset($is_confirm) && $is_confirm == true) {
                return Redirect::to($urlRegistration . '/thanks/event/vip');
            }
        }


        return response()->json([
            'success' => true,
            'message' => "Welcome aboard! Your registration is complete. Don't forget to check your email for exciting updates and next steps.",
            'data' => $dataResponseClient +
                [
                    'role' => $second_client != null ? 'parent' : 'student',
                    'is_vip' => true,
                    'lead' => [
                        'lead_id' => 'LS040',
                        'lead_name' => 'Invited Mentee'
                    ],
                    'joined_event' => [
                        'clientevent_id' => $storedClientEvent->clientevent_id,
                        'event_id' => $storedClientEvent->event->event_id,
                        'event_name' => $storedClientEvent->event->event_title,
                        'attend_status' => $storedClientEvent->status,
                        'attend_party' => 1,
                        'event_type' => 'offline',
                        'status' => $storedClientEvent->registration_type,
                        'referral' => $storedClientEvent->referral_code,
                        'client_type' => $storedClientEvent->notes,
                    ],
                ]
        ]);
    }

    public function store(Request $request)
    {
        # split lead id and eduf id when lead source is edufair
        $explodeLeadId = explode('-', $request['lead_source_id']);
        if ($explodeLeadId[0] == 'LS017') {
            $splitLeadEdufair = $this->splitLeadEdufair($request['lead_source_id']);
            $request['lead_source_id'] = $splitLeadEdufair['lead_id'];
            $request['eduf_id'] = $splitLeadEdufair['eduf_id'];
        }

        # validation
        $rules = [
            'role' => 'required|in:parent,student,teacher/counsellor',
            'user' => 'nullable',
            'fullname' => 'required',
            'mail' => 'required|email',
            'phone' => 'required|different:secondary_phone',
            'secondary_name' => 'required_if:have_child,true',
            'secondary_email' => 'nullable|email',
            'secondary_phone' => 'nullable|different:phone',
            'school_id' => [
                'nullable',
                $request->school_id != 'new' ? 'exists:tbl_sch,sch_id' : null
            ],
            'other_school' => 'nullable',
            # not validated gte because there are chances that registered user has already graduated like since 2020
            'graduation_year' => [
                'nullable',
                $request->role == 'student' ? 'required' : null,
                $request->role == 'parent' ? 'required_if_accepted:have_child' : null,
            ],
            'destination_country' => [
                'array',
                $request->role == 'student' ? 'required' : 'required_if_accepted:have_child',
            ],
            'destination_country.*' => 'exists:tbl_country,id',
            'scholarship' => 'required|in:Y,N',
            'lead_source_id' => 'required|exists:tbl_lead,lead_id',
            'eduf_id' => 'required_if:lead_source_id,LS017|exists:tbl_eduf_lead,id',
            'event_id' => 'required|exists:tbl_events,event_id',
            # status
            'attend_status' => 'nullable|in:attend',
            # number of attend
            'attend_party' => 'nullable',
            'event_type' => 'nullable|in:offline',
            # registration_type
            'status' => 'required|in:OTS,PR',
            # referral code
            'referral' => 'nullable|exists:tbl_client,id',
            # notes
            'client_type' => 'nullable|in:vip',
            'have_child' => 'required|boolean'
        ];



        $incomingRequest = $request->only([
            'role',
            'user',
            'fullname',
            'mail',
            'phone',
            'secondary_name',
            'secondary_email',
            'secondary_phone',
            'school_id',
            'other_school',
            'graduation_year',
            'destination_country',
            'scholarship',
            'lead_source_id',
            'eduf_id',
            'event_id',
            'attend_status',
            'attend_party',
            'event_type',
            'status',
            'referral',
            'have_child'
        ]);

        $messages = [
            'school_id.required_if' => 'The school field is required.',
            'lead_source_id.required_if' => 'The eduf lead field is required.',
            'school_id.exists' => 'The school field is not valid.',
            'lead_source_id.required' => 'The lead field is required.',
            'lead_source_id.exists' => 'The lead field is not valid.',
            'event_id.required' => 'The event field is required.',
            'destination_country.*.exists' => 'The destination country must be one of the following values.'
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

        # modify the variables inside request array
        $validated = $validated->merge([
            'status' => $validated['attend_status'] == "attend" ? 1 : 0,
            'number_of_attend' => $validated['attend_party'] ?? 1,
            'registration_type' => strtoupper($validated['status']) ?? "PR",
            'referral_code' => $validated['referral'] ?? null,
            'notes' => $validated['client_type'] ?? null
        ]);


        # declaration of default variables that will be used 
        $studentId = null;

        DB::beginTransaction();
        try {


            # separate the incoming request data
            switch ($validated['role']) {

                case "student":
                    $client = $this->storeStudent($validated);
                    $clientId = $client->id;

                    # attach interest programs
                    # get the value of interest programs from event category
                    $joinedEvent = Event::whereEventId($validated['event_id']);
                    if ($eventCategory = $joinedEvent->category) {
                        # keep insert the interest program eventhough she/he has already had the program as interested program before
                        $this->reAttachInterestPrograms($clientId, $eventCategory);
                    }

                    # attach destination countries if any
                    $this->attachDestinationCountry($clientId, $validated['destination_country']);

                    $result = $this->checkClientIsExistsOnClientEvent($client, $validated);
                    if (gettype($result) != "boolean") {
                        return response()->json($result);
                    }

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

                        # prevent client_id and child_id on client event has the same value
                        if ($parent->id == $studentId) {

                            throw new Exception('Client ID and Child ID has the same value');
                        }

                        # catch if studentId is not from a valid student but from client with role parent
                        if ($student->roles()->where('role_name', 'Parent')->exists()) {

                            throw new Exception('We cannot continue the process because the studentId was filled with Client that has parent role.');
                        }

                        $this->storeRelationship($parent, $student);

                        $this->attachDestinationCountry($studentId, $validated['destination_country']);

                        // check if both parent and student have already joined the event
                        $familyIds = [
                            'parentId' => $parent->id,
                            'childId' => $studentId,
                        ];

                        $result = $this->checkFamilyAreExistsOnClientEvent($familyIds, $validated);
                        if (gettype($result) != "boolean") {
                            return response()->json($result);
                        }
                    }

                    $result = $this->checkClientIsExistsOnClientEvent($client, $validated);
                    if (gettype($result) != "boolean") {
                        return response()->json($result);
                    }

                    break;

                case "teacher/counsellor":
                    $client = $this->storeTeacher($validated);

                    $result = $this->checkClientIsExistsOnClientEvent($client, $validated);
                    if (gettype($result) != "boolean") {
                        return response()->json($result);
                    }

                    break;

                default:
                    abort(404);
            }


            # declare variables for client events
            $clientEventDetails = [
                'ticket_id' => $this->generateTicketID(),
                'client_id' => $client->id, # it comes from query to database, so it should be a collection
                'child_id' => $studentId,
                'parent_id' => null,
                'event_id' => $validated['event_id'],
                'lead_id' => $validated['lead_source_id'],
                'eduf_id' => isset($incomingRequest['eduf_id']) ? $incomingRequest['eduf_id'] : null,
                'registration_type' => $validated['registration_type'], # default is PR means Pra-Reg
                'number_of_attend' => isset($validated['attend_party']) ? $validated['attend_party'] : 1,
                'notes' => $validated['notes'], # previously, notes filled with VIP & VVIP
                'referral_code' => null,
                'status' => $validated['attend_status'] == 'attend' ? 1 : 0,
                'joined_date' => Carbon::now(),
            ];


            # store client event
            $storedClientEvent = $this->clientEventRepository->createClientEvent($clientEventDetails);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Registration Event Failed | ' . $e->getMessage() . ' | ' . $e->getFile() . ' on line ' . $e->getLine());
            return response()->json([
                'success' => false,
                'code' => 'ERR',
                'message' => "We encountered an issue completing your registration. Please check for any missing information or errors and try again. If you're still having trouble, feel free to contact our support team for assistance."
            ]);
        }


        # if the event is online then 
        # system will not send the email

        
        // if ($validated['event_type'] == 'offline') {

        //     try {

        //         # send an registration success email
        //         $this->sendEmailRegistrationSuccess($validated, $storedClientEvent);
        //         Log::notice('Email registration sent sucessfully to ' . $incomingRequest['mail'] . ' refer to ticket ID : ' . $storedClientEvent->ticket_id);
        //     } catch (Exception $e) {

        //         Log::error('Failed to send email registration to ' . $incomingRequest['mail'] . ' refer to ticket ID : ' . $storedClientEvent->ticket_id . ' | ' . $e->getMessage());
        //     }
        // }



        # create log success
        $this->logSuccess('store', 'Form Embed', 'Client Event', 'Guest', $clientEventDetails, null);

        return response()->json([
            'success' => true,
            'message' => "Welcome aboard! Your registration is complete. Don't forget to check your email for exciting updates and next steps.",
            'code' => 'SCS',
            'data' => [
                'client' => [
                    'name' => $storedClientEvent->client->full_name,
                    'email' => $storedClientEvent->client->mail,
                    'is_vip' => $storedClientEvent->notes == 'VIP' ? true : false,
                    'have_child' => $validated['have_child'],
                    'register_by' => $this->getRole($storedClientEvent)['role']
                ],
                'clientevent' => [
                    'id' => $storedClientEvent->clientevent_id,
                    'ticket_id' => $storedClientEvent->ticket_id,
                    'is_offline' => (isset($validated['event_type']) || $validated['event_type']) == "offline" ? true : false,
                ],
                'link' => [
                    'scan' => url('/client-event/CE/' . $storedClientEvent->clientevent_id)
                ]
            ]
        ]);
    }

    public function storePublicRegistration(PublicRegistrationRequest $request)
    {
        $validated = $request->safe()->only([
            'role',
            'fullname',
            'mail',
            'phone',
            'school_id',
            'other_school',
            'graduation_year',
            'destination_country',
            'interest_prog',
            'secondary_name',
            'secondary_mail',
            'secondary_phone',
            'lead_source_id',
            'scholarship'
        ]);


        # declaration of default variables that will be used 
        $client = null;

        DB::beginTransaction();
        try {


            # separate the incoming request data
            switch ($validated['role']) {
                case 'student':
                    $client = $this->storeStudent($validated);
                    $clientId = $client->id;

                    break;

                case 'parent':
                    $parent = $client = $this->storeParent($validated);
                    $clientId = $client->id;
                    if (isset($validated['secondary_name'])) {
                        $validatedStudent = $request->except(['fullname', 'email', 'phone']);
                        $validatedStudent['fullname'] = $validated['secondary_name'];
                        $validatedStudent['mail'] = $validated['secondary_mail'] ?? null;
                        $validatedStudent['phone'] = $validated['secondary_phone'] ?? null;
                        $validatedStudent['scholarship'] = 'N';
                        $validatedStudent['lead_source_id'] = 'LS001'; # Website

                        $student = $this->storeStudent($validatedStudent);

                        $studentId = $student->id;

                        # prevent client_id and child_id on client event has the same value
                        if ($parent->id == $studentId) {

                            throw new Exception('Client ID and Child ID has the same value');
                        }

                        # catch if studentId is not from a valid student but from client with role parent
                        if ($student->roles()->where('role_name', 'Parent')->exists()) {

                            throw new Exception('We cannot continue the process because the studentId was filled with Client that has parent role.');
                        }

                        $this->storeRelationship($parent, $student);

                        if ($studentId != null && isset($validated['destination_country'])) {
                            $this->attachDestinationCountry($studentId, $validated['destination_country']);
                        }

                        if ($studentId != null && isset($validated['interest_prog'])) {
                            if (isset($validated['interest_prog']) && $validated['interest_prog'] !== null) {
                                $this->reAttachInterestPrograms($studentId, $validated['interest_prog']);
                            }
                        }
                    } else {
                        if (isset($schoolId)) {
                            $this->clientRepository->updateClient($parent->id, ['sch_id' => $schoolId]);
                        }
                    }
                    break;

                case 'teacher/counsellor':
                    $client = $this->storeTeacher($validated);
                    $clientId = $client->id;
                    break;
            }

            if ($client != null && isset($validated['destination_country'])) {
                $this->attachDestinationCountry($clientId, $validated['destination_country']);
            }

            if ($client != null && isset($validated['interest_prog'])) {
                // if(count($validated['interest_prog']) > 0){
                //     foreach ($validated['interest_prog'] as $interestProg) {
                //         $this->reAttachInterestPrograms($clientId, $interestProg);
                //     }
                // }

                if (isset($validated['interest_prog'])) {
                    $this->reAttachInterestPrograms($clientId, $validated['interest_prog']);
                }
            }


            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Registration from EduAll website failed: ' . $e->getMessage() . ' | On Line: ' . $e->getLine() . ' on file ' . $e->getFile());

            return response()->json([
                'success' => false,
                'message' => "We encountered an issue completing your registration. Please check for any missing information or errors and try again. If you're still having trouble, feel free to contact our support team for assistance."
            ]);
        }

        ################################################################
        ## email requirements ##
        ################################################################


        $prog = array_key_exists('interest_prog', $validated) ? $this->programRepository->getProgramById($validated['interest_prog']) : null;
        $passedData = [
            'client' => [
                'name' => $client->full_name,
            ],
            'program' => [
                'name' => $prog->program_name ?? null
            ]
        ];

        $dataResponseClient = [
            'role' => $validated['role'],
            'first_name' => $client->first_name,
            'last_name' => $client->last_name,
            'mail' => $client->mail,
            'phone' => $client->phone,
            'inteset_prog' => $client->interestPrograms,
            'school_id' => $client->sch_id,
            'school_name' => isset($client->school) ? $client->school->sch_name : null,
            'graduation_year' => $client->graduation_year
        ];
        
        /**
         * note:
         * address that being attached in mail::send could be null
         * since there are two ways to get the existing client
         * first: find from tbl_client
         * second: find from tbl_client_additional_info
         * 
         * and to prevent mail::send goes error because no address attached
         * use the email that being submitted
         *  
         */

        try {

            // if ( !isset($validated['mail']) && $validated['mail'] == null )
            //     throw new Exception("Insufficient email address of {$client->full_name}");
            

            switch ($validated['role'])
            {
                case "student":
                    $subject = 'Your registration is confirmed';
                    $template = 'mail-template.registration.public.thanks-email-student';
                    # the system will email 
                    # if they inputted the email address
                    if ( $validated['mail'] )
                    {
                        $recipient['name'] = $client->full_name;
                        $recipient['email'] = $validated['mail'];
                        $this->sendEmailPublicRegistration($template, $passedData, $subject, $recipient);
                    }
                    break;
    
                case "parent":
                    $passedData['client']['child_name'] = $validated['secondary_name'];
                    $subject = 'Your registration is confirmed';
                    $template = 'mail-template.registration.public.thanks-email-parent';
                    # the system will email 
                    # if they inputted the email address
                    if ( $validated['mail'] )
                    {
                        $recipient['name'] = $client->full_name;
                        $recipient['email'] = $validated['mail'];
                        $this->sendEmailPublicRegistration($template, $passedData, $subject, $recipient);
                    }
                    break;
    
                case "teacher/counsellor":
                    $passedData['client']['school'] = $validated['school_id'] == 'new' ? $validated['other_school'] : School::find($validated['school_id'])->sch_name;
                    $passedData['client']['phone'] = $validated['phone'];
                    $passedData['client']['email'] = $validated['mail'];
                    $subject = "A new teacher has signed up for the {$passedData['program']['name']}.";
                    $template = 'mail-template.registration.public.thanks-email-teacher';
                    $recipient['name'] = 'Theresya Afila'; # hard coded for partnership PIC 
                    $recipient['email'] = 'theresya.afila@edu-all.com';
                    $this->sendEmailPublicRegistration($template, $passedData, $subject, $recipient);
                    break;
            }                
            $sent_mail = 1;
            
        } catch (Exception $e) {

            $sent_mail = 0;
            Log::error('Failed send email to public registration | error : ' . $e->getMessage() . ' on file ' . $e->getFile() . ' | Line ' . $e->getLine());
            throw new Exception($e->getMessage(). ' on line ' . $e->getLine() . ' on file ' . $e->getFile());
        }


        # create log success
        $this->logSuccess('store', 'Form Embed', 'Public Registration', 'Guest', $dataResponseClient, null);

        return response()->json([
            'success' => true,
            'data' => $dataResponseClient,
            'message' => "Welcome aboard! Your registration is complete."
        ]);
    }

    public function sendEmailPublicRegistration($template, $passedData, $subject, $recipient)
    {
        Mail::send(
            $template,
            $passedData,
            function ($message) use ($subject, $recipient) {

                // $message->to($client->mail, $client->full_name) //! not used

                $message->to($recipient['email'], $recipient['name'])
                    ->subject($subject);
            }
        );
    }

    public function getRole(ClientEvent $clientevent)
    {
        # initiate variables
        $role = null;
        $have_child = false;
        $client = $clientevent->client;

        switch ($client->roles) {

            case $client->roles()->where('role_name', 'parent')->exists():
                $role = 'Parent';

                # turn have_child into true when the parent has children
                # but check the children from clientevent not from the parent
                if ($client->childrens->count() > 0)
                    $have_child = true;

                break;

            case $client->roles()->where('role_name', 'student')->exists():
                $role = 'Student';

                break;

            case $client->roles()->where('role_name', 'Teacher/Counselor')->exists():
                $role = 'Teacher/Counsellor';
                break;
        }

        return [
            'role' => strtolower($role),
            'have_child' => $have_child
        ];
    }

    public function generateTicketID()
    {
        do {

            $ticket_id = Str::random(4);
            $isUnique = $this->clientEventRepository->isTicketIDUnique($ticket_id);
        } while ($isUnique === false);

        return $ticket_id;
    }

    private function checkClientIsExistsOnClientEvent($client, $incomingRequest)
    {
        # check if registered client has already joined the event
        if ($existing = $this->clientEventRepository->getClientEventByClientIdAndEventId($client->id, $incomingRequest['event_id'])) {


            return [
                'success' => true,
                'message' => 'You have joined the event.',
                'code' => 'EXT', # existing / has joined
                'data' => [
                    'client' => [
                        'name' => $existing->client->full_name,
                        'email' => $existing->client->mail,
                        'is_vip' => $existing->notes == 'VIP' ? true : false,
                        'register_by' => $this->getRole($existing)['role']
                    ],
                    'clientevent' => [
                        'id' => $existing->clientevent_id,
                        'ticket_id' => $existing->ticket_id,
                        'is_offline' => (isset($incomingRequest['event_type']) || $incomingRequest['event_type']) == "offline" ? true : false,
                    ],
                    'link' => [
                        'scan' => url('/client-event/CE/' . $existing->clientevent_id)
                    ]
                ]
            ];
        }

        return true;
    }

    private function checkFamilyAreExistsOnClientEvent(array $familyIds, $incomingRequest)
    {
        if ($existing = $this->clientEventRepository->getClientEventByMultipleIdAndEventId($familyIds['parentId'], $incomingRequest['event_id'], $familyIds['childId'])) {

            return [
                'success' => true,
                'message' => 'You and your child have joined the event.',
                'code' => 'EXT', # existing / has joined
                'data' => [
                    'client' => [
                        'name' => $existing->client->full_name,
                        'email' => $existing->client->mail,
                        'is_vip' => $existing->notes == 'VIP' ? true : false,
                        'register_by' => $this->getRole($existing)['role']
                    ],
                    'clientevent' => [
                        'id' => $existing->clientevent_id,
                        'ticket_id' => $existing->ticket_id,
                        'is_offline' => (isset($incomingRequest['event_type']) || $incomingRequest['event_type']) == "offline" ? true : false,
                    ],
                    'link' => [
                        'scan' => url('/client-event/CE/' . $existing->clientevent_id)
                    ]
                ]
            ];
        }

        return true;
    }

    private function storeStudent($incomingRequest)
    {
        # check if the client exists in crm database
        $existingClient = $this->checkExistingClient($this->tnSetPhoneNumber($incomingRequest['phone']), $incomingRequest['mail']);


        # declare some variables
        $splitNames = $this->split($incomingRequest['fullname']);
        $schoolId = $this->getSchoolId($incomingRequest);


        # create a new client > student
        $newClientDetails = [
            'first_name' => $splitNames['first_name'],
            'last_name' => $splitNames['last_name'],
            'mail' => $incomingRequest['mail'],
            'phone' => $this->tnSetPhoneNumber($incomingRequest['phone']),
            'register_by' => $incomingRequest['role'],
            'st_grade' => $this->getGradeByGraduationYear($incomingRequest['graduation_year']),
            'graduation_year' => $incomingRequest['graduation_year'],
            'lead_id' => $incomingRequest['lead_source_id'],
            'eduf_id' => isset($incomingRequest['eduf_id']) ? $incomingRequest['eduf_id'] : null,
            'scholarship' => $incomingRequest['scholarship'],
            'sch_id' => $schoolId
        ];

        $data_client_for_log_client[0] = [
            'first_name' => $newClientDetails['first_name'],
            'last_name' => $newClientDetails['last_name'],
            'lead_source' => $incomingRequest['lead_source_id'],
            'inputted_from' => 'form-embed'
        ];

        # if the client is exists
        if ($existingClient['isExist']){
            $client = $this->clientRepository->getClientById($existingClient['id']);
            
            $data_client_for_log_client[0]['client_id'] = $client->id;
            # trigger insert log client
            ProcessInsertLogClient::dispatch($data_client_for_log_client)->onQueue('insert-log-client');
            
            return $client;
        }

        $client = $this->clientRepository->createClient('Student', $newClientDetails);
        
        # trigger to verify student / children
        // ProcessVerifyClient::dispatch([$clientId])->onQueue('verifying_client');

        $data_client_for_log_client[0]['client_id'] = $client->id;
        # trigger insert log client
        ProcessInsertLogClient::dispatch($data_client_for_log_client)->onQueue('insert-log-client');

        return $client;
    }

    private function attachInterestPrograms($clientId, $interestedPrograms)
    {
        $selectedClient = $this->clientRepository->getClientById($clientId);
        if (!$selectedClient->interestPrograms()->where('tbl_interest_prog.prog_id', $interestedPrograms)->exists())
            $this->clientRepository->addInterestProgram($clientId, ['prog_id' => $interestedPrograms]);
    }

    private function reAttachInterestPrograms($clientId, $interestedPrograms)
    {
        return $this->clientRepository->addInterestProgram($clientId, ['prog_id' => $interestedPrograms]);
    }

    private function attachDestinationCountry($clientId, array $destinationCountries)
    {
        if (count($destinationCountries) > 0)
            return $this->clientRepository->syncDestinationCountry($clientId, $destinationCountries);
    }

    private function storeParent($incomingRequest)
    {
        # check if the client exists in crm database
        $existingClient = $this->checkExistingClient($this->tnSetPhoneNumber($incomingRequest['phone']), $incomingRequest['mail']);

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
            'phone' => $this->tnSetPhoneNumber($incomingRequest['phone']),
            'register_by' => $incomingRequest['role'],
            'scholarship' => $incomingRequest['scholarship'],
            'lead_id' => $incomingRequest['lead_source_id'],
            'eduf_id' => isset($incomingRequest['eduf_id']) ? $incomingRequest['eduf_id'] : null,
        ];

        $client = $this->clientRepository->createClient('Parent', $newClientDetails);
        $clientId = $client->id;

        # trigger to verify parent
        // ProcessVerifyClientParent::dispatch([$clientId])->onQueue('verifying_client_parent');

        return $client;
    }

    private function storeRelationship($parent, $children)
    {
        $this->clientRepository->createManyClientRelation($parent->id, $children->id);
    }

    private function storeTeacher($incomingRequest)
    {
        # check if the client exists in crm database
        $existingClient = $this->checkExistingClient($this->tnSetPhoneNumber($incomingRequest['phone']), $incomingRequest['mail']);

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
            'phone' => $this->tnSetPhoneNumber($incomingRequest['phone']),
            'register_by' => $incomingRequest['role'],
            'sch_id' => $schoolId,
            'lead_id' => $incomingRequest['lead_source_id'],
            'eduf_id' => isset($incomingRequest['eduf_id']) ? $incomingRequest['eduf_id'] : null,
        ];

        $client = $this->clientRepository->createClient('Teacher/Counselor', $newClientDetails);

        # trigger to verify teacher
        // ProcessVerifyClient::dispatch([$clientId])->onQueue('verifying_client_teacher');

        return $client;
    }

    public function sendEmailVerificationSuccess($incomingRequest, ClientEvent $clientevent)
    {
        # initiate variables 
        $storedClientEventId = $clientevent->clientevent_id;
        $eventName = $clientevent->event->event_title;
        $client = $clientevent->client;
        $clientInformation = [
            'name' => $client->full_name,
            'mail' => $client->mail
        ];

        # why use ots-mail-registration template
        # because it has the same mail content
        $template = 'mail-template.registration.event.ots-mail-registration';
        $email = [
            'subject' => "Welcome to the {$eventName}!",
            'recipient' => [
                'name' => $incomingRequest['fullname'],
                'mail' => $incomingRequest['mail']
            ]
        ];


        # populate client variables
        # when they are student or parents
        # and when they are parents but have a child
        if (($client->roles()->whereIn('role_name', ['student', 'parent'])->exists()
                || (($client->roles()->where('role_name', 'parent')->exists()) && $client->childrens->count() > 0))
            && strtolower($clientevent->notes) != 'vip'
        ) {
            # populate the client array
            $assessment_link = env('EDUALL_ASSESSMENT_URL', null);
            if ($assessment_link !== null)
                $assessment_link .= '?ticket=' . $clientevent->ticket_id;

            $clientInformation['assessment_link'] = $assessment_link;
        }

        $event = [
            'eventName' => $eventName,
            'eventDate_start' => date('l, d M Y', strtotime($clientevent->event->event_startdate)),
            'eventDate_end' => date('M d, Y', strtotime($clientevent->event->event_enddate)),
            'eventTime_start' => date('g A', strtotime($clientevent->event->event_startdate)),
            'eventTime_end' => date('H:i', strtotime($clientevent->event->event_enddate)),
            'eventLocation' => $clientevent->event->event_location,
        ];

        # passing parameter into template
        $passedData = [
            'client' => $clientInformation,
            'event' => $event
        ];

        # send the email function
        try {

            Mail::send(
                $template,
                $passedData,
                function ($message) use ($email) {
                    $message->to($email['recipient']['mail'], $email['recipient']['name'])
                        ->subject($email['subject']);
                }
            );
            $sent_mail = 1;
        } catch (Exception $e) {

            $sent_mail = 0;
            throw new Exception($e->getMessage());
            Log::error('Failed send email to participant of Event ' . $eventName . ' | error : ' . $e->getMessage() . ' on file ' . $e->getFile() . ' | Line ' . $e->getLine());
        }

        # store to log so that we can track the sending status of each email
        # but check if the log was there, then just update it
        # otherwise, we will create the new log of registration-event-mail
        if (!$existingLogMail = $this->clientEventLogMailRepository->getClientEventLogMailByClientEventIdAndCategory($storedClientEventId, 'verification-registration-event-mail')) {
            # when the log does not exist

            $logDetails = [
                'clientevent_id' => $storedClientEventId,
                'sent_status' => $sent_mail,
                'category' => 'verification-registration-event-mail'
            ];

            Log::notice("Form Embed: Successfully send thank mail registration", $passedData);

            return $this->clientEventLogMailRepository->createClientEventLogMail($logDetails);
        }

        return true;
    }

    public function sendEmailRegistrationSuccess($incomingRequest, ClientEvent $clientevent)
    {
        # there are two ways procedure of how to send the email depends on where the user registered (ots, pra-reg)

        # initiate global variables for emails
        $storedClientEventId = $clientevent->clientevent_id;
        $eventName = $clientevent->event->event_title;
        $clientInformation = [
            'name' => $clientevent->client->full_name,
            'mail' => $clientevent->client->mail
        ];


        switch (strtolower($incomingRequest['registration_type'])) {
            case "ots":
                # thanks mail with a ticket and link to access EduApp

                # initiate variables
                $template = 'mail-template.registration.event.ots-mail-registration';
                $client = $clientevent->client;
                $email = [
                    'subject' => "Welcome to the {$eventName}!",
                    'recipient' => [
                        'name' => $incomingRequest['fullname'],
                        'mail' => $incomingRequest['mail']
                    ]
                ];


                # populate client variables
                # when they are student or parents
                # and when they are parents but have a child
                if (($client->roles()->whereIn('role_name', ['student', 'parent'])->exists()
                        || (($client->roles()->where('role_name', 'parent')->exists()) && $client->childrens->count() > 0))
                    && strtolower($incomingRequest['notes']) != 'vip'
                ) {
                    # populate the client array
                    $assessment_link = env('EDUALL_ASSESSMENT_URL', null);
                    if ($assessment_link !== null)
                        $assessment_link .= '?ticket=' . $clientevent->ticket_id;

                    $clientInformation['assessment_link'] = $assessment_link;
                }

                $event = [
                    'eventName' => $eventName,
                    'eventDate_start' => date('l, d M Y', strtotime($clientevent->event->event_startdate)),
                    'eventDate_end' => date('M d, Y', strtotime($clientevent->event->event_enddate)),
                    'eventTime_start' => date('g A', strtotime($clientevent->event->event_startdate)),
                    'eventTime_end' => date('H:i', strtotime($clientevent->event->event_enddate)),
                    'eventLocation' => $clientevent->event->event_location,
                ];

                # passing parameter into template
                $passedData = [
                    'client' => $clientInformation,
                    'event' => $event
                ];


                break;


            default:
                # thanks mail with a ticket only or QR code

                # initiate variables
                # variable for sending email
                $template = 'mail-template.registration.event.pra-reg-mail-registration';
                $email = [
                    'subject' => "Thank you for registering to {$eventName}!",
                    'recipient' => $clientInformation
                ];

                # this ticket id will be converted into QR code
                $ticket_id = $clientevent->ticket_id;


                $event = [
                    'ticket' => $clientevent->ticket_id,
                    'eventName' => $eventName,
                    'eventDate_start' => date('l, d M Y', strtotime($clientevent->event->event_startdate)),
                    'eventDate_end' => date('M d, Y', strtotime($clientevent->event->event_enddate)),
                    'eventTime_start' => date('g A', strtotime($clientevent->event->event_startdate)),
                    'eventTime_end' => date('H:i', strtotime($clientevent->event->event_enddate)),
                    'eventLocation' => $clientevent->event->event_location,
                ];

                # passing parameter into template
                $passedData = [
                    'qr' => $ticket_id,
                    'client' => $clientInformation,
                    'event' => $event
                ];
        }

        # send the email function
        try {

            Mail::send(
                $template,
                $passedData,
                function ($message) use ($email) {
                    $message->to($email['recipient']['mail'], $email['recipient']['name'])
                        ->subject($email['subject']);
                }
            );
            $sent_mail = 1;
        } catch (Exception $e) {

            $sent_mail = 0;
            throw new Exception($e->getMessage());
            Log::error('Failed send email to participant of Event ' . $eventName . ' | error : ' . $e->getMessage() . ' on file ' . $e->getFile() . ' | Line ' . $e->getLine());
        }


        # store to log so that we can track the sending status of each email
        # but check if the log was there, then just update it
        # otherwise, we will create the new log of registration-event-mail
        if (!$existingLogMail = $this->clientEventLogMailRepository->getClientEventLogMailByClientEventIdAndCategory($storedClientEventId, 'registration-event-mail')) {
            # when the log does exist

            // $logMailId = $existingLogMail->id;
            // $newLogDetails = [
            //     'sent_status' => $sent_mail
            // ];

            // Log::notice("Form Embed: Successfully re-send thank mail registration", $passedData);

            // return $this->clientEventLogMailRepository->updateClientEventLogMail($logMailId, $newLogDetails);

            // } else {
            # when the log does not exist

            $logDetails = [
                'clientevent_id' => $storedClientEventId,
                'sent_status' => $sent_mail,
                'category' => 'registration-event-mail'
            ];

            Log::notice("Form Embed: Successfully send thank mail registration", $passedData);

            return $this->clientEventLogMailRepository->createClientEventLogMail($logDetails);
        }
    }

    private function getSchoolId($incomingRequest)
    {
        $schoolId = $incomingRequest['school_id'];


        # SCH-0301 == other 
        if ($incomingRequest['school_id'] == "new" || $incomingRequest['school_id'] == "SCH-0301") {
            # store a new school or get the existing one

            if (!$schoolExistOnDB = $this->schoolRepository->getSchoolByName($incomingRequest['other_school'])) {
                # if user did write down the school name outside our school collection
                # and the school name does not exist in our database then store it to CRM database

                // $last_id = School::max(DB::raw('SUBSTR(sch_id, 5)'));
                $last_id = School::withTrashed()->selectRaw('MAX(SUBSTR(sch_id,5)) as max')->first()->max;
                $school_id_with_label = 'SCH-' . $this->add_digit((int)$last_id + 1, 4);

                if ($school_id_with_label == NULL && $incomingRequest['other_school'] == NULL) {
                    throw new Exception('There is an issue preventing the school from being created.');
                }

                $school = [
                    'sch_id' => $school_id_with_label,
                    'sch_name' => $incomingRequest['other_school'],
                ];

                # create a new school
                $school = $this->schoolRepository->createSchool($school);
                $schoolId = $school->sch_id;

                # manipulate the variable schoolExistOnDB
                $schoolExistOnDB = $school;
            }

            # if user did write down the school name outside our school collection
            # but the school name have been stored previously then get the existing school

            $schoolId = $schoolExistOnDB->sch_id;
        }

        return $schoolId;
    }

    public function update(Request $request)
    {

        $requestUpdateClientEventID = $request->route('clientevent_id');
        # fetch the client information from client event
        $requestUpdateClientEvent = $this->clientEventRepository->getClientEventById($requestUpdateClientEventID);
        if (!$requestUpdateClientEvent) {
            Log::warning("Client Event Verify: Could not continue the process because invalid identifier.", $requestUpdateClientEventID);
            return response()->json([
                'success' => false,
                'message' => 'Could not continue the process because invalid identifier.'
            ]);
        }

        # validation
        $rules = [
            'role' => 'required|in:parent,student,teacher/counsellor',
            'user' => 'nullable',
            'fullname' => 'required',
            'mail' => 'required|email|exists:tbl_client,mail',
            'phone' => [
                'required',
                Rule::unique('tbl_client')->ignore($requestUpdateClientEvent->client->id),
                'different:secondary_phone'
            ],
            'secondary_name' => 'required_if:have_child,true',
            'secondary_email' => 'nullable|email',
            'secondary_phone' => 'nullable|different:phone',
            'school_id' => [
                'nullable',
                $request->school_id != 'new' ? 'exists:tbl_sch,sch_id' : null
            ],
            'other_school' => 'nullable',
            'graduation_year' => [
                'nullable',
                $request->role == 'student' ? 'required' : null,
                $request->role == 'parent' ? 'required_if_accepted:have_child' : null,
            ],
            'destination_country' => [
                'array',
                $request->role == 'student' ? 'required' : 'required_if_accepted:have_child',
            ],
            'destination_country.*' => 'exists:tbl_country,id',
            'scholarship' => 'required|in:Y,N',
            'event_id' => 'required|exists:tbl_events,event_id',
            # status
            'attend_status' => 'nullable|in:attend',
            # number of attend
            'attend_party' => 'nullable|min:1',
            'event_type' => 'nullable|in:offline',
            # registration_type
            'status' => 'required|in:OTS,PR',
            # referral code
            'referral' => 'nullable|exists:tbl_client,id',
            # notes
            'client_type' => 'nullable|in:vip',
            'have_child' => 'required|boolean'
        ];

        $incomingRequest = $request->only([
            'role',
            'user',
            'fullname',
            'mail',
            'phone',
            'secondary_name',
            'secondary_email',
            'secondary_phone',
            'school_id',
            'other_school',
            'graduation_year',
            'destination_country',
            'scholarship',
            'lead_source_id',
            'event_id',
            'attend_status',
            'attend_party',
            'event_type',
            'status',
            'referral',
            'have_child'
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

        # modify the variables inside request array
        $validated = $validated->merge([
            'status' => $validated['attend_status'] == "attend" ? 1 : 0,
            'number_of_attend' => $validated['attend_party'] ?? 1,
            'registration_type' => strtoupper($validated['status']) ?? "PR",
            'referral_code' => $validated['referral'] ?? null,
            'notes' => $validated['client_type'] ?? null,
        ]);


        DB::beginTransaction();
        try {

            # update the data which depends on their register_by 
            switch ($requestUpdateClientEvent->client->roles->count() > 0) {

                case $requestUpdateClientEvent->client->roles()->where('role_name', 'student')->exists():
                    # initiate variables for client
                    $studentId = $requestUpdateClientEvent->client_id;
                    $student = $client = $this->updateStudent($studentId, $validated);

                    # attach interest programs
                    # get the value of interest programs from event category
                    $joinedEvent = Event::whereEventId($validated['event_id']);
                    if ($eventCategory = $joinedEvent->category) {
                        # it will not store the interest program from event
                        # when it comes to update function
                        $this->attachInterestPrograms($studentId, $eventCategory);
                    }

                    # attach destination countries if any
                    $this->attachDestinationCountry($studentId, $validated['destination_country']);

                    break;

                case $requestUpdateClientEvent->client->roles()->where('role_name', 'parent')->exists():
                    # initiate variables for clients
                    $parentId = $requestUpdateClientEvent->client_id;
                    $parent = $client = $this->updateParent($parentId, $validated);

                    # when the request says they have a children
                    # but based on the data we know that the parents don't include a children when they are register
                    if ($validated['have_child'] == true && $requestUpdateClientEvent->child_id === NULL) {
                        $studentId = $requestUpdateClientEvent->child_id;

                        $validatedStudent = $request->except(['fullname', 'email', 'phone']);
                        $validatedStudent['fullname'] = $validated['secondary_name'];
                        $validatedStudent['mail'] = $validated['secondary_email'];
                        $validatedStudent['phone'] = $validated['secondary_phone'];

                        $student = $this->storeStudent($validatedStudent);
                        $studentId = $student->id;

                        # prevent client_id and child_id on client event has the same value
                        if ($parent->id == $studentId) {

                            throw new Exception('Client ID and Child ID has the same value');
                        }

                        # catch if studentId is not from a valid student but from client with role parent
                        if ($student->roles()->where('role_name', 'Parent')->exists()) {

                            throw new Exception('We cannot continue the process because the studentId was filled with Client that has parent role.');
                        }

                        $this->storeRelationship($parent, $student);

                        $this->attachDestinationCountry($studentId, $validated['destination_country']);
                    }


                    # when the request says they have a children
                    # and from the database, they do have a children
                    if ($validated['have_child'] == true && $requestUpdateClientEvent->child_id !== NULL) {
                        $studentId = $requestUpdateClientEvent->child_id;

                        $validatedStudent = $request->except(['fullname', 'email', 'phone']);
                        $validatedStudent['fullname'] = $validated['secondary_name'];
                        $validatedStudent['mail'] = $validated['secondary_email'];
                        $validatedStudent['phone'] = $validated['secondary_phone'];

                        $student = $this->updateStudent($studentId, $validatedStudent);
                        $studentId = $student->id;


                        $this->attachDestinationCountry($studentId, $validated['destination_country']);
                    }

                    # when the request says they don't have a children
                    # but somehow in the time the parents registered, they had inputted the children data which is input "have_child" as a yes 
                    if ($validated['have_child'] == false && $requestUpdateClientEvent->child_id !== NULL) {

                        $studentId = $requestUpdateClientEvent->child_id;

                        $this->deleteRelation($parent, $studentId);

                        Log::info('Student ID : ' . $studentId . 'has been detached from ' . $parent->id);
                    }

                    break;

                case $requestUpdateClientEvent->client->roles()->where('role_name', 'teacher/counselor')->exists():
                    $teacherId = $requestUpdateClientEvent->client_id;
                    $client = $this->updateTeacher($teacherId, $validated);
                    break;

                default:
                    abort(404);
            }


            # update client event
            $updatedClientEvent = $this->clientEventRepository->updateClientEvent($requestUpdateClientEvent->clientevent_id, [
                'number_of_attend' => $validated['attend_party'],
                'status' => 1 # they came to the event
            ]);


            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Verifying Registration Event Failed | ' . $e->getMessage() . ' | ' . $e->getFile() . ' on line ' . $e->getLine());
            return response()->json([
                'success' => false,
                'code' => 'ERR',
                'message' => "We encountered an issue completing your verification. Please check for any missing information or errors and try again. If you're still having trouble, feel free to contact our support team for assistance."
            ]);
        }

        try {

            # send an registration success email
            $this->sendEmailVerificationSuccess($validated, $updatedClientEvent);
            Log::notice('Email verifying sucessfully send to ' . $incomingRequest['mail'] . ' refer to ticket ID : ' . $updatedClientEvent->ticket_id);
        } catch (Exception $e) {

            Log::error('Failed to send email verifying to ' . $incomingRequest['mail'] . ' refer to ticket ID : ' . $updatedClientEvent->ticket_id . ' | ' . $e->getMessage());
        }


        # create log success
        $this->logSuccess('update', 'Form Embed', 'Client Event', 'Guest', $updatedClientEvent, $requestUpdateClientEvent);


        return response()->json([
            'success' => true,
            'message' => 'Verifying registration event success',
            'code' => 'SCS',
            'data' => [
                'client' => [
                    'name' => $updatedClientEvent->client->full_name,
                    'email' => $updatedClientEvent->client->mail,
                    'is_vip' => $updatedClientEvent->notes == 'VIP' ? true : false,
                    'have_child' => $validated['have_child'],
                    'register_by' => $updatedClientEvent->client->register_by
                ],
                'clientevent' => [
                    'id' => $updatedClientEvent->clientevent_id,
                    'ticket_id' => $updatedClientEvent->ticket_id
                ],
            ]
        ]);
    }

    private function updateTeacher($teacherId, $incomingRequest)
    {
        # declare some variables
        $splitNames = $this->split($incomingRequest['fullname']);
        $schoolId = $this->getSchoolId($incomingRequest);


        # create a new client > student
        $newClientDetails = [
            'first_name' => $splitNames['first_name'],
            'last_name' => $splitNames['last_name'],
            'mail' => $incomingRequest['mail'],
            'phone' => $this->tnSetPhoneNumber($incomingRequest['phone']),
            'register_by' => $incomingRequest['role'],
            'sch_id' => $schoolId,
            'lead_id' => 'LS001', # lead is hardcoded into website
        ];

        $client = $this->clientRepository->updateClient($teacherId, $newClientDetails);

        return $client;
    }

    private function updateParent($parentId, $incomingRequest)
    {
        # declare some variables
        $splitNames = $this->split($incomingRequest['fullname']);

        # create a new client > student
        $newClientDetails = [
            'first_name' => $splitNames['first_name'],
            'last_name' => $splitNames['last_name'],
            'mail' => $incomingRequest['mail'],
            'phone' => $this->tnSetPhoneNumber($incomingRequest['phone']),
            'register_by' => $incomingRequest['role'],
            'scholarship' => $incomingRequest['scholarship'],
            'lead_id' => 'LS001', # lead is hardcoded into website
        ];

        $client = $this->clientRepository->updateClient($parentId, $newClientDetails);

        return $client;
    }

    private function deleteRelation($parent, $studentId)
    {
        # check parent relation with the student
        if ($parent->childrens()->where('id', $studentId)->exists())
            $parent->childrens()->detach($studentId);
    }

    private function updateStudent($clientId, $incomingRequest)
    {
        # declare some variables
        $splitNames = $this->split($incomingRequest['fullname']);
        $schoolId = $this->getSchoolId($incomingRequest);


        # create a new client > student
        $newClientDetails = [
            'first_name' => $splitNames['first_name'],
            'last_name' => $splitNames['last_name'],
            'mail' => $incomingRequest['mail'],
            'phone' => $this->tnSetPhoneNumber($incomingRequest['phone']),
            'register_by' => $incomingRequest['role'],
            'st_grade' => $this->getGradeByGraduationYear($incomingRequest['graduation_year']),
            'graduation_year' => $incomingRequest['graduation_year'],
            'lead_id' => 'LS001', # lead is hardcoded into website
            'scholarship' => $incomingRequest['scholarship'],
            'sch_id' => $schoolId
        ];

        $client = $this->clientRepository->updateClient($clientId, $newClientDetails);

        return $client;
    }

    public function getUserByTicket($ticket_no)
    {
        # get student info
        $student = $this->clientRepository->getClientByTicket($ticket_no);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => "I apologize, but it appears you don't currently have access to the initial assessment app."
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $student
        ]);
    }

    public function getUserByUUID($uuid)
    {
        # get student info
        $student = $this->clientRepository->getClientByUUIDforAssessment($uuid);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => "I apologize, but it appears you don't currently have access to the initial assessment app."
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $student
        ]);
    }

    //! Timesheet needs
    public function checkUserEmail(Request $request): JsonResponse
    {
        $incomingEmail = $request->get('email');

        $query = \App\Models\User::query()->
            with([
                'educations' => function ($query) {
                    $query->select('tbl_univ.univ_name', 'tbl_user_educations.created_at')->first();
                },
                'position' => function ($query) {
                    $query->select('id', 'position_name');
                }
            ])->
            withAndWhereHas('roles', function ($query) {
                $query->whereIn('role_name', ['Mentor', 'External Mentor', 'Tutor', 'Editor'])->select('role_name');
            })->where('email', $incomingEmail);

        $result = $resultInArray = null;
        if ($query->exists()) {
            $result = $query->select('id', 'first_name', 'last_name', 'email', 'phone', 'password', 'npwp', 'position_id', 'active')->first();

            # fetch the roles
            foreach ($result->roles as $role) {

                $mappedRoles[] = [
                    'role_name' => $role->role_name,
                    'subjects' => $result->user_subjects && $role->role_name == 'Tutor' ? $result->user_subjects->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'subject' => $item->subject->name,
                            'year' => $item->year,
                            'agreement' => $item->agreement,
                            'head' => $item->head,
                            'additional_fee' => $item->additional_fee,
                            'grade' => $item->grade,
                            'fee_individual' => $item->fee_individual,
                            'fee_group' => $item->fee_group,
                        ];
                    }) : null
                ];
            }

            $resultInArray = $result->toArray();
            $resultInArray['roles'] = $mappedRoles;

            unset($resultInArray['user_subjects']);
            $resultInArray['has_npwp'] = $result->npwp ? true : false;
        }

        return response()->json($resultInArray);
    }

    public function validateCredentials(Request $request): JsonResponse
    {
        $incomingEmail = $request->post('email');
        $incomingPassword = $request->post('password');

        $user = \App\Models\User::with('roles')->where('email', $incomingEmail)->first();

        if ( !$user ) {
            throw new HttpResponseException(
                response()->json([
                    'errors' => 'The user is not registered.'
                ], JsonResponse::HTTP_BAD_REQUEST)
            );
        }

        // check if user is active
        if ( $user->active == 0 ) {

            throw new HttpResponseException(
                response()->json([
                    'errors' => 'The user doesn\'t have access anymore.'
                ], JsonResponse::HTTP_BAD_REQUEST)
            );
        }
        
        // check if the credential is correct
        if ( !Hash::check($incomingPassword, $user->password)) {

            throw new HttpResponseException(
                response()->json([
                    'errors' => 'The provided credentials are incorrect.'
                ], JsonResponse::HTTP_BAD_REQUEST)
            );
        }

        return response()->json($user);
    }

    public function getMentorTutors(Request $request, $authorization = null): JsonResponse
    {
        /* Incoming request */
        $keyword = $request->get('keyword');
        $paginate = $request->get('paginate'); # true will return paginate results, false will return all results 
        $role = $request->get('role');

        $user = \App\Models\User::query()->select('id', 'first_name', 'last_name', 'email', 'phone', 'npwp')->with([
                'roles',
            ])->whereHas('roles', function ($query) use ($role) {
                $query->when($role, function ($sub) use ($role) {
                    $sub->where('role_name', $role);
                }, function ($sub) use ($role) {
                    $sub->whereIn('role_name', ['Mentor', 'External Mentor', 'Tutor']);
                });
            })->when($keyword, function ($query) use ($keyword) {
                $query->where(function ($sub) use ($keyword) {
                        $sub->
                        whereRaw('CONCAT(first_name, " ", COALESCE(last_name)) like ?', ['%' . $keyword . '%'])->
                        orWhereRaw('email like ?', ['%' . $keyword . '%'])->
                        orWhereRaw('phone like ?', ['%' . $keyword . '%']);
                    });
            })->whereNotNull('email')->isActive()->get();

        $mappedUser = $user->map(function ($data) {

            $userRole = $data->roles;
            $acceptedRole = [];
            
            # remove duplication using array as comparison
            $storedRole = [];

            foreach ($userRole as $user_role) {
                $role_name = $user_role['role_name'];
                if (!in_array($role_name, ['Mentor', 'External Mentor', 'Tutor']))
                    continue;

                if ( array_search($role_name, $storedRole) )
                    continue;
                
                $acceptedRole[] = [
                    'role' => $role_name,
                ];

                # array $storedRole uses for removing duplication purposes only
                array_push($storedRole, $role_name);
            }

            return [
                'uuid' => $data['id'],
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'has_npwp' => $data['npwp'] ? true : false, 
                'roles' => $acceptedRole
            ];
        });

        if ($paginate)
            $mappedUser = $mappedUser->paginate(10);

        return response()->json($mappedUser);
    }

    public function updateUser(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required'
        ]);

        $validated = $request->only(['email', 'password']);

        DB::beginTransaction();
        try {
            $user = \App\Models\User::where('email', $validated['email'])->first();
            $user->password = Hash::make($validated['password']);
            $user->save();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage(),
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        Log::info($user->first_name . ' ' . $user->last_name . ' has perform a password change.');

        return response()->json([
            'message' => 'Information has been updated.'
        ]);
    }

    public function getClientInformation($uuid): JsonResponse
    {
        $userClient = UserClient::where('id', $uuid)->select('*')->selectRaw('UpdateGradeStudent (year(CURDATE()),year(created_at),month(CURDATE()),month(created_at),st_grade) as grade')->withTrashed()->first();
        return response()->json($userClient);
    }

    public function updateTookIA(Request $request)
    {
        // if($request->header('crm_authorization') != env('CRM_AUTHORIZATION_KEY')){
        //     return response()->json([
        //         'error' => 'Unauthorized'
        //     ], status: JsonResponse::HTTP_UNAUTHORIZED);
        // }

        # NEW CRM client id convert to UUID
        $id = $request->uuid;
        Log::debug($id . 'trying to update initial assessment');

        $rules = [
            'id' => 'required|exists:tbl_client,id'
        ];

        $validator = Validator::make(['id' => $id], $rules);

        # threw error if validation fails
        if ($validator->fails()) {
	    Log::warning('Failed update took ia, error validation: ' . json_encode($validator->errors()));
            return response()->json([
                'success' => false,
                'error' => $validator->errors()
            ]);
        }

        DB::beginTransaction();
        try {

            $client =  $this->clientRepository->updateClient($id, ['took_ia' => 1, 'took_ia_date' => Carbon::now()]);

            $response = [
                'success' => true,
                'message' => 'Successfully update took ia'
            ];

            Log::notice('Successfully update took ia ' . $client->full_name);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update took ia ' . $e->getMessage() . ' | On Line: ' . $e->getLine());
            return response()->json([
                'success' => false,
                'error' => 'Failed to update took ia' . $e->getMessage()
            ], 500);
        }

        return response()->json($response);
    }

    public function showMentorTutor($uuid)
    {
        $user = \App\Models\User::where('id', $uuid)->select('password')->first();
        if ( !$user )
        {
            return response()->json([
                'success' => false,
                'error' => 'Cannot find the user.'
            ]);
        } 
        return response()->json($user);
    }

    public function fnGetUserByRole(string $role)
    {
        $users = \App\Models\User::whereHas('roles', function ($query) use ($role) {
            $query->where('role_name', $role);
        })->get();
        return response()->json($users);
    }

    public function fnGetUserByRoleAndUUID(string $role, string $uuid)
    {
        # Added 'with (user_type)' for editing platform -> get contract date editor
        // $users = \App\Models\User::with(['user_type'])
        $users = \App\Models\User::with(['user_type' => function($query){
            $query->orderBy('tbl_user_type_detail.id', 'desc');
        }])
        ->whereHas('roles', function ($query) use ($role, $uuid) {
            $query->where('role_name', $role);
        })->where('id', $uuid)->first();
        return response()->json($users);
    }

    /**
     * Mentoring
     */
    public function fnGetMenteeDetails(Request $request): JsonResponse
    {
        $requested_mentee_id = $request->route('user_client');
        $details = $this->clientRepository->getClientById($requested_mentee_id);
        $response_of_student_information = [
            'mentee_id' => $details->id,
            'mentee_name' => $details->first_name . ' ' . $details->last_name,
            'mentee_phone' => $details->phone,
            'mentee_email' => $details->mail,
            'grade' => $details->grade_now,
            'application_year' => null,
            'address' => [
                'detail' => $details->address,
                'city' => $details->city,
            ],
            'birthdate' => $details->dob,
            'parent_name' => $details->parents()->select(['first_name', 'last_name', 'mail', 'phone'])->get()->toArray() 
        ];

        $response_of_student_mentor = array();
        // foreach ($details->clientProgram as $client_program) {
        //     foreach ($client_program->clientMentor as $client_mentor) {
        //         array_push($response_of_student_mentor, [
        //             'user_id' => $client_mentor->id,
        //             'mentor_name' => $client_mentor->first_name . ' ' . $client_mentor->last_name,
        //             'act_as' => $this->translateType($client_mentor->pivot->type),
        //         ]);
        //     }
        // }

        $response = array_merge($response_of_student_information, $response_of_student_mentor);

        return response()->json($response);
    }

    public function fnGetGraduatedMentee(Request $request)
    {
        $terms = $request->get('terms');
        $uni = $request->get('uni');
        $major = $request->get('major');
        $search = compact('terms', 'uni', 'major');
        $graduated_mentees = $this->clientRepository->rnGetGraduatedMentees($search);
        return response()->json($graduated_mentees);
    }

    public function fnGetActiveMentee(Request $request)
    {
        $terms = $request->get('terms');
        $search = compact('terms');
        $active_mentees = $this->clientRepository->rnGetActiveMentees($search);
        return response()->json($active_mentees);
    }

    public function fnGetMentorsByMentee(UserClient $user_client): JsonResponse
    {
        $latest_admission_program = $user_client->clientProgram()->whereRelation('program.main_prog', 'prog_name', 'Admissions Mentoring')->latest()->first();
        $mentors = $latest_admission_program->clientMentor()->where('tbl_client_mentor.status', 1)->get();
        $mapped_mentors = $mentors->map(function ($item) {
            return [
                'mentor_id' => $item->id,
                'mentor_name' => $item->first_name . ' ' . $item->last_name,
                'act_as' => $this->translateType($item->pivot->type)
            ];
        });
        return response()->json($mapped_mentors);
    }

    public function fnGetJoinedProgramsByMentee(UserClient $user_client)
    {
        $program_besides_admissions = $user_client->clientProgram()->whereRelation('program.main_prog', 'prog_name', '!=', 'Admissions Mentoring')->has('invoice.receipt')->get();
        $mapped_program = $program_besides_admissions->map(function ($item) {
            return [
                'clientprog_id' => $item->clientprog_id,
                'main_program' => $item->program->main_prog->prog_name,
                'sub_program' => $item->program->sub_prog->sub_prog_name,
                'program_name' => $item->program->prog_program,
                'success_date' => $item->success_date,
                'status' => $this->translate($item->prog_running_status)
            ];
        }); 
        return response()->json($mapped_program);
    }

    public function fnUpdateMenteeGDriveLink(
        UserClient $user_client, 
        UpdateMenteeGDriveRequest $request,
        LogService $log_service
        )
    {
        $validated = $request->safe()->only(['gdrive_link']);
        DB::beginTransaction();
        try {
            $user_client->mentoring_google_drive_link = $validated['gdrive_link'];
            $user_client->save();
            DB::commit();
        } catch (Exception $err) {
            DB::rollBack();
            $log_service->createErrorLog(LogModule::UPDATE_MENTEE_GDRIVE, $err->getMessage(), $err->getLine(), $err->getFile(), $validated);
            throw new HttpResponseException(
                response()->json(['errors' => 'Failed to update gdrive link'], JsonResponse::HTTP_BAD_REQUEST)
            );
        }
        $log_service->createSuccessLog(LogModule::UPDATE_MENTEE_GDRIVE, 'The gdrive link has been updated', $validated);
        return response()->json([
            'message' => 'Mentee gdrive has been updated'
        ]);
    }

    public function fnGetPackagesBoughtByMentee(
        UserCLient $user_client        
        )
    {
        try {
            $mapped_packages_bought = [];
            $packages_bought = $user_client->clientProgram()->whereRelation('program.main_prog', 'prog_name', 'Admissions Mentoring')->latest()->has('phase_detail')->get();

            if(count($packages_bought) > 0){

                $mapped_packages_bought = $packages_bought->map(function($item){

                    $mapped_phase_detail = $item->phase_detail->map(function($item) {
                        return [
                            'phase_detail_id' => $item->id,
                            'phase_detail_name' => $item->phase_detail_name,
                            'allocate' => $item->pivot->quota,
                            'use' => $item->pivot->use
                        ];
                    });
                    return $mapped_phase_detail;
                });
                
            }

            return response()->json(count($mapped_packages_bought) > 0 ? $mapped_packages_bought->first() : $mapped_packages_bought);
        } catch (Exception $err) {

            throw new HttpResponseException(
                response()->json(['errors' => 'Failed to get packages bought'], JsonResponse::HTTP_BAD_REQUEST)
            );
        }
    }
}
