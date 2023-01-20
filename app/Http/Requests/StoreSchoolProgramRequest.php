<?php

namespace App\Http\Requests;

use App\Interfaces\InvoiceB2bRepositoryInterface;
use App\Interfaces\ReceiptRepositoryInterface;
use App\Models\School;
use App\Models\SchoolProgram;
use App\Models\User;
use Arcanedev\Support\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSchoolProgramRequest extends FormRequest
{

    private InvoiceB2bRepositoryInterface $invoiceB2bRepository;
    private ReceiptRepositoryInterface $receiptRepository;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function __construct(InvoiceB2bRepositoryInterface $invoiceB2bRepository, ReceiptRepositoryInterface $receiptRepository)
    {
        $this->invoiceB2bRepository = $invoiceB2bRepository;
        $this->receiptRepository = $receiptRepository;
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */

    public function messages()
    {
        return [
            'required_if' => 'The :attribute field is required',
        ];
    }

    public function attributes()
    {
        return [
            'reason_id' => 'reason',
            'reason_refund_id' => 'reason refund',
            'other_reason_refund' => 'reason refund',
            'other_reason' => 'reason',
            'sch_id' => 'school',
            'prog_id' => 'program name',
            'empl_id' => 'PIC',
        ];
    }

    public function rules()
    {

        switch ($this->input('status')) {
            case null:
                return $this->pending();
                break;

                // Pending
            case '0':
                return $this->pending();
                break;

                // Success
            case '1':
                return $this->success();
                break;

                // Denied
            case '2':
                return $this->denied();
                break;

                // Refund
            case '3':


                return $this->refund();
                break;
        }
    }

    protected function pending()
    {
        $sch_id = $this->route('school');

        return [
            'prog_id' => 'required|exists:tbl_prog,prog_id',
            'first_discuss' => 'required|date',
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
            'notes' => 'nullable',
            'notes_detail' => 'nullable',
        ];

        if ($sch_id) {
            $rules = [
                'sch_id' => [
                    'required',
                    function ($attribute, $value, $fail) use ($sch_id) {
                        if (!School::find($sch_id))
                            $fail('The school is required');
                    },
                ]
            ];
            return $rules;
        }
    }

    protected function success()
    {
        $sch_id = $this->route('school');

        return [
            'prog_id' => 'required|exists:tbl_prog,prog_id',
            'first_discuss' => 'required|date',
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
            'notes' => 'nullable',
            'notes_detail' => 'nullable',
            'running_status' => 'required|in:Not yet,On going,Done',
            'total_hours' => 'required|integer',
            'total_fee' => 'required|numeric',
            'participants' => 'required|integer',
            'place' => 'required|string',
            'end_program_date' => 'required|date|after_or_equal:start_program_date',
            'start_program_date' => 'required|date|before_or_equal:end_program_date',
            'success_date' => 'required|date',
        ];

        if ($sch_id) {
            $rules = [
                'sch_id' => [
                    'required',
                    function ($attribute, $value, $fail) use ($sch_id) {
                        if (!School::find($sch_id))
                            $fail('The school is required');
                    },
                ]
            ];
            return $rules;
        }
    }

    protected function denied()
    {
        $sch_id = $this->route('school');

        return [
            'prog_id' => 'required|exists:tbl_prog,prog_id',
            'first_discuss' => 'required|date',
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
            'notes' => 'nullable',
            'notes_detail' => 'nullable',
            'denied_date' => 'required|date',
            'reason_id' => 'required',
            'other_reason' => 'required_if:reason_id,other|nullable|unique:tbl_reason,reason_name',

        ];

        if ($sch_id) {
            $rules = [
                'sch_id' => [
                    'required',
                    function ($attribute, $value, $fail) use ($sch_id) {
                        if (!School::find($sch_id))
                            $fail('The school is required');
                    },
                ]
            ];
            return $rules;
        }
    }

    protected function refund()
    {
        $sch_id = $this->route('school');
        if ($this->isMethod('PUT')) {
            $schprog_id = $this->route('detail');

            $hasInvoice = $this->invoiceB2bRepository->getInvoiceB2bBySchProg($schprog_id);
            if (isset($hasInvoice)) {
                $hasReceipt = $this->receiptRepository->getReceiptByInvoiceIdentifier('B2B', $hasInvoice->invb2b_id);
            } else {
                $hasReceipt = null;
            }
        }


        if ($this->isMethod('PUT')) {
            $rules = [
                'status' =>
                [
                    'required', 'in:0,1,2,3',
                    function ($attribute, $value, $fail) use ($hasInvoice, $hasReceipt) {
                        if (!isset($hasInvoice)) {
                            $fail('The status cannot change to refund, if invoice have not been made');
                        } else if (isset($hasInvoice)  && !isset($hasReceipt)) {
                            $fail('The status cannot change to refund, if receipt have not been made');
                        }
                    }
                ]

            ];
        } else {
            $rules = [
                'status' =>
                [
                    'required', 'in:0,1,2',
                ]

            ];
        }


        if ($sch_id) {
            $rules = [
                'sch_id' => [
                    'required',
                    function ($attribute, $value, $fail) use ($sch_id) {
                        if (!School::find($sch_id))
                            $fail('The school is required');
                    },
                ]
            ];
        }

        $rules = [
            'prog_id' => 'required|exists:tbl_prog,prog_id',
            'first_discuss' => 'required|date',
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
            'notes' => 'nullable',
            'notes_detail' => 'nullable',
            'reason_refund_id' => 'required',
            'other_reason_refund' => 'required_if:reason_refund_id,other_reason_refund|nullable|unique:tbl_reason,reason_name',
            'refund_date' => 'required|date',
            'refund_notes' => 'nullable',
        ];



        return $rules;
    }
}
