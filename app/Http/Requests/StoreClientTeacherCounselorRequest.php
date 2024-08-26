<?php

namespace App\Http\Requests;

use App\Models\Lead;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClientTeacherCounselorRequest extends FormRequest
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
        $teacher_counsellorId = $this->route('teacher_counselor');

        $rules = [
            'first_name' => 'required',
            'last_name' => 'nullable',
            'mail' => 'required|email|unique:tbl_client,mail,' . $teacher_counsellorId . ',id',
            'phone' => 'required|min:10|max:15',
            'dob' => 'nullable',
            'insta' => 'nullable|unique:tbl_client,insta,' . $teacher_counsellorId . ',id',
            'state' => 'required',
            'city' => 'nullable',
            'postal_code' => 'nullable',
            'address' => 'nullable',
            'sch_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    if ($this->input('sch_id') != "add-new") {
                        Rule::exists('tbl_sch', 'sch_id');
                    }
                }
            ],
            'sch_name' => 'sometimes|required_if:sch_id,add-new', #|unique:tbl_sch,sch_name
            // 'sch_location' => 'sometimes|required_if:sch_id,add-new',
            'sch_type' => 'required_if:sch_id,add-new',
            'sch_curriculum.*' => 'required_if:sch_id,add-new',
            'sch_score' => 'required_if:sch_id,add-new',
            'event_id' => 'required_if:lead_id,LS003', # make sure id LS003 is all-in event
            'eduf_id' => 'required_if:lead_id,LS017', # make sure id LS017 is all-in eduf
            'referral_code' => 'required_if:lead_id,LS005', # make sure id LS005 is referral
            'kol_lead_id' => [
                function ($attribute, $value, $fail) {
                    if ($this->input('lead_id') == 'kol' && empty($value))
                        $fail('The KOL name field is required');

                    if (!Lead::where('main_lead', 'KOL')->where('lead_id', $value)->get())
                        $fail('The KOL name is invalid');
                }
            ],
            'st_levelinterest' => 'required|in:High,Medium,Low',
        ];

        if ($this->input('lead_id') != "kol") {
            $rules['lead_id'] = 'required|exists:tbl_lead,lead_id';
        }

        return $rules;;
    }
}
