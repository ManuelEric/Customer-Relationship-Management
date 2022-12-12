<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePartnerProgramAttachRequest extends FormRequest
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
            'partner_prog_id.required' => 'The Partner program is required',
            'corprog_file.required' => 'The File name is required',
            'corprog_attach.required' => 'The attachment is required',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $partnerProg_id = $this->route('corp_prog');
        return [

            'corprog_file' => 'required|string',
            'corprog_attach' => 'required|mimes:pdf'
        ];

        if($partnerProg_id)
            $rules = ['partner_prog_id' => 'required|exists:tbl_partner_prog,id',
            ];

        return $rules;
        
    }
}
