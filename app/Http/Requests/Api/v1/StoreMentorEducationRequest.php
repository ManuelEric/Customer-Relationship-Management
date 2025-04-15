<?php

namespace App\Http\Requests\Api\v1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class StoreMentorEducationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
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
            'degree' => 'required|in:bachelor,master,phd',
            'univ_id' => 'required_if:other_univ_name,null',
            'other_univ_name' => 'string|required_if:univ_id,null',
            'major_id' => 'required_if:other_major_name,null',
            'other_major_name' => 'required_if:major_id,null',
            'graduation_date' => 'nullable',
        ];
    }

    public function attributes()
    {
        return [
            'univ_id' => 'university',
            'other_univ_name' => 'university name',
            'major_group_id' => 'major group',
            'major_name' => 'major name',
        ];
    }
}
