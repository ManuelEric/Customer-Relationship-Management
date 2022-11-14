<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCorporateRequest extends FormRequest
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
        $rules = [
            'corp_name' => 'required',
            'corp_industry' => 'nullable',
            'corp_mail' => 'required|email',
            'corp_phone' => 'required',
            'corp_insta' => 'nullable',
            'corp_site' => 'required|url',
            'corp_region' => 'nullable',
            'corp_address' => 'nullable',
            'corp_note' => 'nullable',
            'corp_password' => 'nullable',
        ];

        if ($corp_id = $this->input('corp_id'))
            $rules['corp_id'] = 'required|unique:tbl_corp,corp_id,'.$corp_id.',corp_id';
        
        return $rules;
    }
}
