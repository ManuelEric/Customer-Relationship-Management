<?php

namespace App\Http\Requests\Api\v1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class StoreAcceptanceRequest extends FormRequest
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
            'acceptance_id' => $this->route('acceptance')
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'univ_id' => 'required|exists:tbl_univ,univ_id',
            'major_group' => 'required',
            'major_id' => 'required|exists:tbl_major,id',
            'status' => 'required|in:waitlisted,accepted,denied,chosen',
            'is_picked' => 'required',
        ];

        if ( $this->isMethod('PUT') )
            $rules['acceptance_id'] = 'required';

        return $rules;
    }

    /**
     * Summary of attributes
     * @return array{alumni: string, major: string, major_group: string, status: string, uni_id: string}
     */
    public function attributes()
    {
        return [
            'univ_id' => 'university',
            'major_group' => 'major group',
            'major_id' => 'major',
            'status' => 'status'
        ];
    }
}
