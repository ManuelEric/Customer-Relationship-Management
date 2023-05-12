<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSchoolCollaboratorsPartnerProgramRequest extends FormRequest
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

        $partnerprogId = $this->route('corp_prog');
        $schoolId = $this->input('sch_id');

        return [
            'sch_id' => [
                'required',
                'exists:tbl_sch,sch_id',
                Rule::unique('tbl_partner_prog_sch')->where('partnerprog_id', $partnerprogId)->where('sch_id', $schoolId)
                ]
        ];
    }
}
