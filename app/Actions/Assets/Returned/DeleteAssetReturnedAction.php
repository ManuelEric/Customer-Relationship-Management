<?php

namespace App\Actions\Assets\Returned;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\AssetReturnedRepositoryInterface;

class DeleteAssetReturnedAction
{
    use CreateCustomPrimaryKeyTrait;
    private AssetReturnedRepositoryInterface $assetReturnedRepository;

    public function __construct(AssetReturnedRepositoryInterface $assetReturnedRepository)
    {
        $this->assetReturnedRepository = $assetReturnedRepository;
    }

    public function execute(
        String $asset_id,
        $returned_id
    )
    {
        # delete asset returned
        $deleted_asset_returned = $this->assetReturnedRepository->deleteAssetReturned($asset_id, $returned_id);

        return $deleted_asset_returned;
    }
}