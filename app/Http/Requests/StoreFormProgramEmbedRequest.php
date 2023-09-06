<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFormProgramEmbedRequest extends FormRequest
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
            'fullname.*' => 'required',
            'email.*' => 'required|email',
            'fullnumber.*' => 'required',
            
            'school' => 'required|exists:tbl_sch,sch_id',
            'graduation_year' => 'required',
            'destination_country' => 'required|exists:tbl_tag,id',
            'leadsource' => 'required|exists:tbl_lead,lead_id'
        ];
    }
}
