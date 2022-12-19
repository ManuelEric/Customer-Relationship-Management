<?php

namespace App\Http\Requests;

use App\Models\Lead;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClientParentRequest extends FormRequest
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
        $parentId = $this->route('parent');

        $rules = [
            'pr_firstname' => 'required',
            'pr_lastname' => 'nullable',
            'pr_mail' => 'required|email|unique:tbl_client,mail,'.$parentId.',id',
            'pr_phone' => 'required|min:10|max:12',
            'pr_dob' => 'required',
            'pr_insta' => 'nullable|unique:tbl_client,insta,'.$parentId.',id',
            'state' => 'required',
            'city' => 'nullable',
            'postal_code' => 'nullable',
            'address' => 'nullable',
            'st_grade' => 'required_if:child_id,add-new',
            'st_graduation_year' => 'nullable',
            'sch_id' => [
                'required_if:child_id,add-new',
                function ($attribute, $value, $fail) {
                    if ($this->input('sch_id') != "add-new") {
                        Rule::exists('tbl_sch', 'sch_id');
                    }
                }
            ],
            'sch_name' => 'sometimes|required_if:sch_id,add-new|unique:tbl_sch,sch_name',
            // 'sch_location' => 'sometimes|required_if:sch_id,add-new',
            'sch_type' => 'required_if:sch_id,add-new',
            'sch_curriculum.*' => 'required_if:sch_id,add-new',
            'sch_score' => 'required_if:sch_id,add-new',
            'event_id' => 'required_if:lead_id,LS004',
            'eduf_id' => 'required_if:lead_id,LS018',
            'kol_lead_id' => [
                function ($attribute, $value, $fail) {
                    if ($this->input('lead_id') == 'kol' && empty($value))
                        $fail('The KOL name field is required');

                    if (!Lead::where('main_lead', 'KOL')->where('lead_id', $value)->get()) 
                        $fail('The KOL name is invalid');
                }
            ],
            'st_levelinterest' => 'required|in:High,Medium,Low',
            'prog_id.*' => 'sometimes|required|exists:tbl_prog,prog_id',
            'st_abryear' => [
                'sometimes',
                function($attribute, $value, $fail) {
                    if ( ($value <= date('Y')) && ($value >= date('Y', strtotime("+5 years"))) ) {
                        $fail('The abroad year is invalid');
                    }
                }
            ],
            'st_abrcountry.*' => 'nullable',
            'st_abruniv.*' => 'sometimes|nullable|exists:tbl_univ,univ_id',
            'st_abrmajor.*' => 'sometimes|nullable|exists:tbl_major,id',
            'child_id' => 'nullable',
            'first_name' => 'required_if:child_id,add-new',
            'last_name' => 'nullable',
            'mail' => 'nullable|email',
            'phone' => 'required_if:child_id,add-new',
        ];

        if ($this->input('lead_id') != "kol") {
            $rules['lead_id'] = 'required|exists:tbl_lead,lead_id';
        }

        return $rules;
    }
}
