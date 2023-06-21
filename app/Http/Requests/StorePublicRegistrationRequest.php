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
            'parent_or_children' => 'required|in:parent,children',
            'parent_name' => 'required_if:parent_or_children,parent|string',
            'parent_email' => 'required_if:parent_or_children,parent|email',
            'parent_phone' => 'required_if:parent_or_children,parent',
            'child_name' => 'required_if:parent_or_children,children|string',
            'chilld_email' => 'required_if:parent_or_children,children|email',
            'chilld_phone' => 'required_if:parent_or_children,children',
            'chilld_school' => 'required_if:parent_or_children,children',
            'chilld_grade' => 'required_if:parent_or_children,children',
            'chilld_program' => 'required_if:parent_or_children,children'
        ];
    }
}
