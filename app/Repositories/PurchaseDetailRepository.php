<?php

namespace App\Repositories;

use App\Interfaces\PurchaseDetailRepositoryInterface;
use App\Models\PurchaseDetail;
use DataTables;

class PurchaseDetailRepository implements PurchaseDetailRepositoryInterface 
{
    public function getAllPurchaseDetailByPurchaseId($purchaseRequestId)
    {
        return PurchaseDetail::where('purchase_id', $purchaseRequestId)->get();
    }

    public function getPurchaseDetailById($purchaseDetailId)
    {
        return PurchaseDetail::find($purchaseDetailId);
    }

    public function deletePurchaseDetail($purchaseDetailId)
    {
        return PurchaseDetail::destroy($purchaseDetailId);
    }

    public function createOnePurchaseDetail(array $purchaseDetails)
    {
        return PurchaseDetail::create($purchaseDetails);
    }

    public function createManyPurchaseDetail(array $purchaseDetails)
    {
        return PurchaseDetail::insert($purchaseDetails);
    }

    public function updatePurchaseDetail($purchaseDetailId, array $newDetails)
    {
        return PurchaseDetail::find($purchaseDetailId)->update($newDetails);
    }

}