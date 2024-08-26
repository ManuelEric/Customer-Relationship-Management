<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Interfaces\ClientEventRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use Illuminate\Http\Request;

class ClientEventController extends Controller
{
    protected ClientEventRepositoryInterface $clientEventRepository;
    protected ClientRepositoryInterface $clientRepository;

    public function __construct(ClientEventRepositoryInterface $clientEventRepository, ClientRepositoryInterface $clientRepository)
    {
        $this->clientEventRepository = $clientEventRepository;
        $this->clientRepository = $clientRepository;
    }

    public function findClientEvent(Request $request)
    {
        # initiate base variables
        $requestedScreeningType = strtoupper($request->route('screening_type'));
        $allowableScreeningType = ['CE', 'PH', 'TKT'];
        $requestedIdentifier = $request->route('identifier'); # can be clientevent_id or phone_number or ticket id


        # validation based on identifier
        if (!in_array($requestedScreeningType, $allowableScreeningType)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid screening type.',
            ]);
        }

        # validation based on value of the identifier
        switch ($requestedScreeningType) {

            # check clientevent_id
            case 'CE':
                if (!$foundClientevent = $this->clientEventRepository->getClientEventById($requestedIdentifier)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Could not find the client event.'
                    ]);
                }
                break;

            # check phone number of both user (parent or student) if any
            case 'PH':
                # if the identifier is phone_number then parameter event id should be passes
                $requestedEventId = $request->get('EVT');
                
                if (!$foundClient = $this->clientRepository->getClientByPhoneNumber($requestedIdentifier))
                    return response()->json(['success' => false, 'message' => "We apologize, but we couldn't locate an account associated with the information provided. Please double-check your credentials or create a new account."]);


                $foundClientevent = $foundClient->clientEvent()->where('event_id', $requestedEventId)->first();
                if (!$foundClientevent)
                    return response()->json(['success' => false, 'message' => "It seems we don't have your information on record yet. To provide you with the best possible service, could you please register first? It's quick and easy!"]);

                break;

            # find a client event data using ticket ID
            case 'TKT':
                $foundClientevent = $this->clientEventRepository->getClientEventByTicketId($requestedIdentifier);
                if (!$foundClientevent)
                    return response()->json(['success' => false, 'message' => "The system isn't recognizing the ticket ID you entered. Would you like to try again?"]);

                break;

        }
        

        # create an array of information that need to be brought up to front-end
        $informations = $this->createResponse($foundClientevent);

        return response()->json([
            'success' => true,
            'message' => 'Client event was found.',
            'data' => $informations
        ]);
        
    }

    private function createResponse(object $foundClientevent)
    {
        # first we need to create the general information
        $roleAndHaveChild = app('App\Http\Controllers\Api\v1\ExtClientController')->getRole($foundClientevent);
        $informations = [
            'role' => $roleAndHaveChild['role'],
            'is_vip' => $foundClientevent->notes == 'vip' ? true : false,
            'scholarship' => $foundClientevent->client->scholarship,
            'have_child' => $roleAndHaveChild['have_child'] ?? false,
            'lead' => [
                'lead_id' => $foundClientevent->client->lead_id,
                'lead_name' => isset($foundClientevent->client->lead->lead_name) ? $foundClientevent->client->lead->lead_name : null,
            ],
            'joined_event' => [
                'clientevent_id' => $foundClientevent->clientevent_id,
                'event_id' => $foundClientevent->event_id,
                'event_name' => $foundClientevent->event->event_title,
                'attend_status' => $foundClientevent->status,
                'attend_party' => $foundClientevent->number_of_attend,
                'event_type' => 'offline',
                'status' => $foundClientevent->registration_type,
                'referral' => $foundClientevent->referral_code,
                'client_type' => $foundClientevent->notes,
            ]
        ];

        # secondly we need to add client information but it depends on their role (previously was register_as) 
        # for example, if they are student then we will add student object, 
        # but when they are parent we will add the parent as well as the student.
        switch ($foundClientevent->client->roles->count() > 0) {

            case $foundClientevent->client->roles()->where('role_name', 'student')->exists():
                $clientInformation = [
                    'student' => [
                        'name' => $foundClientevent->client->full_name,
                        'first_name' => $foundClientevent->client->first_name,
                        'last_name' => $foundClientevent->client->last_name,
                        'mail' => $foundClientevent->client->mail,
                        'phone' => $foundClientevent->client->phone,
                    ],
                    'education' => [
                        'school_id' => $foundClientevent->client->sch_id,
                        'school_name' => isset($foundClientevent->client->school->sch_name) ? $foundClientevent->client->school->sch_name : null,
                        'graduation_year' => $foundClientevent->client->graduation_year,
                        'grade' => $foundClientevent->client->st_grade,
                    ],
                    'dreams_countries' => $foundClientevent->client->destinationCountries->map(function ($country) {
                            return [
                                'country_id' => $country->id,
                                'country_name' => $country->name
                            ];
                        }),
                ];
                
                break;

            case $foundClientevent->client->roles()->where('role_name', 'parent')->exists():
                
                # the point of make student information separated from parent
                # because of not all parents have a children which we're not gonna show the student information while the parent doesn't have a child

                # general parent information
                $clientInformation = [
                    'parent' => [
                        'name' => $foundClientevent->client->full_name,
                        'first_name' => $foundClientevent->client->first_name,
                        'last_name' => $foundClientevent->client->last_name,
                        'mail' => $foundClientevent->client->mail,
                        'phone' => $foundClientevent->client->phone,
                    ]
                ];

                # if the parent has children that attached to the client event
                if ($foundClientevent->children) {

                    $studentInformation = [
                        'student' => [
                            'name' => $foundClientevent->children->full_name,
                            'first_name' => $foundClientevent->children->first_name,
                            'last_name' => $foundClientevent->children->last_name,
                            'mail' => $foundClientevent->children->mail,
                            'phone' => $foundClientevent->children->phone,
                        ],
                        'education' => [
                            'school_id' => $foundClientevent->children->sch_id,
                            'school_name' => $foundClientevent->children->school->sch_name,
                            'graduation_year' => $foundClientevent->children->graduation_year,
                            'grade' => $foundClientevent->children->st_grade,
                        ],
                        'dreams_countries' => $foundClientevent->children->destinationCountries->map(function ($country) {
                                return [
                                    'country_id' => $country->id,
                                    'country_name' => $country->name
                                ];
                            }),
                    ];

                    $clientInformation = array_merge($clientInformation, $studentInformation);

                }

                break;

            case $foundClientevent->client->roles()->where('role_name', 'Teacher/Counselor')->exists():
                $clientInformation = [
                    'teacher/counsellor' => [
                        'name' => $foundClientevent->client->full_name,
                        'first_name' => $foundClientevent->client->first_name,
                        'last_name' => $foundClientevent->client->last_name,
                        'mail' => $foundClientevent->client->mail,
                        'phone' => $foundClientevent->client->phone,
                    ],
                    'education' => [
                        'school_id' => $foundClientevent->client->sch_id,
                        'school_name' => isset($foundClientevent->client->school->sch_name) ? $foundClientevent->client->school->sch_name : null,
                    ],
                ];
                break;

            default:
                $clientInformation = [];

        }

        return array_merge($informations, $clientInformation);
    }
}
