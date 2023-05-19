<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSchoolRequest extends FormRequest
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
            'sch_name.required' => 'The School Name field is required',
            'sch_type.in' => 'The Type field is invalid',
            'sch_curriculum.required' => 'The Curriculum field is required',
            'sch_mail.unique' => 'The School Mail already been taken', 
            'sch_curriculum.*.exists' => 'The Selected Curriculum is invalid',
            'sch_score.required' => 'The Target field is required'
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $sch_id = $this->route('school');

        return [
            'sch_name' => 'required',
            'sch_type' => 'required|in:International,National,National_plus,National_private,Home_schooling',
            'sch_curriculum' => 'required',
            'sch_curriculum.*' => 'sometimes|exists:tbl_curriculum,id',
            'sch_insta' => 'nullable',
            'sch_mail' => 'nullable|unique:tbl_sch,sch_mail,'.$sch_id.',sch_id|email',
            'sch_phone' => 'nullable',
            'sch_city' => 'nullable',
            'sch_location' => 'nullable',
            'sch_score' => 'required'
        ];
    }
}
