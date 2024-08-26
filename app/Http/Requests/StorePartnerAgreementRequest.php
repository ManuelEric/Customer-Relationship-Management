<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StorePartnerAgreementRequest extends FormRequest
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

    public function attributes()
    {
        return [
            'corp_pic' => 'Partner PIC',
            'empl_id' => 'ALL In PIC',
            'attachment' => 'Agreement File',
        ];
    }


    public function rules()
    {

        return [
            // 'corp_id' => 'required|exists:tbl_corp,corp_id',
            'agreement_name' => 'required',
            'agreement_type' => 'required|in:0,1,2,3',
            'attachment' => 'required|mimes:pdf',
            'start_date' => 'required|date|before_or_equal:end_date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'corp_pic' => 'required|exists:tbl_corp_pic,id',
            'empl_id' => [
                'required', 'required',
                function ($attribute, $value, $fail) {
                    if (!User::with('roles')->whereHas('roles', function ($q) {
                        $q->where('role_name', 'Employee');
                    })->find($value)) {
                        $fail('The submitted pic was invalid employee');
                    }
                },
            ],

            
        ];
  
        
    }
}