<?php

namespace App\Actions\Assets;

use App\Interfaces\AssetRepositoryInterface;

class DeleteAssetAction
{
    private AssetRepositoryInterface $assetRepository;

    public function __construct(AssetRepositoryInterface $assetRepository)
    {
        $this->assetRepository = $assetRepository;
    }

    public function execute(
        String $asset_id
    )
    {
        # Update asset
        $deleted_asset = $this->assetRepository->deleteAsset($asset_id);

        return $deleted_asset;
    }
}