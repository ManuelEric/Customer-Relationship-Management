<?php

namespace App\Http\Controllers;

use App\Actions\Assets\DeleteAssetReturnedAction;
use App\Actions\Assets\Returned\CreateAssetReturnedAction;
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

    public function store(StoreAssetReturnedRequest $request, CreateAssetReturnedAction $createAssetReturnedAction)
    {

        $returned_details = $request->safe()->only([
            'usedId',
            'assetId',
            'user',
            'amount_returned',
            'returned_date',
            'condition',
        ]);

        DB::beginTransaction();
        try {

            $createAssetReturnedAction->execute($request, $returned_details);
            
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store asset returned failed : ' . $e->getMessage());
            return Redirect::to('master/asset/'.$request->asset_id)->withError('Failed to create asset returned');

        }

        return Redirect::to('master/asset/'.$request->asset_id)->withSuccess('Asset returned was successfully noted');
    }

    public function show(Request $request)
    {
        $asset_id = $request->route('asset');
        $used_id = $request->route('used');

        $asset = $this->assetRepository->getAssetById($asset_id);
        $user = $asset->userUsedAsset()->where('tbl_asset_used.id', $used_id)->first();
        
        $employees = $this->userRepository->getAllUsersByRole('employee');
        
        # put view detail asset below
        return view('pages.asset.form')->with(
            [
                'asset' => $asset,
                'employees' => $employees,
                'user' => $user,
                'usedId' => $used_id
            ]
        );
    }

    public function destroy(Request $request, DeleteAssetReturnedAction $deleteAssetReturnedAction)
    {
        $asset_id = $request->route('asset');
        $returned_id = $request->route('returned');

        DB::beginTransaction();
        try {

            $deleteAssetReturnedAction->execute($asset_id, $returned_id);
            
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete asset returned failed : ' . $e->getMessage());
            return Redirect::to('master/asset/'.$asset_id)->withError('Failed to delete asset returned');

        }

        return Redirect::to('master/asset/'.$asset_id)->withSuccess('Asset returned successfully deleted');
    }
}
