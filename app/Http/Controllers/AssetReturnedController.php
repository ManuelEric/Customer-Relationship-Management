<?php

namespace App\Http\Controllers;

use App\Actions\Assets\Returned\CreateAssetReturnedAction;
use App\Actions\Assets\Returned\DeleteAssetReturnedAction;
use App\Enum\LogModule;
use App\Http\Requests\StoreAssetReturnedRequest;
use App\Interfaces\AssetRepositoryInterface;
use App\Interfaces\AssetReturnedRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Models\Asset;
use App\Services\Log\LogService;
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

    public function store(StoreAssetReturnedRequest $request, CreateAssetReturnedAction $createAssetReturnedAction, LogService $log_service)
    {

        $new_returned_details = $request->safe()->only([
            'used_id',
            'asset_id',
            'user',
            'amount_returned',
            'returned_date',
            'condition',
        ]);

        DB::beginTransaction();
        try {

            $createAssetReturnedAction->execute($request, $new_returned_details);
            
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::STORE_ASSET_RETURNED, $e->getMessage(), $e->getLine(), $e->getFile(), $new_returned_details);

            return Redirect::to('master/asset/'.$request->asset_id)->withError('Failed to create asset returned');

        }

        $log_service->createSuccessLog(LogModule::STORE_ASSET_RETURNED, 'New asset returned has been added', $new_returned_details);

        return Redirect::to('master/asset/'.$request->asset_id)->withSuccess('Asset returned was successfully noted');
    }

    public function show(Request $request)
    {
        $asset_id = $request->route('asset');
        $used_id = $request->route('used');

        $asset = $this->assetRepository->getAssetById($asset_id);
        $user = $asset->userUsedAsset()->where('tbl_asset_used.id', $used_id)->first();
        
        $employees = $this->userRepository->rnGetAllUsersByRole('employee');
        
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

    public function destroy(Request $request, DeleteAssetReturnedAction $deleteAssetReturnedAction, LogService $log_service)
    {
        $asset_id = $request->route('asset');
        $returned_id = $request->route('returned');

        DB::beginTransaction();
        try {

            $deleteAssetReturnedAction->execute($asset_id, $returned_id);
            
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_ASSET_RETURNED, $e->getMessage(), $e->getLine(), $e->getFile(), ['returned_id' => $returned_id, 'asset_id' => $asset_id]);

            return Redirect::to('master/asset/'.$asset_id)->withError('Failed to delete asset returned');

        }

        $log_service->createSuccessLog(LogModule::DELETE_ASSET_RETURNED, 'Asset returned has been deleted', ['returned_id' => $returned_id, 'asset_id' => $asset_id]);

        return Redirect::to('master/asset/'.$asset_id)->withSuccess('Asset returned successfully deleted');
    }
}
