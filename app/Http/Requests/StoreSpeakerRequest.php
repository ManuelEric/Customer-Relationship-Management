<?php

namespace App\Http\Requests;

use App\Models\AgendaSpeaker;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreSpeakerRequest extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return $this->isMethod('POST') ? $this->store() : $this->update();
    }

    protected function store()
    {
        $eventId = $this->route('event');
        $startTime = $this->input('start_time');
        $endTime = $this->input('end_time');

        $rules = [
            'speaker_type' => 'required|in:internal,partner,school,university',
            'allin_speaker' => [
                'required_if:speaker_type,internal',
                function ($attribute, $value, $fail) use ($eventId) {
                    if (!User::whereHas('roles', function($query) {
                            $query->where('role_name', 'employee');
                        })->whereId($value)) {

                            $fail('The partner name is invalid');

                    } elseif ($this->input('speaker_type') == 'internal' && AgendaSpeaker::where('empl_id', $value)->where('event_id', $eventId)->first()) {

                        $fail('The ALL-In speaker has already added before, cannot add same speaker');

                    }
                    
                    
                }
            ],
            'partner_speaker' => [
                'required_if:speaker_type,partner',
                function ($attribute, $value, $fail) use ($eventId) {

                    if ($this->input('speaker_type') == 'partner' && AgendaSpeaker::where('partner_pic_id', $value)->where('event_id', $eventId)->first())
                        $fail('The partner speaker has already added before, cannot add same speaker');
                    
                }
            ],
            'school_speaker' => [
                'required_if:speaker_type,school',
                function ($attribute, $value, $fail) use ($eventId) {

                    if ($this->input('speaker_type') == 'school' && AgendaSpeaker::where('sch_pic_id', $value)->where('event_id', $eventId)->first())
                        $fail('The school speaker has already added before, cannot add same speaker');

                }
            ],
            'university_speaker' => [
                'required_if:speaker_type,university',
                function ($attribute, $value, $fail) use ($eventId) {

                    if ($this->input('speaker_type') == 'university' && AgendaSpeaker::where('univ_pic_id', $value)->where('event_id', $eventId)->first())
                        $fail('The university speaker has already added before, cannot add same speaker');

                    
                }
            ],
            'start_time' => [
                'required',
                // function ($attribute, $value, $fail) use ($eventId, $endTime) {
                //     if (!Event::where('event_startdate', 'like', $value.'%')->whereEventId($eventId)) {

                //         $fail('The start time is invalid');

                //     } elseif (AgendaSpeaker::where('event_id', $eventId)->whereBetween('start_time', [date('Y-m-d H:i:s', strtotime($value)), date('Y-m-d H:i:s', strtotime($endTime))])->count() > 0) {

                //         $fail('Another speaker has booked at '.date('H:i', strtotime($value)).' on '.date('d M Y', strtotime($value)).'. Please select an empty schedule');

                //     }
                // }
            ],
            'end_time' => [
                'required',
                // function ($attribute, $value, $fail) use ($eventId, $startTime) {
                //     if (!Event::where('event_enddate', 'like', $value.'%')->whereEventId($eventId)) {

                //         $fail('The start time is invalid');

                //     } elseif (AgendaSpeaker::where('event_id', $eventId)->whereBetween('end_time', [date('Y-m-d H:i:s', strtotime($startTime)), date('Y-m-d H:i:s', strtotime($value))])->count() > 0) {

                //         $fail('Another speaker has booked at '.date('H:i', strtotime($value)).' on '.date('d M Y', strtotime($value)).'. Please select an empty schedule');

                //     }
                // }
            ],
        ];

        switch ($this->input('speaker_type')) {

            case "Partner":
                $rules['partner_speaker'][] = 'exists:tbl_corp_pic,id';
                break; 
                
            case "School":
                $rules['school_speaker'][] = 'exists:tbl_schdetail,schdetail_id';
                break;
                
            case "University":
                $rules['university_speaker'][] = 'exists:tbl_univ_pic,id';
                break;
        }

        return $rules;
    }

    protected function update()
    {
        return [
            'status' => 'required|in:1,2',
            'agendaId' => 'required|exists:tbl_agenda_speaker,id'
        ];
    }
}
