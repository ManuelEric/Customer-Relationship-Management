<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientStudentRequest extends FormRequest
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
            'st_firstname.required' => 'The first name field is required'
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
            'st_firstname' => 'required',
            'st_lastname' => 'nullable',
            'st_mail' => 'required|email',
            'st_phone' => 'required|min:10|max:12',
            'st_dob' => 'required',
            'st_insta' => 'nullable',
            'st_state' => 'required',
            'st_city' => 'nullable',
            'st_pc' => 'nullable',
            'st_address' => 'nullable',
            'sch_id' => ['sometimes'],
            'sch_name' => 'required_if:sch_id,add-new',
            // 'st_currentsch'
        ];

        if ($this->input('sch_id') != "add-new") {
            $rules['sch_id'][] = 'exists:tbl_sch,sch_id';
        }

        return $rules;
    }
}
