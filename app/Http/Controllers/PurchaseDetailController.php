<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseDetailRequest;
use App\Interfaces\PurchaseDetailRepositoryInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class PurchaseDetailController extends Controller
{

    private PurchaseDetailRepositoryInterface $purchaseDetailRepository;

    public function __construct(PurchaseDetailRepositoryInterface $purchaseDetailRepository)
    {
        $this->purchaseDetailRepository = $purchaseDetailRepository;
    }

    public function show(Request $request): JsonResponse
    {
        $detailId = $request->route('detail');

        $detail = $this->purchaseDetailRepository->getPurchaseDetailById($detailId);

        return response()->json(
            [
                'success' => $detail ? true : false,
                'message' => $detail ? "Detail data has been retrieved" : "Couldn't get the detail data",
                'data' => $detail ? $detail : null
            ]
        );
    }

    public function store(StorePurchaseDetailRequest $request)
    {
        $itemDetails = $request->only([
            'item',
            'amount',
            'price_per_unit',
            'total',
        ]);

        $itemDetails['purchase_id'] = $purchaseId = $request->route('purchase');

        DB::beginTransaction();
        try {

            $this->purchaseDetailRepository->createOnePurchaseDetail($itemDetails);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store requested item of Purchase Request '.$purchaseId.' failed : ' . $e->getMessage());
            return Redirect::to('master/purchase/'.$purchaseId)->withError('Failed to create a new requested item of Purchase Request '.$purchaseId);
        }

        return Redirect::to('master/purchase/'.$purchaseId)->withSuccess('Request Item of Purchase Request '.$purchaseId.' successfully added');
    }

    public function update(StorePurchaseDetailRequest $request)
    {
        $newDetails = $request->only([
            'item',
            'amount',
            'price_per_unit',
            'total',
        ]);

        $purchaseId = $request->route('purchase');
        $detailId = $request->route('detail');

        DB::beginTransaction();
        try {

            $this->purchaseDetailRepository->updatePurchaseDetail($detailId, $newDetails);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update requested item of Purchase Request '.$purchaseId.' failed : ' . $e->getMessage());
            return Redirect::to('master/purchase/'.$purchaseId)->withError('Failed to update requested item of Purchase Request '.$purchaseId);
        }

        return Redirect::to('master/purchase/'.$purchaseId)->withSuccess('Request Item of Purchase Request '.$purchaseId.' successfully updated');
    }

    public function destroy(Request $request)
    {
        $purchaseId = $request->route('purchase');
        $detailId = $request->route('detail');

        DB::beginTransaction();
        try {

            $this->purchaseDetailRepository->deletePurchaseDetail($detailId);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete requested item of Purchase Request '.$purchaseId.' failed : ' . $e->getMessage());
            return Redirect::to('master/purchase/'.$purchaseId)->withError('Failed to delete requested item of Purchase Request '.$purchaseId);

        }

        return Redirect::to('master/purchase/'.$purchaseId)->withSuccess('Requested item of Purchase Request '.$purchaseId.' successfully deleted');
    }
}
