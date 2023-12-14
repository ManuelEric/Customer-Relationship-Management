<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSchoolRawRequest extends FormRequest
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
            'sch_id' => 'required|exists:tbl_sch,sch_id',
            'sch_name' => [
                'required',
                Rule::unique('tbl_sch')->ignore($this->input('sch_id'), 'sch_id')
            ],
            'sch_type' => 'required|in:International,National,National_plus,National_private,Home_schooling',
            'sch_location' => 'nullable',
            'sch_score' => 'nullable'
        ];
    }
}
