<?php

namespace App\Http\Requests;

use App\Models\Asset;
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
        return $this->isMethod('POST') ? $this->store() : $this->update();
        
    }

    public function store()
    {
        return [
            'asset_name' => 'required',
            'asset_merktype' => 'nullable',
            'asset_dateachieved' => 'nullable',
            'asset_amount' => 'required',
            'asset_unit' => 'required|alpha',
            'asset_condition' => 'required|in:Good,Good Enough,Not Good',
            'asset_notes' => 'nullable',
        ];
    }

    public function update()
    {
        $assetId = $this->route('asset');
        $asset = Asset::where('asset_id', $assetId)->first();
        $updatedAmount = $this->input('asset_amount');

        return [
            'asset_name' => 'required',
            'asset_merktype' => 'nullable',
            'asset_dateachieved' => 'nullable',
            'asset_amount' => [
                'required',
                function ($attribute, $value, $fail) use ($asset, $updatedAmount) {
                    if ($asset->asset_running_stock < $asset->asset_amount && $updatedAmount < $asset->asset_amount) {
                        $fail('Cannot update the amount because the asset is already running.');
                    }
                }
            ],
            'asset_unit' => 'required|alpha',
            'asset_condition' => 'required|in:Good,Good Enough,Not Good',
            'asset_notes' => 'nullable',
        ];
    }
}
