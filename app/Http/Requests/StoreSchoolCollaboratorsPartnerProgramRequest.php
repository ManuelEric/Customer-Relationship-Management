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
        $collaborators = $this->route('collaborators');
        
        switch ($collaborators) {

            case "school":
                $schoolId = $this->input('sch_id');
                $rules = [
                    'sch_id' => [
                        'required',
                        'exists:tbl_sch,sch_id',
                        Rule::unique('tbl_partner_prog_sch')->where('partnerprog_id', $partnerprogId)->where('sch_id', $schoolId)
                    ]
                ];
                break;

            case "university":
                $univId = $this->input('univ_id');
                $rules = [
                    'univ_id' => [
                        'required',
                        'exists:tbl_univ,univ_id',
                        Rule::unique('tbl_partner_prog_univ')->where('partnerprog_id', $partnerprogId)->where('univ_id', $univId)
                    ]
                ];
                break;

            case "partner":
                $corpId = $this->input('corp_id');
                $rules = [
                    'corp_id' => [
                        'required',
                        'exists:tbl_corp,corp_id',
                        Rule::unique('tbl_partner_prog_partner')->where('partnerprog_id', $partnerprogId)->where('corp_id', $corpId)
                    ]
                ];
                break;

        }

        return $rules;
    }
}
