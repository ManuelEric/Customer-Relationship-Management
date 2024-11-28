<?php

namespace App\Http\Requests;

use App\Models\Asset;
use App\Models\pivot\AssetUsed;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class StoreAssetUsedRequest extends FormRequest
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
        $assetId = $this->input('asset_id');
        $old_amount_used = $this->input('old_amount_used');

        return [
            'asset_id' => 'exists:tbl_asset,asset_id',
            'user' => 'required|exists:users,id',
            'amount_used' => ['required', 'min:1', function ($attribute, $value, $fail) use ($assetId, $old_amount_used) {

                if ($old_amount_used < $value) {
                    $fail('The amount used must be less than available stock');
                }
                // if (!Asset::where('asset_id', $assetId)->where(DB::raw('asset_amount-(asset_running_stock-'.$old_amount_used.')'), '>=', $value)->first()) {
                //     $fail('The amount used must be less than available stock');
                // }
            }],
            'used_date' => 'required',
            'condition' => 'nullable|in:null,Good,Not Good',
        ];
    }
}