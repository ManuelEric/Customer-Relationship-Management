<?php

namespace App\Http\Controllers;

use App\Actions\Assets\CreateAssetAction;
use App\Actions\Assets\DeleteAssetAction;
use App\Actions\Assets\UpdateAssetAction;
use App\Http\Requests\StoreAssetRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\LoggingTrait;
use App\Interfaces\AssetRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Models\Asset;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class AssetController extends Controller
{
    use CreateCustomPrimaryKeyTrait;
    use LoggingTrait;

    private AssetRepositoryInterface $assetRepository;
    private UserRepositoryInterface $userRepository;

    public function __construct(AssetRepositoryInterface $assetRepository, UserRepositoryInterface $userRepository)
    {
        $this->assetRepository = $assetRepository;
        $this->userRepository = $userRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->assetRepository->getAllAssetsDataTables();
        }

        return view('pages.master.asset.index');
    }

    public function store(StoreAssetRequest $request, CreateAssetAction $createAssetAction)
    {
        $new_asset_details = $request->safe()->only([
            'asset_name',
            'asset_merktype',
            'asset_dateachieved',
            'asset_amount',
            'asset_unit',
            'asset_condition',
            'asset_notes',
        ]);

        
        DB::beginTransaction();
        try {

            $new_asset = $createAssetAction->execute($new_asset_details);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store asset failed : ' . $e->getMessage());
            return Redirect::to('master/asset/' . $new_asset->id)->withError('Failed to create asset');
        }

        # store Success
        # create log success
        // $this->logSuccess('store', 'Form Input', 'Asset', Auth::user()->first_name . ' '. Auth::user()->last_name, $new_asset);

        return Redirect::to('master/asset/' . $new_asset->id)->withSuccess('Asset successfully created');
    }

    public function create()
    {
        return view('pages.master.asset.form')->with(
            [
                'edit' => true,
            ]
        );
    }

    public function show(Request $request)
    {
        $asset_id = $request->route('asset');

        $asset = $this->assetRepository->getAssetById($asset_id);

        $employees = $this->userRepository->getAllUsersByRole('employee');

        # put view detail asset below
        return view('pages.master.asset.form')->with(
            [
                'asset' => $asset,
                'employees' => $employees,
                'request' => $request
            ]
        );
    }

    public function edit(Request $request)
    {
        $asset_id = $request->route('asset');

        # retrieve asset data by id
        $asset = $this->assetRepository->getAssetById($asset_id);
        # put the link to update asset form below
        # example

        return view('pages.master.asset.form')->with(
            [
                'edit' => true,
                'asset' => $asset,
            ]
        );
    }

    public function update(StoreAssetRequest $request, UpdateAssetAction $updateAssetAction)
    {
        $new_asset_details = $request->safe()->only([
            'asset_name',
            'asset_merktype',
            'asset_dateachieved',
            'asset_amount',
            'asset_unit',
            'asset_condition',
            'asset_notes',
        ]);

        # retrieve asset id from url
        $asset_id = $request->route('asset');
        $old_asset = $this->assetRepository->getAssetById($asset_id);

        DB::beginTransaction();
        try {

            $updated_asset = $updateAssetAction->execute($asset_id, $new_asset_details);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update asset failed : ' . $e->getMessage());
            return Redirect::to('master/asset/'.$asset_id)->withError('Failed to update asset');
        }

        # Update success
        # create log success
        // $this->logSuccess('update', 'Form Input', 'Asset', Auth::user()->first_name . ' '. Auth::user()->last_name, $updated_asset, $old_asset);

        return Redirect::to('master/asset/'.$asset_id)->withSuccess('Asset successfully updated');
    }

    public function destroy(Request $request, DeleteAssetAction $deleteAssetAction)
    {
        $asset_id = $request->route('asset');
        $asset = $this->assetRepository->getAssetById($asset_id);

        DB::beginTransaction();
        try {

            $deleted_asset = $deleteAssetAction->execute($asset_id);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete asset failed : ' . $e->getMessage());
            return Redirect::to('master/asset')->withError('Failed to delete asset');
        }

        # Delete success
        # create log success
        // $this->logSuccess('delete', null, 'Asset', Auth::user()->first_name . ' '. Auth::user()->last_name, $asset);

        return Redirect::to('master/asset')->withSuccess('Asset successfully deleted');
    }
}
