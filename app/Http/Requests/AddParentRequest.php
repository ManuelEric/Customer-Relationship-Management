<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddParentRequest extends FormRequest
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
            'required_if' => 'The :attribute field is required',
        ];
    }

    public function attributes()
    {
        return [
            'pr_id' => 'Parent Name',
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
            'existing_parent' => 'required|in:1,0',
            'pr_id' => 'required_if:existing_parent,1|nullable',
            'first_name' => 'required_if:existing_parent,0|nullable',
            'last_name' => 'required_if:existing_parent,0|nullable',
            'mail' => 'required_if:existing_parent,0|email|unique:tbl_client,mail|nullable',
            'phone' => 'required_if:existing_parent,0|min:10|max:15|nullable',
        ];
    }
}
