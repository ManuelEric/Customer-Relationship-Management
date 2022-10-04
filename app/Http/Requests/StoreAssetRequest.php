<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssetRequest extends FormRequest
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
        return [
            'asset_name' => 'required',
            'asset_merktype' => 'nullable',
            'asset_dateachieved' => 'nullable',
            'asset_amount' => 'nullable',
            'asset_unit' => 'nullable',
            'asset_condition' => 'nullable|in:Good,Good Enough,Not Good',
            'asset_notes' => 'nullable',
        ];
    }
}
