<?php

namespace App\Repositories;

use App\Interfaces\PurchaseRequestRepositoryInterface;
use App\Models\PurchaseRequest;
use DataTables;

class PurchaseRequestRepository implements PurchaseRequestRepositoryInterface 
{
    public function getAllPurchaseRequestDataTables()
    {
        return Datatables::eloquent(PurchaseRequest::query()->rawColumns([
            'purchase_notes', 'purchase_attachment'
        ]))->make(true);
    }

    public function getPurchaseRequestById($purchaseRequestId)
    {
        return PurchaseRequest::where('purchase_id', $purchaseRequestId)->first();
    }

    public function deletePurchaseRequest($purchaseRequestId)
    {
        return PurchaseRequest::where('purchase_id', $purchaseRequestId)->delete();
    }

    public function createPurchaseRequest(array $purchaseRequestDetails)
    {
        return PurchaseRequest::create($purchaseRequestDetails);
    }

    public function updatePurchaseRequest($purchaseRequestId, array $newDetails)
    {
        return PurchaseRequest::where('purchase_id', $purchaseRequestId)->update($newDetails);
    }
}