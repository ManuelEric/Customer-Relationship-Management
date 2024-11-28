<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportEventRequest extends FormRequest
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
            'event_name' => $this->get('event_name'),
            'start_date' => null, //* change the value if you want the function to receive filter by date
            'end_date' => null, //* change the value if you want the function to receive filter by date
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
            'event_name' => 'nullable|exists:tbl_events,event_title',
            'start_date' => 'nullable',
            'end_date' => 'nullable'
        ];
    }
}
