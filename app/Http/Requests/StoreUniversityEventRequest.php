<?php

namespace App\Http\Requests;

use App\Models\Event;
use Illuminate\Foundation\Http\FormRequest;

class StoreUniversityEventRequest extends FormRequest
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
            'univ_id.required' => 'The University field is required',
            'univ_id.exists' => 'The University is invalid',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $eventId = $this->route('event');
        
        return [
            'univ_id' => [
                'required',
                'exists:tbl_univ,univ_id',
                function ($attribute, $value, $fail) use ($eventId) {
                    $event = Event::whereEventId($eventId);
                    if (in_array($value, $event->university()->pluck('tbl_univ_event.univ_id')->toArray()))
                        $fail('The university has been added');
                }
            ],
        ];
    }
}
