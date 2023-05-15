<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCorporatePicRequest extends FormRequest
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
            'pic_name.required' => 'The full name field is required',
            'pic_name.string' => 'The full name must only contain letters',
            'pic_phone.required' => 'The phone number field is required',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $picId = $this->route('detail');
        $corporateId = $this->route('corporate');

        return [
            'pic_name' => 'required|string',
            'pic_mail' => [
                'nullable',
                'email', 
                Rule::unique('tbl_corp_pic', 'pic_mail')->where('corp_id', $corporateId)->when($picId !== null, function ($query) use ($picId) {
                    $query->ignore($picId);
                }),
            ],
            'pic_phone' => 'required',
            'pic_linkedin' => 'nullable|url',
            'is_pic' => 'required:in,true,false',
        ];
    }
}
