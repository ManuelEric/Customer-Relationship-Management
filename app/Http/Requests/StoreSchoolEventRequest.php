<?php

namespace App\Http\Requests;

use App\Models\Event;
use Illuminate\Foundation\Http\FormRequest;

class StoreSchoolEventRequest extends FormRequest
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
            'sch_id.required' => 'The school field is required',
            'sch_id.exists' => 'The school field is invalid',
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
            'sch_id' => [
                'required', 
                'exists:tbl_sch,sch_id',
                function ($attribute, $value, $fail) use ($eventId) {
                    $event = Event::whereEventId($eventId);
                    if (in_array($value, $event->school()->pluck('tbl_sch_event.sch_id')->toArray()))
                        $fail('The school has been added');
                }
            ],
        ];
    }
}
