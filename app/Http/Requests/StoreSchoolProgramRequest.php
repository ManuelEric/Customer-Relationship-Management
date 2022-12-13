<?php

namespace App\Http\Requests;

use App\Models\School;
use App\Models\SchoolProgram;
use App\Models\User;
use Arcanedev\Support\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSchoolProgramRequest extends FormRequest
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
            'reason_id' => 'reason',
            'sch_id' => 'school',
            'prog_id' => 'program name',
            'empl_id' => 'PIC',
        ];
    }

    public function rules()
    {
        $sch_id = $this->route('school');
       
        return [
            'prog_id' => 'required|exists:tbl_prog,prog_id',
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
            'notes_detail' => 'nullable',
            'running_status' => 'required_if:status,1|nullable|in:Not yet,On going,Done',
            'total_hours' => 'required_if:status,1|nullable|integer',
            'total_fee' => 'required_if:status,1|nullable|numeric',
            'participants' => 'required_if:status,1|nullable|integer',
            'place' => 'required_if:status,1|nullable|string',
            'end_program_date' => 'required_if:status,1|nullable|date|after_or_equal:start_program_date',
            'start_program_date' => 'required_if:status,1|nullable|date|before_or_equal:end_program_date',
            'success_date' => 'required_if:status,1|nullable|date',
            'denied_date' => 'required_if:status,2|nullable|date',
            'reason_id' => 'required_if:status,2|nullable',
            'other_reason' => 'required_if:reason_id,other|nullable',
            
            
        ];

        if($sch_id){
            $rules = ['sch_id' => [
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
}