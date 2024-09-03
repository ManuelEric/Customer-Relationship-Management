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

    public function messages()
    {
        return [
            'corp_name.required' => 'Corporate / partner name field is required',
            'corp_mail.required' => 'The Email field is required',
            'corp_phone.required' => 'The Phone field is required',
            'corp_site.required' => 'The Website field is required',
            'corp_site.url' => 'The Website must be a valid URL',
            'country_type.required' => 'The Country Type field is required',
            'country_type.in' => 'The Country Type value is invalid',
            'partnership_type.in' => 'The Partnership Type value is invalid',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return $this->isMethod('POST') ? $this->store() : $this->update();
    }

    protected function store()
    {
        return [
            'corp_name' => 'required|unique:tbl_corp,corp_name',
            'corp_industry' => 'nullable',
            'corp_mail' => 'required|email',
            'corp_phone' => 'required',
            'corp_insta' => 'nullable',
            'corp_site' => 'nullable|url',
            'corp_region' => 'nullable',
            'corp_address' => 'nullable',
            'corp_note' => 'nullable',
            'corp_password' => 'nullable',
            'country_type' => 'required|in:Indonesia,Overseas',
            'type' => 'required|in:Corporate,Individual Professional,Tutoring Center,Course Center,Agent,Community,NGO',
            'partnership_type' => 'nullable|in:Market Sharing,Program Collaborator,Internship,External Mentor',
        ];
    }

    protected function update()
    {
        $corp_id = $this->input('corp_id');

        return [
            'corp_id' => 'required|unique:tbl_corp,corp_id,'.$corp_id.',corp_id',
            'corp_name' => 'required|unique:tbl_corp,corp_name,'.$corp_id.',corp_id',
            'corp_industry' => 'nullable',
            'corp_mail' => 'required|email',
            'corp_phone' => 'required',
            'corp_insta' => 'nullable',
            'corp_site' => 'nullable|url',
            'corp_region' => 'nullable',
            'corp_address' => 'nullable',
            'corp_note' => 'nullable',
            'corp_password' => 'nullable',
            'country_type' => 'required|in:Indonesia,Overseas',
            'type' => 'required|in:Corporate,Individual Professional,Tutoring Center,Course Center,Agent,Community,NGO',
            'partnership_type' => 'nullable|in:Market Sharing,Program Collaborator,Internship,External Mentor',
        ];
    }
}
