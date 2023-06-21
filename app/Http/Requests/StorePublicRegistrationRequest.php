<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePublicRegistrationRequest extends FormRequest
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
            'parent_name' => 'required_if:child_name,null',
            'child_name' => 'required',
            'email' => 'required|email|unique:tbl_client,mail',
            'phone' => 'required',
            'school' => 'required',
            'grade' => 'required',
            'program' => 'nullable'
        ];
    }
}
