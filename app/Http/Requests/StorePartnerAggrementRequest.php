<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StorePartnerAggrementRequest extends FormRequest
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

    // public function messages()
    // {
    //     return [
    //         'required_if' => 'The :attribute field is required',
    //     ];
    // }

    public function attributes()
    {
        return [
            'empl_id' => 'PIC',
        ];
    }

    public function rules()
    {

        return [
            // 'corp_id' => 'required|exists:tbl_corp,corp_id',
            'jenis_aggrement' => 'required|string',
            'attachment' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
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