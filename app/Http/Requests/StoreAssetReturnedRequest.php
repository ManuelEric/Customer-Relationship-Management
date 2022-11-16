<?php

namespace App\Http\Requests;

use App\Models\Asset;
use Illuminate\Foundation\Http\FormRequest;

class StoreAssetReturnedRequest extends FormRequest
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
        $assetId = $this->input('assetId');
        $old_used_date = $this->input('old_used_date');
        $old_amount_used = $this->input('old_amount_used');

        return [
            'usedId' => 'exists:tbl_asset_used,id',
            'asset_id' => 'exists:tbl_asset,asset_id',
            'user' => 'required|exists:users,id',
            'amount_returned' => ['required', 'min:1', function($attribute, $value, $fail) use ($old_amount_used) {

                if ($old_amount_used < $value) {
                    $fail('The returned amount must less than used amount');
                }

                // if (Asset::where('asset_id', $assetId)->whereHas('userUsedAsset', function ($query) use ($value) {
                //         $query->where('tbl_asset_used.amount_used', '<', $value);
                //     })->first())
                // {
                //     $fail("The returned amount must less than used amount");
                // }
            }],
            'returned_date' => ['required', function ($attribute, $value, $fail) use ($assetId) {

                if (Asset::where('asset_id', $assetId)->whereHas('userUsedAsset', function ($query) use ($value) {
                    $query->where('tbl_asset_used.used_date', '>', $value);
                })->first()) {
                    $fail("The returned date must less than used date");
                }
            }],
            'condition' => 'nullable|in:null,Good,Not Good',
        ];
    }
}