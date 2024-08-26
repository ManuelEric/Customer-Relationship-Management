<?php

namespace App\Http\Requests;

use App\Models\Event;
use Illuminate\Foundation\Http\FormRequest;

class StoreCorporatePartnerEventRequest extends FormRequest
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
            'corp_id.required' => 'The partner field is required',
            'corp_id.exists' => 'The partner field is invalid',
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
            'corp_id' => [
                'required', 
                'exists:tbl_corp,corp_id',
                function ($attribute, $value, $fail) use ($eventId) {
                    $event = Event::whereEventId($eventId);
                    if (in_array($value, $event->partner()->pluck('tbl_corp_partner_event.corp_id')->toArray()))
                        $fail('The partner has been added');
                }
            ],
        ];
    }
}
