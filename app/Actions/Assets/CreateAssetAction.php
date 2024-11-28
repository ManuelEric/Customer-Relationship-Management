<?php

namespace App\Actions\Assets;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\AssetRepositoryInterface;
use App\Models\Asset;

class CreateAssetAction
{
    use CreateCustomPrimaryKeyTrait;
    private AssetRepositoryInterface $assetRepository;

    public function __construct(AssetRepositoryInterface $assetRepository)
    {
        $this->assetRepository = $assetRepository;
    }

    public function execute(
        Array $new_asset_details
    )
    {
        # Set label asset id
        $last_id = Asset::max('asset_id');
        $asset_id_without_label = $last_id ? $this->remove_primarykey_label($last_id, 3) : '0000';
        $asset_id_with_label = 'AS-' . $this->add_digit($asset_id_without_label + 1, 4);
        $new_asset_details['asset_id'] = $asset_id_with_label;

        # store new asset
        $new_asset = $this->assetRepository->createAsset($new_asset_details);

        return $new_asset;
    }
}