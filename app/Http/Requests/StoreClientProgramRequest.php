<?php

namespace App\Http\Requests;

use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\ProgramRepositoryInterface;
use App\Models\ClientProgram;
use App\Models\Lead;
use App\Models\Program;
use App\Models\User;
use App\Models\UserClient;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClientProgramRequest extends FormRequest
{

    private ClientRepositoryInterface $clientRepository;
    private ProgramRepositoryInterface $programRepository;
    private ClientProgramRepositoryInterface $clientProgramRepository;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function __construct(ClientRepositoryInterface $clientRepository, ProgramRepositoryInterface $programRepository, ClientProgramRepositoryInterface $clientProgramRepository)
    {
        $this->admission_prog_id = Program::whereHas('main_prog', function($query) {
                                        $query->where('prog_name', 'Admissions Mentoring');
                                    })->orWhereHas('sub_prog', function ($query) {
                                        $query->where('sub_prog_name', 'Admissions Mentoring');
                                    })->pluck('prog_id')->toArray();

        $this->tutoring_prog_id = Program::whereHas('main_prog', function($query) {
                                        $query->where('prog_name', 'Academic & Test Preparation');
                                    })->orWhereHas('sub_prog', function ($query) {
                                        $query->where('sub_prog_name', 'like', '%Tutoring%');
                                    })->pluck('prog_id')->toArray();
                                    
        $this->clientRepository = $clientRepository;
        $this->programRepository = $programRepository;
        $this->clientProgramRepository = $clientProgramRepository;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {

        $admission_prog_id = Program::whereHas('main_prog', function($query) {
                                $query->where('prog_name', 'Admissions Mentoring');
                            })->orWhereHas('sub_prog', function ($query) {
                                $query->where('sub_prog_name', 'Admissions Mentoring');
                            })->pluck('prog_id')->toArray();

        $tutoring_prog_id = Program::whereHas('sub_prog', function ($query) {
                                $query->where('sub_prog_name', 'like', '%Tutoring%');
                            })->pluck('prog_id')->toArray();

        $satact_prog_id = Program::whereHas('sub_prog', function ($query) {
                                $query->where('sub_prog_name', 'like', '%SAT%')->orWhere('sub_prog_name', 'like', '%ACT%');
                            })->pluck('prog_id')->toArray();

        $studentId = $this->route('student');
        $student = $this->clientRepository->getClientById($studentId);
        $isMentee = $student->roles()->where('role_name', 'like', '%mentee%')->count();

        $clientProg = $this->clientProgramRepository->getClientProgramById($this->route('program'));
        $hasInvoice = isset($clientProg->invoice) ? $clientProg->invoice()->count() : 0;
        $hasReceipt = isset($clientProg->invoice->receipt) ? $clientProg->invoice->receipt()->count() : 0;

        if ($this->input('status') === null) {

            return [
                'prog_id' => 'required|exists:tbl_prog,prog_id',
                'lead_id' => 'required',
                // 'referral_code' => 'required_if:lead_id,LS005',
                'referral_code' => 'nullable',
                'first_discuss_date' => 'required|date',
                'meeting_notes' => 'nullable',
                'status' => 'required|in:0,1,2,3',
                'empl_id' => [
                    'required', 'required',
                    function ($attribute, $value, $fail) {
                        if (!User::with('roles')->whereHas('roles', function ($q) {
                            $q->where('role_name', 'Employee');
                        })->find($value)) {
                            $fail('The submitted pic was invalid employee');
                        }
                    },
                ]
            ];

        }

        # when program name is admission mentoring and status is pending
        switch ($this->input('status')) {
            
            # pending
            case 0:

                if (in_array($this->input('prog_id'), $admission_prog_id))
                    $rules = $this->store_admission_pending($isMentee);
                elseif (in_array($this->input('prog_id'), $tutoring_prog_id))
                    $rules = $this->store_tutoring_pending($isMentee);
                else {
                    $rules = [
                        'prog_id' => [
                            'required',
                            'exists:tbl_prog,prog_id',
                            function ($attribute, $value, $fail) use ($isMentee) {
                                $program = $this->programRepository->getProgramById($value);
                                if ($program->prog_scope == "mentee" && $isMentee == 0)
                                    $fail("This program is for mentee only");
                            }
                        ],
                        'lead_id' => 'required',
                        // 'referral_code' => 'required_if:lead_id,LS005',
                        'referral_code' => 'nullable',
                        'first_discuss_date' => 'required|date',
                        'meeting_notes' => 'nullable',
                        'status' => 'required|in:0,1,2,3',
                        'empl_id' => [
                            'required',
                            function ($attribute, $value, $fail) {
                                if (!User::with('roles')->whereHas('roles', function ($q) {
                                    $q->where('role_name', 'Employee');
                                })->find($value)) {
                                    $fail('The submitted pic was invalid employee');
                                }
                            },
                        ]
                    ];
                }

                break;

            # success
            case 1:
                
                if (in_array($this->input('prog_id'), $admission_prog_id)) {

                    $rules = $this->store_admission_success($isMentee, $studentId);

                    $rules['status'] = [
                        'required',
                        'in:0,1,2,3',
                        function ($attribute, $value, $fail) use ($clientProg) {
                            $studentId = $this->route('student');
                            $student = UserClient::find($studentId);

                            if (($student->mail == NULL || $student->mail == '') && ($student->phone == NULL || $student->phone == ''))
                                $fail('Not able to change status to success. Please complete student\'s email and phone number.');
    
                            if ($student->parents()->count() == 0)
                                $fail('Not able to change status to success. Please complete the parent\'s information');

                            if (isset($clientProg) && $clientProg->status == 3) 
                                $fail('Not able to change status to success. This activities has marked as "refunded" ');
    
                        }
                    ];

                } elseif (in_array($this->input('prog_id'), $tutoring_prog_id)){
                    $rules = $this->store_tutoring_success($isMentee);
                }elseif (in_array($this->input('prog_id'), $satact_prog_id)){
                    $rules = $this->store_satact_success($isMentee, $studentId);
                }

                
                break;

            # failed
            case 2:
                $rules = [
                    'prog_id' => [
                        'required',
                        'exists:tbl_prog,prog_id',
                        function ($attribute, $value, $fail) use ($isMentee) {
                            $program = $this->programRepository->getProgramById($value);
                            if ($program->prog_scope == "mentee" && $isMentee == 0)
                                $fail("This program is for mentee only");
                        }
                    ],
                    'lead_id' => 'required',
                    // 'referral_code' => 'required_if:lead_id,LS005',
                    'referral_code' => 'nullable',
                    'clientevent_id' => 'required_if:lead_id,LS003',
                    'eduf_lead_id' => 'required_if:lead_id,LS018',
                    'kol_lead_id' => [
                        function ($attribute, $value, $fail) {
                            if ($this->input('lead_id') == 'kol' && empty($value))
                                $fail('The KOL name field is required');

                            if (!Lead::where('main_lead', 'KOL')->where('lead_id', $value)->get()) 
                                $fail('The KOL name is invalid');
                        }
                    ],
                    'partner_id' => 'required_if:lead_id,LS010',
                    'first_discuss_date' => 'required|date',
                    'meeting_notes' => 'nullable',
                    'status' => 'required|in:0,1,2,3',
                    'empl_id' => [
                        'required', 'required',
                        function ($attribute, $value, $fail) {
                            if (!User::with('roles')->whereHas('roles', function ($q) {
                                $q->where('role_name', 'Employee');
                            })->find($value)) {
                                $fail('The submitted pic was invalid employee');
                            }
                        },
                    ],
                    'failed_date' => 'required',
                    'reason_id' => 'required_if:other_reason,null',
                    'other_reason' => 'required_if:reason_id,=,null'
                ];
                break;

            # refund
            case 3:
                $rules = [
                    'prog_id' => [
                        'required',
                        'exists:tbl_prog,prog_id',
                        function ($attribute, $value, $fail) use ($isMentee) {
                            $program = $this->programRepository->getProgramById($value);
                            if ($program->prog_scope == "mentee" && $isMentee == 0)
                                $fail("This program is for mentee only");
                        }
                    ],
                    'lead_id' => 'required',
                    // 'referral_code' => 'required_if:lead_id,LS005',
                    'referral_code' => 'nullable',
                    'clientevent_id' => 'required_if:lead_id,LS003',
                    'eduf_lead_id' => 'required_if:lead_id,LS018',
                    'kol_lead_id' => [
                        function ($attribute, $value, $fail) {
                            if ($this->input('lead_id') == 'kol' && empty($value))
                                $fail('The KOL name field is required');

                            if (!Lead::where('main_lead', 'KOL')->where('lead_id', $value)->get()) 
                                $fail('The KOL name is invalid');
                        }
                    ],
                    'partner_id' => 'required_if:lead_id,LS010',
                    'first_discuss_date' => 'required|date',
                    'meeting_notes' => 'nullable',
                    'status' => [
                        'required',
                        'in:0,1,2,3',
                        function ($attribute, $value, $fail) use ($hasInvoice, $hasReceipt) {

                            if ((int)$hasInvoice > 0 && (int)$hasReceipt == 0) 
                                $fail("Looks like this program has not been paid");
                        }
                    ],
                    'empl_id' => [
                        'required', 'required',
                        function ($attribute, $value, $fail) {
                            if (!User::with('roles')->whereHas('roles', function ($q) {
                                $q->where('role_name', 'Employee');
                            })->find($value)) {
                                $fail('The submitted pic was invalid employee');
                            }
                        },
                    ],
                    'refund_date' => 'required',
                    'refund_notes' => 'nullable',
                    'reason_id' => 'required_if:other_reason,null',
                    'other_reason' => 'required_if:reason_id,=,null'
                ];
                break;
        }

        if ($this->input('lead_id') != "kol") {
            $rules['lead_id'] = 'required|exists:tbl_lead,lead_id';
        }

        return $rules;
    }

    public function store_admission_pending($isMentee)
    {
        $rules = [
            'prog_id' => [
                'required',
                'exists:tbl_prog,prog_id',
                function ($attribute, $value, $fail) use ($isMentee) {
                    $program = $this->programRepository->getProgramById($value);
                    if ($program->prog_scope == "mentee" && $isMentee == 0)
                        $fail("This program is for mentee only");
                }
            ],
            'lead_id' => 'required',
            // 'referral_code' => 'required_if:lead_id,LS005',
            'referral_code' => 'nullable',
            'clientevent_id' => 'required_if:lead_id,LS003',
            'eduf_lead_id' => 'required_if:lead_id,LS018',
            'kol_lead_id' => [
                function ($attribute, $value, $fail) {
                    if ($this->input('lead_id') == 'kol' && empty($value))
                        $fail('The KOL name field is required');

                    if (!Lead::where('main_lead', 'KOL')->where('lead_id', $value)->get()) 
                        $fail('The KOL name is invalid');
                }
            ],
            'partner_id' => 'required_if:lead_id,LS010',
            'first_discuss_date' => 'required|date',
            'meeting_notes' => 'nullable',
            'status' => 'required|in:0,1,2,3',
            'pend_initconsult_date' => 'nullable|date|after_or_equal:first_discuss_date',
            'pend_assessmentsent_date' => 'nullable|date|after_or_equal:pend_initconsult_date',
            'pend_mentor_ic' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if (!User::with('roles')->whereHas('roles', function ($q) {
                        $q->where('role_name', 'Mentor');
                    })->find($value)) {
                        $fail('The submitted mentor was invalid mentor');
                    }
                },
            ],
            'empl_id' => [
                'required', 'required',
                function ($attribute, $value, $fail) {
                    if (!User::with('roles')->whereHas('roles', function ($q) {
                        $q->where('role_name', 'Employee');
                    })->find($value)) {
                        $fail('The submitted pic was invalid employee');
                    }
                },
            ]
        ];

        if ($this->input('pend_assessmentsent_date') != NULL)
            $rules['pend_initconsult_date'] .= '|before_or_equal:pend_assessmentsent_date';
        
        return $rules;
    }

    public function store_admission_success($isMentee, $studentId)
    {
        $validate = [
            'prog_id' => [
                'required', 
                'exists:tbl_prog,prog_id',
                function ($attribute, $value, $fail) use ($isMentee) {
                    $program = $this->programRepository->getProgramById($value);
                    if ($program->prog_scope == "mentee" && $isMentee == 0)
                        $fail("This program is for mentee only");
                }
            ],
            'lead_id' => 'required',
            // 'referral_code' => 'required_if:lead_id,LS005',
            'referral_code' => 'nullable',
            'clientevent_id' => 'required_if:lead_id,LS003',
            'eduf_lead_id' => 'required_if:lead_id,LS018',
            'kol_lead_id' => [
                function ($attribute, $value, $fail) {
                    if ($this->input('lead_id') == 'kol' && empty($value))
                        $fail('The KOL name field is required');

                    if (!Lead::where('main_lead', 'KOL')->where('lead_id', $value)->get()) 
                        $fail('The KOL name is invalid');
                }
            ],
            'partner_id' => 'required_if:lead_id,LS010',
            'first_discuss_date' => 'required|date',
            'meeting_notes' => 'nullable',
            // 'status' => 'required|in:0,1,2,3',
            'empl_id' => [
                'required', 'required',
                function ($attribute, $value, $fail) {
                    if (!User::with('roles')->whereHas('roles', function ($q) {
                        $q->where('role_name', 'Employee');
                    })->find($value)) {
                        $fail('The submitted pic was invalid employee');
                    }
                },
            ],
            'success_date' => 'required_if:status,1|after_or_equal:assessmentsent_date',
            'initconsult_date' => 'required',
            'assessmentsent_date' => 'required', # update v1.4 : <= 1.3 required
            'mentoring_prog_end_date' => 'required|date|after_or_equal:success_date',
            'total_uni' => 'required|numeric',
            'total_foreign_currency' => 'required|numeric',
            'foreign_currency_exchange' => 'required|numeric',
            'total_idr' => 'required|numeric',
            'main_mentor' => [
                function ($attribute, $value, $fail) {
                    if (!User::with('roles')->whereHas('roles', function ($q) {
                        $q->where('role_name', 'Mentor');
                    })->find($value)) {
                        $fail('The submitted mentor was invalid mentor');
                    }
                },
                'required_if:status,1',
                'different:backup_mentor',
            ],
            'backup_mentor' => [
                function ($attribute, $value, $fail) use ($studentId) {
                    if (!User::with('roles')->whereHas('roles', function ($q) {
                        $q->where('role_name', 'Mentor');
                    })->find($value)) {
                        $fail('The submitted mentor was invalid mentor');
                    }

                    if (UserClient::whereHas('clientMentor', function($query) use ($value) {
                        $query->where('users.id', $value);
                    })->where('id', $studentId)->count() > 0) {
                        $fail('The choosen backup mentor has already exist');
                    }
                },
                'nullable',
                // 'required_if:status,1',
                'different:main_mentor'
            ],
            'mentor_ic' => [
                function ($attribute, $value, $fail) {
                    if (!User::with('roles')->whereHas('roles', function ($q) {
                        $q->where('role_name', 'Mentor');
                    })->find($value)) {
                        $fail('The submitted mentor was invalid mentor');
                    }
                },
                'required_if:status,1',
            ],
            'installment_notes' => 'nullable',
            'agreement' => 'nullable', #mimes:pdf
            'prog_running_status' => 'required',
        ];

        # if client program will be created
        if ($this->isMethod('POST')) {
            $validate['agreement'] = 'required|mimes:pdf';
        }

        # if client program will be updated and the agreement still nullable
        if ($this->isMethod('PUT')) {
            $clientprog_id = $this->route('program');
            $clientProg = ClientProgram::whereClientProgramId($clientprog_id);
            if ($clientProg->agreement == 'NULL')
                $validate['agreement'] = 'required|mimes:pdf';
        }

        return $validate;
    }

    public function store_tutoring_pending($isMentee)
    {
        return [
            'prog_id' => [
                'required',
                'exists:tbl_prog,prog_id',
                function ($attribute, $value, $fail) use ($isMentee) {
                    $program = $this->programRepository->getProgramById($value);
                    if ($program->prog_scope == "mentee" && $isMentee == 0)
                        $fail("This program is for mentee only");
                }
            ],
            'lead_id' => 'required',
            // 'referral_code' => 'required_if:lead_id,LS005',
            'referral_code' => 'nullable',
            'clientevent_id' => 'required_if:lead_id,LS003',
            'eduf_lead_id' => 'required_if:lead_id,LS018',
            'kol_lead_id' => [
                function ($attribute, $value, $fail) {
                    if ($this->input('lead_id') == 'kol' && empty($value))
                        $fail('The KOL name field is required');

                    if (!Lead::where('main_lead', 'KOL')->where('lead_id', $value)->get()) 
                        $fail('The KOL name is invalid');
                }
            ],
            'partner_id' => 'required_if:lead_id,LS010',
            'first_discuss_date' => 'required|date',
            'meeting_notes' => 'nullable',
            'status' => 'required|in:0,1,2,3',
            'pend_trial_date' => 'required|date',
            'empl_id' => [
                'required', 'required',
                function ($attribute, $value, $fail) {
                    if (!User::with('roles')->whereHas('roles', function ($q) {
                        $q->where('role_name', 'Employee');
                    })->find($value)) {
                        $fail('The submitted pic was invalid employee');
                    }
                },
            ]
        ];
    }

    public function store_tutoring_success($isMentee)
    {
        $invoice_exist = $this->input('invoice_exist');
        $extended_rules = [];
        $rules = [
            'prog_id' => [
                'required',
                'exists:tbl_prog,prog_id',
                function ($attribute, $value, $fail) use ($isMentee) {
                    $program = $this->programRepository->getProgramById($value);
                    if ($program->prog_scope == "mentee" && $isMentee == 0)
                        $fail("This program is for mentee only");
                }
            ],
            'lead_id' => 'required',
            // 'referral_code' => 'required_if:lead_id,LS005',
            'referral_code' => 'nullable',
            'clientevent_id' => 'required_if:lead_id,LS003',
            'eduf_lead_id' => 'required_if:lead_id,LS018',
            'kol_lead_id' => [
                function ($attribute, $value, $fail) {
                    if ($this->input('lead_id') == 'kol' && empty($value))
                        $fail('The KOL name field is required');

                    if (!Lead::where('main_lead', 'KOL')->where('lead_id', $value)->get()) 
                        $fail('The KOL name is invalid');
                }
            ],
            'partner_id' => 'required_if:lead_id,LS010',
            'first_discuss_date' => 'required|date',
            'meeting_notes' => 'nullable',
            // 'status' => 'required|in:0,1,2,3',
            'empl_id' => [
                'required', 'required',
                function ($attribute, $value, $fail) {
                    if (!User::with('roles')->whereHas('roles', function ($q) {
                        $q->where('role_name', 'Employee');
                    })->find($value)) {
                        $fail('The submitted pic was invalid employee');
                    }
                },
            ],
            'success_date' => 'required',
            'trial_date' => 'required|date',
            // 'first_class' => 'required|date',
            'prog_start_date' => 'required|date',
            'prog_end_date' => 'required|date|after_or_equal:prog_start_date',
            'timesheet_link' => 'required|url',
            'tutor_id' => [
                'required_if:status,1',
                function ($attribute, $value, $fail) {

                    if (!User::with('roles')->whereHas('roles', function ($q) {
                        $q->where('role_name', 'Tutor');
                    })->find($value)) {
                        $fail('The submitted tutor was invalid tutor');
                    }
                },
            ],
            'prog_running_status' => 'required',
        ];

        // if ($invoice_exist) {
        //     $extended_rules = [
        //         'session' => 'required',
        //         'sessionDetail.*' => 'required',
        //         'sessionLinkMeet.*' => 'required|url',
        //     ];
        // }

        $rules = array_merge($rules, $extended_rules);
        return $rules;
    }

    public function store_satact_success($isMentee, $studentId)
    {
        return [
            'prog_id' => [
                'required', 
                'exists:tbl_prog,prog_id',
                function ($attribute, $value, $fail) use ($isMentee) {
                    $program = $this->programRepository->getProgramById($value);
                    if ($program->prog_scope == "mentee" && $isMentee == 0)
                        $fail("This program is for mentee only");
                }
            ],
            'lead_id' => 'required',
            // 'referral_code' => 'required_if:lead_id,LS005',
            'referral_code' => 'nullable',
            'clientevent_id' => 'required_if:lead_id,LS003',
            'eduf_lead_id' => 'required_if:lead_id,LS018',
            'kol_lead_id' => [
                function ($attribute, $value, $fail) {
                    if ($this->input('lead_id') == 'kol' && empty($value))
                        $fail('The KOL name field is required');

                    if (!Lead::where('main_lead', 'KOL')->where('lead_id', $value)->get()) 
                        $fail('The KOL name is invalid');
                }
            ],
            'partner_id' => 'required_if:lead_id,LS010',
            'first_discuss_date' => 'required|date',
            'meeting_notes' => 'nullable',
            // 'status' => 'required|in:0,1,2,3',
            'empl_id' => [
                'required', 'required',
                function ($attribute, $value, $fail) {
                    if (!User::with('roles')->whereHas('roles', function ($q) {
                        $q->where('role_name', 'Employee');
                    })->find($value)) {
                        $fail('The submitted pic was invalid employee');
                    }
                },
            ],
            'success_date' => 'required',
            'test_date' => 'required|date',
            'first_class' => 'required|date',
            'last_class' => 'required|date',
            'diag_score' => 'required|numeric',
            'test_score' => 'required|numeric',
            'tutor_1' => [
                'required_if:status,1',
                function ($attribute, $value, $fail) {

                    if (!User::with('roles')->whereHas('roles', function ($q) {
                        $q->where('role_name', 'Tutor');
                    })->find($value)) {
                        $fail('The submitted tutor was invalid tutor');
                    }
                },
            ],
            'tutor_2' => [
                'nullable',
                function ($attribute, $value, $fail) use ($studentId) {

                    if (!User::with('roles')->whereHas('roles', function ($q) {
                        $q->where('role_name', 'Tutor');
                    })->find($value)) {
                        $fail('The submitted tutor was invalid tutor');
                    }

                    if (UserClient::whereHas('clientMentor', function($query) use ($value) {
                        $query->where('users.id', $value);
                    })->where('id', $studentId)->count() > 0) {
                        $fail('The choosen tutor has already exist');
                    }
                },
            ],
            'timesheet_1' => 'required_if:tutor_1,!=,null',
            'timesheet_2' => 'required_if:tutor_2,!=,null',
            'prog_running_status' => 'required',
        ];
    }
}
