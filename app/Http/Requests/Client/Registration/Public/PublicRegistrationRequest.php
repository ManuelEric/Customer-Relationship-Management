<?php

namespace App\Http\Requests\Client\Registration\Public;

use Illuminate\Foundation\Http\FormRequest;

class PublicRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'role' => 'required|in:parent,student',
            'fullname' => 'required',
            'mail' => 'nullable|email',
            'phone' => 'required',
            'school_id' => [
                'nullable',
                $this->input('school_id') != 'new' ? 'exists:tbl_sch,sch_id' : null
            ],
            'other_school' => 'nullable',
            'secondary_name' => 'required_if:role,parent',
            'secondary_mail' => 'nullable',
            'secondary_phone' => 'nullable',
            'graduation_year' => 'required',
            'destination_country' => 'nullable|array',
            'destination_country.*' => 'exists:tbl_country,id',
            'interest_prog' => 'required|exists:tbl_prog,prog_id',
            'lead_id' => 'required|exists:tbl_lead,lead_id',
        ];
    }
}
