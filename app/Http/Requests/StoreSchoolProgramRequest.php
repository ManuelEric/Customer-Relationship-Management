<?php

namespace App\Http\Requests;

use App\Models\School;
use App\Models\SchoolProgram;
use App\Models\User;
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
            'sch_id.required' => 'The School field is required',
            'prog_id.required' => 'The Program Name field is required',
            'empl_id.required' => 'The PIC field is required',
        ];
    }

    public function rules()
    {
        $sch_id = $this->route('school');
       
      

        return [
            'prog_id' => 'required|exists:tbl_prog,prog_id',
            'first_discuss' => 'required|date',
            'planned_followup' => 'required|date|after_or_equal:first_discuss',
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
            'running_status' => 
                [
                    'required_if:status,1|in:Not yet,On going,Done',
                    function ($attribute, $value, $fail) {

                        if ($this->input('status') == 1 )
                            $fail('The Running Status is required');
    
                    }
                ]
            ,
            'total_hours' => 
                [
                    'required_if:status,1|integer',
                    function ($attribute, $value, $fail) {

                        if ($this->input('status') == 1 )
                            $fail('The Total hours is required');
    
                    }  
                ],
            'total_fee' => 
                [
                    'required_if:status,1|numeric',
                    function ($attribute, $value, $fail) {

                        if ($this->input('status') == 1 )
                            $fail('The Total fee is required');

                    }  
                ],
            'participants' => 
                [
                    'required_if:status,1|integer',
                    function ($attribute, $value, $fail) {

                        if ($this->input('status') == 1 )
                            $fail('The Participants is required');

                    }  
                ],
            'place' => 
                [
                    'required_if:status,1|integer',
                    function ($attribute, $value, $fail) {

                        if ($this->input('status') == 1 )
                            $fail('The Place is required');

                    }  
                ],
            'end_program_date' => 
                [
                    'required_if:status,1|date|after_or_equal:start_program_date',
                    function ($attribute, $value, $fail) {

                        if ($this->input('status') == 1 )
                            $fail('The End program date is required');

                    }  
                ],
            
            'start_program_date' => 
                [
                    'required_if:status,1|date|before_or_equal:end_program_date',
                    function ($attribute, $value, $fail) {

                        if ($this->input('status') == 1 )
                            $fail('The Start program date is required');

                    }  
                ],
            
            'success_date' => 
                [
                    'required_if:status,1|date',
                    function ($attribute, $value, $fail) {

                        if ($this->input('status') == 1 )
                            $fail('The Success date is required');

                    }  
                ],
            
            'reason_id' => 
                [
                    'required_if:status,2',
                    function ($attribute, $value, $fail) {

                        if ($this->input('status') == 2 )
                            $fail('The Reason is required');

                    }  
                ],

            'denied_date' => 
                [
                    'required_if:status,2|date',
                    function ($attribute, $value, $fail) {

                        if ($this->input('status') == 2 )
                            $fail('The Denied date is required');

                    }  
                ],

            
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