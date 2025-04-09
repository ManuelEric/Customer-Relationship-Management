<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class StoreProgramPhaseRequest extends FormRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'clientprog_id' => $this->route('clientprog'),
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
            'clientprog_id' => 'required|exists:tbl_client_prog,clientprog_id',
            'phase_detail_id' => 'required|exists:phase_details,id',
            'phase_lib_id' => 'nullable|exists:phase_libraries,id',
        ];
    }
}
