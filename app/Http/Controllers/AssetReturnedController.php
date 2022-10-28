<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssetReturnedRequest;
use App\Interfaces\AssetRepositoryInterface;
use App\Interfaces\AssetReturnedRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Models\Asset;
use Exception;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Throwable;

class AssetReturnedController extends Controller
{

    protected AssetReturnedRepositoryInterface $assetReturnedRepository;
    protected AssetRepositoryInterface $assetRepository;
    protected UserRepositoryInterface $userRepository;

    public function __construct(AssetReturnedRepositoryInterface $assetReturnedRepository, UserRepositoryInterface $userRepository, AssetRepositoryInterface $assetRepository)
    {
        $this->assetReturnedRepository = $assetReturnedRepository;
        $this->userRepository = $userRepository;
        $this->assetRepository = $assetRepository;
    }

    public function store(StoreAssetReturnedRequest $request)
    {

        $returnedDetails = $request->only([
            'usedId',
            'assetId',
            'user',
            'amount_returned',
            'returned_date',
            'condition',
        ]);

        DB::beginTransaction();
        try {

            $returnedDetails['asset_used_id'] = $request->usedId;
            unset($returnedDetails['usedId']);

            $this->assetReturnedRepository->createAssetReturned($returnedDetails);
            
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store asset returned failed : ' . $e->getMessage());
            return Redirect::to('master/asset/'.$request->assetId)->withError($e->getMessage());

        }

        return Redirect::to('master/asset/'.$request->assetId)->withSuccess('Asset returned was successfully noted');
    }

    public function show(Request $request)
    {
        $assetId = $request->route('asset');
        $usedId = $request->route('used');

        $asset = $this->assetRepository->getAssetById($assetId);
        $user = $asset->userUsedAsset()->where('tbl_asset_used.id', $usedId)->first();
        
        $employees = $this->userRepository->getAllUsersByRole('employee');
        
        # put view detail asset below
        return view('pages.asset.form')->with(
            [
                'asset' => $asset,
                'employees' => $employees,
                'user' => $user,
                'usedId' => $usedId
            ]
        );
    }

    public function destroy(Request $request)
    {
        $assetId = $request->route('asset');
        $returnedId = $request->route('returned');

        DB::beginTransaction();
        try {

            $this->assetReturnedRepository->deleteAssetReturned($assetId, $returnedId);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete asset returned failed : ' . $e->getMessage());

        }

        return Redirect::to('master/asset/'.$assetId)->withSuccess('Asset returned successfully deleted');
    }
}
