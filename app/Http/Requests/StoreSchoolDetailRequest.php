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
            'schdetail_name.*' => 'required|string',
            'schdetail_mail.*' => 'required|email',
            'schdetail_grade.*' => 'required|in:Middle School,High School,Middle School & High School',
            'schdetail_position.*' => 'required|in:Principal,Counselor,Teacher,Marketing',
            'schdetail_phone.*' => 'required',
            'last_discuss' => 'required|date',
        ];
    }
}
