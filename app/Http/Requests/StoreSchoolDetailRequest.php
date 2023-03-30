<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSchoolDetailRequest extends FormRequest
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
            'schdetail_name.*.required' => 'The fullname field is required',
            'schdetail_name.*.alpha' => 'The fullname must only contain letters',
            'schdetail_mail.*.required' => 'The email field is required',
            'schdetail_grade.*.required' => 'The school grade field is required',
            'schdetail_position.*.required' => 'The status field is required',
            'schdetail_phone.*.required' => 'The phone number field is required',

        ];
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
            // 'schdetail_fullname' => 'required',
            // 'schdetail_email' => 'required|email',
            // 'schdetail_grade' => 'required|in:Middle School,High School,Middle School & High School',
            // 'schdetail_position' => 'required|in:Principal,Counselor,Teacher,Marketing',
            // 'schdetail_phone' => 'required',
            'schdetail_name.*' => 'required|alpha',
            'schdetail_mail.*' => 'required|email',
            'schdetail_grade.*' => 'required|in:Middle School,High School,Middle School & High School',
            'schdetail_position.*' => 'required|in:Principal,Counselor,Teacher,Marketing',
            'schdetail_phone.*' => 'required',
        ];
    }
}
