<?php

namespace App\Http\Requests;

use App\Interfaces\UserRepositoryInterface;
use Illuminate\Foundation\Http\FormRequest;

class StoreFollowupClientRequest extends FormRequest
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

    public function store()
    {
        return [
            'followup_date' => 'nullable',
            'notes' => 'nullable',
            'status' => 'required|in:0,1,2,3|integer'
        ];
    }

    public function update()
    {
        return [
            'minutes_of_meeting' => 'nullable',
            'status' => 'required|in:0,1,2,3|integer'
        ];
    }
   
}
