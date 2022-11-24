<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUniversityPicRequest extends FormRequest
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
            'name' => 'required',
            'title' => 'required|in:Admissions Advisor,Former Admission Officer,new',
            'phone' => 'required',
            'email' => 'required|email',
            'other_title' => 'required_if:title,new|not_in:Admissions Advisor,Former Admission Officer',
        ];
    }
}
