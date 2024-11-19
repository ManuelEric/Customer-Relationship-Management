<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class ReportProgramTrackingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'start_month' => $this->get('start_month') ?? Carbon::now()->startOfMonth()->format('Y-m'),
            'end_month' => $this->get('end_month') ?? Carbon::now()->endOfMonth()->format('Y-m'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'start_month' => 'nullable',
            'end_month' => 'nullable|after_or_equal:start_month'
        ];
    }
}
