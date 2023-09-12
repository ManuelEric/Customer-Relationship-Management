<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFormEventEmbedRequest extends FormRequest
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
            'leadsource' => ':attributes field is required'
        ];
    }

    public function attributes()
    {
        switch ($this->input('role')) {
            case "parent":
                return [
                    'fullname.0' => 'name',
                    'email.0' => 'email address',
                    'fullnumber.0' => 'phone number',
                    'fullname.1' => 'child name',
                    'email.1' => 'child email address',
                    'fullnumber.1' => 'child phone number',
                    'leadsource' => 'where do you know this event'
                ];
            case "student":
                return [
                    'fullname.0' => 'name',
                    'email.0' => 'email address',
                    'fullnumber.0' => 'phone number',
                    'fullname.1' => 'parent name',
                    'email.1' => 'parent email address',
                    'fullnumber.1' => 'parent phone number',
                    'leadsource' => 'where do you know this event'
                ];
                break;
            
            default:
                return [
                    'fullname.0' => 'name',
                    'email.0' => 'email address',
                    'fullnumber.0' => 'phone number',
                    'leadsource' => 'where do you know this event'
                ];
        }
        
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        switch ($this->input('role')) {
            
            case "parent":
            case "student":
                return $this->validateParentAndStudent();
                break;

            case "teacher/counsellor":
                return $this->validateTeacher();
                break;
        }
    }

    public function validateParentAndStudent()
    {
        return [
            'fullname.0' => 'required',
            'email.0' => 'required|email',
            'fullnumber.0' => 'required',

            'fullname.1' => 'required',
            'email.1' => 'nullable|email',
            'fullnumber.1' => 'nullable',
            
            'school' => 'required',
            'graduation_year' => 'required',
            'destination_country' => 'required|exists:tbl_tag,id',
            'leadsource' => 'nullable|exists:tbl_lead,lead_id'
        ];
    }

    public function validateTeacher()
    {
        return [
            'fullname.0' => 'required',
            'email.0' => 'required|email',
            'fullnumber.0' => 'required',
            'fullname.1' => 'required',
            'email.1' => 'nullable|email',
            'fullnumber.1' => 'nullable',
            
            'school' => 'required',
            'leadsource' => 'nullable|exists:tbl_lead,lead_id'
        ];
    }
}
