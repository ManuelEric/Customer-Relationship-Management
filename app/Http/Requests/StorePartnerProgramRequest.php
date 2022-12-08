<?php

namespace App\Http\Requests;

use App\Models\Corporate;
use App\Models\PartnerProg;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StorePartnerProgramRequest extends FormRequest
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
            // 'reason_id' => 'reason',
            'prog_id' => 'program name',
            'empl_id' => 'PIC',
        ];
    }

    public function rules()
    {

        return [
            'prog_id' => 'required|exists:tbl_prog,prog_id',
            // 'type' => 'required',
            'first_discuss' => 'required|date',
            'status' => 'required|in:0,1,2',
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
            'number_of_student' => 'required_if:status,1|nullable|integer',
            // 'total_fee' => 'required_if:status,1|nullable|numeric',
            'end_date' => 'required_if:status,1|nullable|date|after_or_equal:start_program_date',
            'start_date' => 'required_if:status,1|nullable|date|before_or_equal:end_program_date',
            // 'success_date' => 'required_if:status,1|nullable|date',
            // 'reason_id' => 'required_if:status,2|nullable',
            // 'denied_date' => 'required_if:status,2|nullable|date',

            
        ];
  
        
    }
}