<?php

namespace App\Http\Requests;

use App\Models\Lead;
use App\Models\Program;
use App\Models\User;
use App\Models\UserClient;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClientProgramRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function __construct()
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


        if ($this->input('status') === null) {

            return [
                'prog_id' => 'required|exists:tbl_prog,prog_id',
                'lead_id' => 'required',
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
                    $rules = $this->store_admission_pending();
                elseif (in_array($this->input('prog_id'), $tutoring_prog_id))
                    $rules = $this->store_tutoring_pending();

                break;

            # success
            case 1:
                
                if (in_array($this->input('prog_id'), $admission_prog_id))
                    $rules = $this->store_admission_success();
                elseif (in_array($this->input('prog_id'), $tutoring_prog_id))
                    $rules = $this->store_tutoring_success();
                elseif (in_array($this->input('prog_id'), $satact_prog_id))
                    $rules = $this->store_satact_success();

                $rules['status'] = [
                    'required',
                    'in:0,1,2,3',
                    function ($attribute, $value, $fail) {
                        $studentId = $this->route('student');
                        $student = UserClient::find($studentId);

                        if ($student->parents()->count() == 0)
                            $fail('Not able change status to success. Please complete the parent\'s information');

                    }
                ];
                break;

            # failed
            case 2:
                $rules = [
                    'prog_id' => 'required|exists:tbl_prog,prog_id',
                    'lead_id' => 'required',
                    'clientevent_id' => 'required_if:lead_id,LS004',
                    'eduf_lead_id' => 'required_if:lead_id,LS018',
                    'kol_lead_id' => [
                        function ($attribute, $value, $fail) {
                            if ($this->input('lead_id') == 'kol' && empty($value))
                                $fail('The KOL name field is required');

                            if (!Lead::where('main_lead', 'KOL')->where('lead_id', $value)->get()) 
                                $fail('The KOL name is invalid');
                        }
                    ],
                    'partner_id' => 'required_if:lead_id,LS015',
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
                    'prog_id' => 'required|exists:tbl_prog,prog_id',
                    'lead_id' => 'required',
                    'clientevent_id' => 'required_if:lead_id,LS004',
                    'eduf_lead_id' => 'required_if:lead_id,LS018',
                    'kol_lead_id' => [
                        function ($attribute, $value, $fail) {
                            if ($this->input('lead_id') == 'kol' && empty($value))
                                $fail('The KOL name field is required');

                            if (!Lead::where('main_lead', 'KOL')->where('lead_id', $value)->get()) 
                                $fail('The KOL name is invalid');
                        }
                    ],
                    'partner_id' => 'required_if:lead_id,LS015',
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
                    'refund_date' => 'required',
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

    public function store_admission_pending()
    {
        return [
            'prog_id' => 'required|exists:tbl_prog,prog_id',
            'lead_id' => 'required',
            'clientevent_id' => 'required_if:lead_id,LS004',
            'eduf_lead_id' => 'required_if:lead_id,LS018',
            'kol_lead_id' => [
                function ($attribute, $value, $fail) {
                    if ($this->input('lead_id') == 'kol' && empty($value))
                        $fail('The KOL name field is required');

                    if (!Lead::where('main_lead', 'KOL')->where('lead_id', $value)->get()) 
                        $fail('The KOL name is invalid');
                }
            ],
            'partner_id' => 'required_if:lead_id,LS015',
            'first_discuss_date' => 'required|date',
            'meeting_notes' => 'nullable',
            'status' => 'required|in:0,1,2,3',
            'pend_initconsult_date' => 'required|date',
            'pend_assessmentsent_date' => 'required|date',
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

    public function store_admission_success()
    {
        return [
            'prog_id' => 'required|exists:tbl_prog,prog_id',
            'lead_id' => 'required',
            'clientevent_id' => 'required_if:lead_id,LS004',
            'eduf_lead_id' => 'required_if:lead_id,LS018',
            'kol_lead_id' => [
                function ($attribute, $value, $fail) {
                    if ($this->input('lead_id') == 'kol' && empty($value))
                        $fail('The KOL name field is required');

                    if (!Lead::where('main_lead', 'KOL')->where('lead_id', $value)->get()) 
                        $fail('The KOL name is invalid');
                }
            ],
            'partner_id' => 'required_if:lead_id,LS015',
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
            'success_date' => 'required_if:status,1',
            'initconsult_date' => 'required',
            'assessmentsent_date' => 'required',
            'mentoring_prog_end_date' => 'required',
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
                function ($attribute, $value, $fail) {
                    if (!User::with('roles')->whereHas('roles', function ($q) {
                        $q->where('role_name', 'Mentor');
                    })->find($value)) {
                        $fail('The submitted mentor was invalid mentor');
                    }

                    if (UserClient::whereHas('clientMentor', function($query) use ($value) {
                        $query->where('users.id', $value);
                    })->count() > 0) {
                        $fail('The choosen backup mentor has already exist');
                    }
                },
                'required_if:status,1',
                'different:main_mentor'
            ],
            'installment_notes' => 'nullable',
            'prog_running_status' => 'required',
        ];
    }

    public function store_tutoring_pending()
    {
        return [
            'prog_id' => 'required|exists:tbl_prog,prog_id',
            'lead_id' => 'required',
            'clientevent_id' => 'required_if:lead_id,LS004',
            'eduf_lead_id' => 'required_if:lead_id,LS018',
            'kol_lead_id' => [
                function ($attribute, $value, $fail) {
                    if ($this->input('lead_id') == 'kol' && empty($value))
                        $fail('The KOL name field is required');

                    if (!Lead::where('main_lead', 'KOL')->where('lead_id', $value)->get()) 
                        $fail('The KOL name is invalid');
                }
            ],
            'partner_id' => 'required_if:lead_id,LS015',
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

    public function store_tutoring_success()
    {
        return [
            'prog_id' => 'required|exists:tbl_prog,prog_id',
            'lead_id' => 'required',
            'clientevent_id' => 'required_if:lead_id,LS004',
            'eduf_lead_id' => 'required_if:lead_id,LS018',
            'kol_lead_id' => [
                function ($attribute, $value, $fail) {
                    if ($this->input('lead_id') == 'kol' && empty($value))
                        $fail('The KOL name field is required');

                    if (!Lead::where('main_lead', 'KOL')->where('lead_id', $value)->get()) 
                        $fail('The KOL name is invalid');
                }
            ],
            'partner_id' => 'required_if:lead_id,LS015',
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
            'prog_start_date' => 'required|date',
            'prog_end_date' => 'required|date|after:prog_start_date',
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
    }

    public function store_satact_success()
    {
        return [
            'prog_id' => 'required|exists:tbl_prog,prog_id',
            'lead_id' => 'required',
            'clientevent_id' => 'required_if:lead_id,LS004',
            'eduf_lead_id' => 'required_if:lead_id,LS018',
            'kol_lead_id' => [
                function ($attribute, $value, $fail) {
                    if ($this->input('lead_id') == 'kol' && empty($value))
                        $fail('The KOL name field is required');

                    if (!Lead::where('main_lead', 'KOL')->where('lead_id', $value)->get()) 
                        $fail('The KOL name is invalid');
                }
            ],
            'partner_id' => 'required_if:lead_id,LS015',
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
                function ($attribute, $value, $fail) {

                    if (!User::with('roles')->whereHas('roles', function ($q) {
                        $q->where('role_name', 'Tutor');
                    })->find($value)) {
                        $fail('The submitted tutor was invalid tutor');
                    }

                    if (UserClient::whereHas('clientMentor', function($query) use ($value) {
                        $query->where('users.id', $value);
                    })->count() > 0) {
                        $fail('The choosen tutor has already exist');
                    }
                },
            ],
            'prog_running_status' => 'required',
        ];
    }
}
