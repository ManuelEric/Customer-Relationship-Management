<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangeUserStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'new_status' => 'required|in:activate,deactivate',
            'deactivated_at' => 'required',
            'new_pic' => 'nullable',
            'department' => 'nullable'
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'active' => $this->new_status == "activate" ? 1 : 0,
        ]);
    }

    public function messages(): array
    {
        return [
            'new_status.in' => 'Invalid status',
            'new_status.required' => 'New status is required',
        ];
    }

    /**
     * Get the validation attributes.
     * 
     */
    public function attributes(): array
    {
        return [
            'deactivated_at' => 'date of deactivation',
        ];
    }
}
