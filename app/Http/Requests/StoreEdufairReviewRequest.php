<?php

namespace App\Http\Requests;

use App\Models\EdufReview;
use Illuminate\Foundation\Http\FormRequest;

class StoreEdufairReviewRequest extends FormRequest
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
            'eduf_id' => 'required|unique:tbl_eduf_review,eduf_id',
            'reviewer_name' => 'required',
            'score' => 'required',
            'review' => 'required'
        ];
    }

    protected function update()
    {
        return [
            'eduf_id' => 'required|unique:tbl_eduf_review,eduf_id,'.$this->input('eduf_id').',eduf_id',
            'reviewer_name' => 'required',
            'score' => 'required',
            'review' => 'required'
        ];
    }
}
