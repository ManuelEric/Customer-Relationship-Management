<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'schdetail_name.*.string' => 'The fullname must only contain letters',
            'schdetail_mail.*.required' => 'The email field is required',
            'schdetail_mail.*.unique' => 'The email has already been taken',
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

        $schId = $this->route('school');
        $picId = $this->input('schdetail_id');

        return [
            'sch_id' => 'required|exists:tbl_sch,sch_id',
            // 'schdetail_fullname' => 'required',
            // 'schdetail_email' => 'required|email',
            // 'schdetail_grade' => 'required|in:Middle School,High School,Middle School & High School',
            // 'schdetail_position' => 'required|in:Principal,Counselor,Teacher,Marketing',
            // 'schdetail_phone' => 'required',
            'schdetail_name.*' => 'required|string',
            'schdetail_mail.*' => [
                'required',
                'email', 
                Rule::unique('tbl_schdetail', 'schdetail_email')->where('sch_id', $schId)->when($picId !== null, function ($query) use ($picId) {
                    $query->ignore($picId, 'schdetail_id');
                }),
            ],
            'schdetail_grade.*' => 'required|in:Middle School,High School,Middle School & High School',
            'schdetail_position.*' => 'required|in:Principal,Counselor,Teacher,Marketing',
            'schdetail_phone.*' => 'required',
            'is_pic' => 'required:in,true,false',
        ];
    }
}
