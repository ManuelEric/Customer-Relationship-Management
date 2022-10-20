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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'sch_name' => 'required',
            'sch_type' => 'nullable|in:International,National',
            'sch_curriculum' => 'required|in:American Curriculum,Australian Curriculum,Canadian Curriculum,European Curriculum,French Curriculum,GCE,IB,IGCSE,Korean Curriculum,National Curriculum,Singapore Curiculum',
            'sch_insta' => 'nullable',
            'sch_mail' => 'nullable|unique:tbl_sch,sch_mail|email',
            'sch_phone' => 'nullable',
            'sch_city' => 'nullable',
            'sch_location' => 'nullable',
        ];
    }
}
