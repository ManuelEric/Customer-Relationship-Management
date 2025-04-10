<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class UpdateUseProgramPhaseRequest extends FormRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'mentee_id' => $this->route('mentee'),
            'phase_detail_id' => $this->route('phase_detail'),
            'phase_lib_id' => $this->route('phase_lib') == 'null' ? null : $this->route('phase_lib'),
        ]);
    }

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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'mentee_id' => 'required|exists:tbl_client,id',
            'phase_detail_id' => 'required|exists:phase_details,id',
            'type' => 'required|in:increment,update,decrement',
            'use' => 'required|numeric|min:0'
        ];
    }
}
