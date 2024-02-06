<?php

namespace App\Http\Requests;

use App\Models\Lead;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function PHPSTORM_META\map;

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
            'st_firstname.required' => 'The first name field is required',
            'referral_code.required' => 'The Referral name field is required'
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $studentId = $this->route('student');

        $rules = [
            'first_name' => 'required',
            'last_name' => 'nullable',
            'mail' => 'required|email|unique:tbl_client,mail,' . $studentId . ',id',
            'phone' => 'required|min:10|max:15',
            'dob' => 'nullable',
            'insta' => 'nullable|unique:tbl_client,insta,' . $studentId . ',id',
            'state' => 'required',
            'city' => 'nullable',
            'postal_code' => 'nullable',
            'address' => 'nullable',
            'st_grade' => 'required',
            'st_graduation_year' => 'nullable',
            'gap_year' => 'nullable',
            'sch_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    if ($this->input('sch_id') != "add-new") {
                        Rule::exists('tbl_sch', 'sch_id');
                    }
                }
            ],
            'sch_name' => [
                'sometimes',
                'required_if:sch_id,add-new',
                function ($attribute, $value, $fail) {
                    if ($this->input('sch_id') == "add-new") {
                        Rule::unique('tbl_sch', 'sch_name');
                    }
                }
            ], #|unique:tbl_sch,sch_name
            // 'sch_location' => 'sometimes|required_if:sch_id,add-new',
            'sch_type' => 'required_if:sch_id,add-new',
            'sch_curriculum.*' => 'required_if:sch_id,add-new',
            'sch_score' => 'required_if:sch_id,add-new',
            'event_id' => 'required_if:lead_id,LS003', # make sure id LS003 is all-in event
            'eduf_id' => 'required_if:lead_id,LS017', # make sure id LS017 is all-in eduf
            'referral_code' => 'required_if:lead_id,LS005', # make sure id LS005 is Referral
            'kol_lead_id' => [
                function ($attribute, $value, $fail) {
                    if ($this->input('lead_id') == 'kol' && empty($value))
                        $fail('The KOL name field is required');

                    if (!Lead::where('main_lead', 'KOL')->where('lead_id', $value)->get())
                        $fail('The KOL name is invalid');
                }
            ],
            'st_levelinterest' => 'required|in:High,Medium,Low',
            // 'prog_id.*' => 'sometimes|required|exists:tbl_prog,prog_id',
            'st_abryear' => [
                'sometimes',
                function ($attribute, $value, $fail) {
                    if (($value <= date('Y')) && ($value >= date('Y', strtotime("+5 years")))) {
                        $fail('The abroad year is invalid');
                    }
                }
            ],
            'st_abrcountry.*' => 'nullable',
            'st_abruniv.*' => 'sometimes|nullable|exists:tbl_univ,univ_id',
            'st_abrmajor.*' => 'sometimes|nullable|exists:tbl_major,id',
            // 'pr_id' => 'nullable',
            // 'pr_firstname' => 'required_if:pr_id,add-new',
            // 'pr_lastname' => 'nullable',
            // 'pr_mail' => 'nullable|email',
            // 'pr_phone' => 'required_if:pr_id,add-new',
            'is_funding' => 'nullable|in:0,1',
            'register_as' => 'nullable|in:student,parent',
        ];

        if ($this->input('lead_id') != "kol") {
            $rules['lead_id'] = 'required|exists:tbl_lead,lead_id';
        }

        return $rules;
    }
}
