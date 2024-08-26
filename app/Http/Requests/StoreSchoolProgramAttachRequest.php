<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSchoolProgramAttachRequest extends FormRequest
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


    public function messages()
    {
        return [
            'schprog_id.required' => 'The School program is required',
            'schprog_file.required' => 'The File name is required',
            'schprog_attach.required' => 'The attachment is required',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $schprog_id = $this->route('sch_prog');
        return [

            'schprog_file' => 'required|string',
            'schprog_attach' => 'required|mimes:pdf'
        ];

        if($schprog_id)
            $rules = ['sch_id' => 'required|exists:tbl_sch_prog,id',
            ];

        return $rules;
        
    }
}
