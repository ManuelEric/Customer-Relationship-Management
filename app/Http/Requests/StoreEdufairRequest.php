<?php

namespace App\Http\Requests;

use App\Models\Corporate;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEdufairRequest extends FormRequest
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
        $organizer = $this->input('organizer');

        return [
            'organizer' => 'required|in:school,corporate',
            'sch_id' => [
                'required_unless:organizer,corporate',
                function ($attribute, $value, $fail) use ($organizer) {
                    if ($organizer == 'school') {
                        $school = School::where('sch_id', $value)->pluck('sch_id');
                        if (!$school)
                            $fail('The school id is invalid');
                    }
                },
            ],
            'corp_id' => [
                'required_unless:organizer,school',
                function ($attribute, $value, $fail) use ($organizer) {
                    if ($organizer == 'corporate') {
                        $corporate = Corporate::where('corp_id', $value)->pluck('corp_id');
                        if (!$corporate)
                            $fail('The corporate id is invalid');
                    }
                },
            ],
            'title' => 'required',
            'location' => 'required',
            'intr_pic' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    $user = User::whereHas('roles', function ($query) {
                        $query->where('role_name', 'Client')->orWhere('role_name', 'BizDev');
                    })->pluck('id');

                    if (!$user->contains($value)) {
                        $fail('Pic has to be person from client department and bizdev department');
                    }
                }
            ],
            'ext_pic_name' => 'required',
            'ext_pic_mail' => 'required|email',
            'ext_pic_phone' => 'required',
            'first_discussion_date' => 'nullable|date|before_or_equal:last_discussion_date,event_start',
            'last_discussion_date' => 'nullable|date|after_or_equal:first_discussion_date',
            'event_start' => 'nullable|date|before_or_equal:event_end',
            'event_end' => 'nullable|date|after_or_equal:event_start',
            'status' => 'integer:0,1,2',
            'notes' => 'nullable'
        ];
    }
}
