<?php

namespace App\Http\Requests\Client\Registration\Public;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class PublicRegistrationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function failedValidation(Validator $validator): JsonResponse
    {
        $errors = (new ValidationException($validator))->errors();

        throw new HttpResponseException(
            response()->json([
                'message' => "",
                'errors' => $errors
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        );
    }

    public function prepareForValidation()
    {
        $this->merge([
            'scholarship' => 'N',
            'lead_source_id' => $this->input('lead_id')
        ]);
    }

    public function messages()
    {
        return [
            'secondary_name.required_if' => 'The child name field is required.',
            'school_id.required_if' => 'The school field is required.',
            'school_id.exists' => 'The school field is not valid.',
            'destination_country.*.exists' => 'The destination country must be one of the following values.',
            'lead_source_id.required' => 'Something is not right.', # we hide the lead_id because lead_id comes from get parameter so user should not know
            'lead_source_id.exists' => 'Something is not right.', # we hide the lead_id because lead_id comes from get parameter so user should not know
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'role' => 'required|in:parent,student,teacher/counsellor',
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
            'graduation_year' => 'required_if:role,student,parent',
            'destination_country' => 'nullable|array',
            'destination_country.*' => 'exists:tbl_country,id',
            'interest_prog' => 'nullable|exists:tbl_prog,prog_id',
            'lead_source_id' => 'required|exists:tbl_lead,lead_id',
            'scholarship' => 'nullable' # possibly N or Y
        ];
    }
}
