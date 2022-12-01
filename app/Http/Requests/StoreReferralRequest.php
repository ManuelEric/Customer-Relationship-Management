<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreReferralRequest extends FormRequest
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
            'referral_type.required' => 'The type field is required',
            'referral_type.in' => 'The selected type is invalid',
            'partner_id.required' => 'The partner name field is required',
            'partner_id.exists' => 'The selected partner is invalid',
            'prog_id.required_if' => 'The program name field is required',
            'prog_id.exists' => 'The selected program is invalid',
            'empl_id.required' => 'The PIC field is required',
            'additional_prog_name.required_if' => 'The program name field is required',
            'number_of_student.required' => 'The participant field is required',
            'revenue.required' => 'The amount field is required',
            'ref_date.required' => 'The referral date is required',
            'ref_date.date' => 'The referral date format is invalid',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [
            'referral_type' => 'required|in:In,Out',
            'partner_id' => 'required|exists:tbl_corp,corp_id',
            'prog_id' => ['required_unless:referral_type,Out'],
            'empl_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!User::whereHas('roles', function ($q) {
                        $q->where('role_name', 'Employee');
                    })->find($value)) {
                        $fail('The submitted pic was invalid employee');
                    }
                }
            ],
            'additional_prog_name' => 'required_if:referral_type,Out',
            'number_of_student' => 'required',
            'revenue' => 'required',
            'ref_date' => 'required|date',
            'notes' => 'nullable'
        ];

        if ($this->input('referral_type') == 'In')
            $rules['prog_id'][] = 'exists:tbl_prog,prog_id';


        return $rules;
    }

}
