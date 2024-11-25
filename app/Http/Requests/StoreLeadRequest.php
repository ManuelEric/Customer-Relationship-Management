<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeadRequest extends FormRequest
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
            'main_lead.required_unless' => 'The lead name field is required',
            'main_lead.unique' => 'The lead name has already been taken',
            'sub_lead.required_if' => 'The lead name field is required',
            'sub_lead.unique' => 'The lead name has already been taken',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [
            'kol' => 'sometimes',
            'score' => 'required|numeric|gt:0',
            'department_id' => 'required|exists:tbl_department,id'
        ];

        // $leadId = $this->input('id');
        $kol = $this->input('kol');

        if ($kol == true)
            # Error when store lead is KOL
            // $rules['lead_name'] = 'required_if:kol,true|exclude_unless:kol,true|unique:tbl_lead,sub_lead,'.$leadId;
            $rules['lead_name'] = 'required_if:kol,true|exclude_unless:kol,true';
        else
            // $rules['lead_name'] = 'required_if:kol,true|exclude_unless:kol,true|unique:tbl_lead,main_lead,'.$leadId;
            $rules['lead_name'] = 'required_if:kol,true|exclude_unless:kol,true';

        return $rules;
    }
}
