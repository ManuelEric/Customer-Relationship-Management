<?php

namespace App\Actions\Assets\Used;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\AssetUsedRepositoryInterface;

class DeleteAssetUsedAction
{
    use CreateCustomPrimaryKeyTrait;
    private AssetUsedRepositoryInterface $assetUsedRepository;

    public function __construct(AssetUsedRepositoryInterface $assetUsedRepository)
    {
        $this->assetUsedRepository = $assetUsedRepository;
    }

    public function execute(
        String $asset_id,
        $used_id
    )
    {
        # delete asset used
        $deleted_asset_used = $this->assetUsedRepository->deleteAssetUsed($asset_id, $used_id);

        return $deleted_asset_used;
    }
}