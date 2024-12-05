<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFormProgramEmbedRequest extends FormRequest
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
            
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            // 'fullname.*' => 'required',
            // 'email.*' => 'required|email',
            // 'fullnumber.*' => 'required',

            'fullname.0' => 'required',
            'email.0' => 'required|email',
            'fullnumber.0' => 'required',

            'fullname.1' => 'required',
            'email.1' => 'nullable|email',
            'fullnumber.1' => 'nullable',
            
            'school' => 'required',
            'graduation_year' => 'required',
            'destination_country' => 'required|exists:tbl_country,id',
            'leadsource' => 'required|exists:tbl_lead,lead_id',
            'program' => 'nullable',
        ];
    }
}
