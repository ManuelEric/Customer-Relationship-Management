<?php

namespace App\Http\Controllers;

use App\Actions\PurchaseReques\Detail\UpdatePurchaseRequestDetailAction;
use App\Actions\PurchaseRequest\Detail\CreatePurchaseRequestDetailAction;
use App\Actions\PurchaseRequest\Detail\DeletePurchaseRequestDetailAction;
use App\Enum\LogModule;
use App\Http\Requests\StorePurchaseDetailRequest;
use App\Interfaces\PurchaseDetailRepositoryInterface;
use App\Services\Log\LogService;
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
        $detail_id = $request->route('detail');

        $detail = $this->purchaseDetailRepository->getPurchaseDetailById($detail_id);

        return response()->json(
            [
                'success' => $detail ? true : false,
                'message' => $detail ? "Detail data has been retrieved" : "Couldn't get the detail data",
                'data' => $detail ? $detail : null
            ]
        );
    }

    public function store(StorePurchaseDetailRequest $request, CreatePurchaseRequestDetailAction $createPurchaseRequestDetailAction, LogService $log_service)
    {
        $new_item_details = $request->safe()->only([
            'item',
            'amount',
            'price_per_unit',
            'total',
        ]);

        $new_item_details['purchase_id'] = $purchase_id = $request->route('purchase');

        DB::beginTransaction();
        try {

            $new_item = $createPurchaseRequestDetailAction->execute($new_item_details);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::STORE_PURCHASE_REQUEST_DETAIL, $e->getMessage(), $e->getLine(), $e->getFile(), $new_item_details);

            return Redirect::to('master/purchase/'.$purchase_id)->withError('Failed to create a new requested item of Purchase Request '.$purchase_id);
        }

        $log_service->createSuccessLog(LogModule::STORE_PURCHASE_REQUEST_DETAIL, 'Asset request detail has been added', $new_item->toArray());
        return Redirect::to('master/purchase/'.$purchase_id)->withSuccess('Request Item of Purchase Request '.$purchase_id.' successfully added');
    }

    public function update(StorePurchaseDetailRequest $request, UpdatePurchaseRequestDetailAction $updatePurchaseRequestDetailAction, LogService $log_service)
    {
        $new_item_details = $request->safe()->only([
            'item',
            'amount',
            'price_per_unit',
            'total',
        ]);

        $purchase_id = $request->route('purchase');
        $detail_id = $request->route('detail');

        DB::beginTransaction();
        try {

            $updated_purchase_request_detail = $updatePurchaseRequestDetailAction->execute($detail_id, $new_item_details);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::UPDATE_PURCHASE_REQUEST_DETAIL, $e->getMessage(), $e->getLine(), $e->getFile(), $new_item_details);

            return Redirect::to('master/purchase/'.$purchase_id)->withError('Failed to update requested item of Purchase Request '.$purchase_id);
        }

        $log_service->createSuccessLog(LogModule::UPDATE_PURCHASE_REQUEST_DETAIL, 'Purchase request detail has been updated', $updated_purchase_request_detail->toArray());

        return Redirect::to('master/purchase/'.$purchase_id)->withSuccess('Request Item of Purchase Request '.$purchase_id.' successfully updated');
    }

    public function destroy(Request $request, DeletePurchaseRequestDetailAction $deletePurchaseRequestDetailAction, LogService $log_service)
    {
        $purchase_id = $request->route('purchase');
        $detail_id = $request->route('detail');

        DB::beginTransaction();
        try {

            $deletePurchaseRequestDetailAction->execute($detail_id);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_PURCHASE_REQUEST_DETAIL, $e->getMessage(), $e->getLine(), $e->getFile(), ['detail_id' => $detail_id]);

            return Redirect::to('master/purchase/'.$purchase_id)->withError('Failed to delete requested item of Purchase Request '.$purchase_id);

        }
        
        $log_service->createSuccessLog(LogModule::DELETE_PURCHASE_REQUEST_DETAIL, 'Purchase request detail has been deleted', ['detail_id' => $detail_id]);

        return Redirect::to('master/purchase/'.$purchase_id)->withSuccess('Requested item of Purchase Request '.$purchase_id.' successfully deleted');
    }
}
