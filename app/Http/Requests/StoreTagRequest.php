<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTagRequest extends FormRequest
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
        return [
            'name' => 'required|string|max:50|unique:tbl_tag,name',
            'score' => 'required|integer',
        ];
    }

    protected function update()
    {
        return [
            'name' => 'required|string|max:50',
            'score' => 'required|integer',
        ];
    }
}
