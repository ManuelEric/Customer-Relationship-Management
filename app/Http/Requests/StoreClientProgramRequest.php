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

        $tutoring_prog_id = Program::whereHas('main_prog', function($query) {
                                $query->where('prog_name', 'Academic & Test Preparation');
                            })->orWhereHas('sub_prog', function ($query) {
                                $query->where('sub_prog_name', 'like', '%Tutoring%');
                            })->pluck('prog_id')->toArray();

        $satact_prog_id = Program::whereHas('main_prog', function($query) {
                                $query->where('prog_name', 'Academic & Test Preparation');
                            })->orWhereHas('sub_prog', function ($query) {
                                $query->where('sub_prog_name', 'like', '%SAT%')->orWhere('sub_prog_name', 'like', '%ACT%');
                            })->pluck('prog_id')->toArray();

        $admission_tutoring_prog_id = array_merge($admission_prog_id, $tutoring_prog_id);

        # when program name is admission mentoring and status is pending
        if (in_array($this->input('prog_id'), $admission_prog_id) && $this->input('status') == 0) {
            
            return $this->store_admission_pending();
        
        } elseif (in_array($this->input('prog_id'), $tutoring_prog_id) && $this->input('status') == 0) {

            return $this->store_tutoring_pending();

        }


        $rules = [
            'prog_id' => 'required|exists:tbl_prog,prog_id',
            'event_id' => 'required_if:lead_id,LS004',
            'eduf_id' => 'required_if:lead_id,LS018',
            'kol_lead_id' => [
                function ($attribute, $value, $fail) {
                    if ($this->input('lead_id') == 'kol' && empty($value))
                        $fail('The KOL name field is required');

                    if (!Lead::where('main_lead', 'KOL')->where('lead_id', $value)->get()) 
                        $fail('The KOL name is invalid');
                }
            ],
            'corp_id' => 'required_if:lead_id,LS015',

            # basic info validation
            'first_discuss_date' => 'required|date',
            'meeting_notes' => 'nullable',
            'status' => 'required|in:0,1,2,3',
            'success_date' => 'required_if:status,1',
            'failed_date' => 'required_if:status,2',
            'refund_date' => 'required_if:status,3',
            'reason_id' => 'required_if:status,2,3',
            'other_reason' => 'required_if:reason_id,!=,NULL',

            # validation when program is admission mentoring
            'initconsult_date' => [
                function ($attribute, $value, $fail) use ($admission_prog_id) {
                    if (in_array($this->input('prog_id'), $admission_prog_id) && empty($value)) 
                        $fail('The initial consultation date field is required');
                },
                'required_if:status,0,1'
            ],
            'assessmentsent_date' => [
                function ($attribute, $value, $fail) use ($admission_prog_id) {
                    if (in_array($this->input('prog_id'), $admission_prog_id) && empty($value)) 
                        $fail('The assessment date field is required');
                },
                'required_if:status,0,1'
            ],
            'prog_end_date' => [
                function ($attribute, $value, $fail) use ($admission_tutoring_prog_id) {
                    if (in_array($this->input('prog_id'), $admission_tutoring_prog_id) && empty($value)) 
                        $fail('The program end date field is required');
                },
                'required_if:status,1'
            ],
            'total_uni' => [
                function ($attribute, $value, $fail) use ($admission_prog_id) {
                    if (in_array($this->input('prog_id'), $admission_prog_id) && empty($value)) 
                        $fail('The total universities field is required');
                },
                'numeric',
                'required_if:status,1'
            ],
            'total_foreign_currency' => [
                function ($attribute, $value, $fail) use ($admission_prog_id) {
                    if (in_array($this->input('prog_id'), $admission_prog_id) && empty($value)) 
                        $fail('The total foreign currency field is required');
                },
                'numeric',
                'required_if:status,1'
            ],
            'foreign_currency_exchange' => [
                function ($attribute, $value, $fail) use ($admission_prog_id) {
                    if (in_array($this->input('prog_id'), $admission_prog_id) && empty($value)) 
                        $fail('The foreign currency exchange field is required');
                },
                'numeric',
                'required_if:status,1'
            ],
            'total_idr' => [
                function ($attribute, $value, $fail) use ($admission_prog_id) {
                    if (in_array($this->input('prog_id'), $admission_prog_id) && empty($value)) 
                        $fail('The total rupiah field is required');
                },
                'numeric',
                'required_if:status,1'
            ],
            'main_mentor' => [
                function ($attribute, $value, $fail) use ($admission_prog_id) {
                    if (in_array($this->input('prog_id'), $admission_prog_id) && empty($value)) 
                        $fail('The main mentor field is required');

                    if (!User::with('roles')->whereHas('roles', function ($q) {
                        $q->where('role_name', 'Mentor');
                    })->find($value)) {
                        $fail('The submitted mentor was invalid mentor');
                    }
                },
                'required_if:status,1'
            ],
            'backup_mentor' => [
                function ($attribute, $value, $fail) use ($admission_prog_id) {
                    if (in_array($this->input('prog_id'), $admission_prog_id) && empty($value)) 
                        $fail('The backup mentor field is required');

                    if (!User::with('roles')->whereHas('roles', function ($q) {
                        $q->where('role_name', 'Mentor');
                    })->find($value)) {
                        $fail('The submitted mentor was invalid mentor');
                    }

                    // if (UserClient::whereHas('clientMentor', function($query) use ($value) {
                    //     $query->where('users.id', $value);
                    // })->count() > 0) {
                    //     $fail('The choosen backup mentor has already exist');
                    // }
                },
                'required_if:status,1'
            ],
            'installment_notes' => 'nullable',

            # validation when program is tutoring
            'trial_date' => [
                function ($attribute, $value, $fail) use ($tutoring_prog_id) {
                    if (in_array($this->input('prog_id'), $tutoring_prog_id) && empty($value)) 
                        $fail('The trial date field is required');
                },
                'required_if:status,0,1',
                'date',
            ],
            'prog_start_date' => [
                function ($attribute, $value, $fail) use ($tutoring_prog_id) {
                    if (in_array($this->input('prog_id'), $tutoring_prog_id) && empty($value)) 
                        $fail('The program start date field is required');
                },
                'required_if:status,1',
                'date',
            ],
            // 'prog_end_date' => [
            //     function ($attribute, $value, $fail) use ($tutoring_prog_id) {
            //         if (in_array($this->input('prog_id'), $tutoring_prog_id) && empty($value)) 
            //             $fail('The program end date field is required');
            //     },
            //     'required_if:status,1',
            //     'date',
            //     'min:prog_start_date',
            // ],
            'timesheet_link' => [
                function ($attribute, $value, $fail) use ($tutoring_prog_id) {
                    if (in_array($this->input('prog_id'), $tutoring_prog_id) && empty($value)) 
                        $fail('The timesheet link field is required');
                },
                'required_if:status,1',
                'url',
            ],
            'tutor_id' => [
                'required_if:status,1',
                function ($attribute, $value, $fail) use ($tutoring_prog_id) {
                    if (in_array($this->input('prog_id'), $tutoring_prog_id) && empty($value)) 
                        $fail('The tutor field is required');

                    if (!User::with('roles')->whereHas('roles', function ($q) {
                        $q->where('role_name', 'Tutor');
                    })->find($value)) {
                        $fail('The submitted tutor was invalid tutor');
                    }
                },
            ],
            
            # validation when program is sat / act
            'test_date' => [
                function ($attribute, $value, $fail) use ($satact_prog_id) {
                    if (in_array($this->input('prog_id'), $satact_prog_id) && empty($value)) 
                        $fail('The test date field is required');
                },
                'required_if:status,1',
                'date',
            ],
            'last_class' => [
                function ($attribute, $value, $fail) use ($satact_prog_id) {
                    if (in_array($this->input('prog_id'), $satact_prog_id) && empty($value)) 
                        $fail('The last class field is required');
                },
                'required_if:status,1',
                'date'
            ],
            'diag_score' => [
                function ($attribute, $value, $fail) use ($satact_prog_id) {
                    if (in_array($this->input('prog_id'), $satact_prog_id) && empty($value)) 
                        $fail('The diagnostic score field is required');
                },
                'required_if:status,1'
            ],
            'test_score' => [
                function ($attribute, $value, $fail) use ($satact_prog_id) {
                    if (in_array($this->input('prog_id'), $satact_prog_id) && empty($value)) 
                        $fail('The test score field is required');
                },
                'required_if:status,1'
            ],
            'tutor_1' => [
                'required_if:status,1',
                function ($attribute, $value, $fail) use ($satact_prog_id) {
                    if (in_array($this->input('prog_id'), $satact_prog_id) && empty($value)) 
                        $fail('The tutor#1 is required');

                    if (!User::with('roles')->whereHas('roles', function ($q) {
                        $q->where('role_name', 'Tutor');
                    })->find($value)) {
                        $fail('The submitted tutor was invalid tutor');
                    }
                },
            ],
            'tutor_2' => [
                Rule::RequiredIf(in_array($this->input('prog_id'), $satact_prog_id)),
                'required_if:status,1',
                function ($attribute, $value, $fail) use ($satact_prog_id) {
                    if (in_array($this->input('prog_id'), $satact_prog_id) && empty($value)) 
                        $fail('The tutor#2 field is required');

                    if (!User::with('roles')->whereHas('roles', function ($q) {
                        $q->where('role_name', 'Tutor');
                    })->find($value)) {
                        $fail('The submitted tutor was invalid tutor');
                    }

                    // if (UserClient::whereHas('clientMentor', function($query) use ($value) {
                    //     $query->where('user_id', $value);
                    // })->count() > 0) {
                    //     $fail('The choosen tutor has already exist');
                    // }
                },
            ],


            'prog_running_status' => 'sometimes|in:0,1,2',
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
            
        ];

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
            'event_id' => 'required_if:lead_id,LS004',
            'eduf_id' => 'required_if:lead_id,LS018',
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
            'pend_initconsult_date' => [
                function ($attribute, $value, $fail) {
                    if (in_array($this->input('prog_id'), $this->admission_prog_id) && empty($value)) 
                        $fail('The initial consultation date field is required');
                },
            ],
            'pend_assessmentsent_date' => [
                function ($attribute, $value, $fail) {
                    if (in_array($this->input('prog_id'), $this->admission_prog_id) && empty($value)) 
                        $fail('The assessment date field is required');
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
    }

    public function store_tutoring_pending()
    {
        return [
            'prog_id' => 'required|exists:tbl_prog,prog_id',
            'lead_id' => 'required',
            'event_id' => 'required_if:lead_id,LS004',
            'eduf_id' => 'required_if:lead_id,LS018',
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
            'pend_trial_date' => [
                function ($attribute, $value, $fail) {
                    if (in_array($this->input('prog_id'), $this->tutoring_prog_id) && empty($value)) 
                        $fail('The trial date field is required');
                },
                'required_if:status,0,1',
                'date',
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
    }
}
