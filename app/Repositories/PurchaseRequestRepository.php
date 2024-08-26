<?php

namespace App\Repositories;

use App\Interfaces\PurchaseRequestRepositoryInterface;
use App\Models\PurchaseRequest;
use DataTables;
use Illuminate\Support\Facades\DB;

class PurchaseRequestRepository implements PurchaseRequestRepositoryInterface
{
    public function getAllPurchaseRequestDataTables()
    {
        return Datatables::eloquent(
            PurchaseRequest::join('tbl_department', 'tbl_department.id', '=', 'tbl_purchase_request.purchase_department')->join('users', 'users.id', '=', 'tbl_purchase_request.requested_by')->select(['tbl_purchase_request.purchase_id', 'tbl_purchase_request.purchase_statusrequest', 'tbl_purchase_request.updated_at', 'tbl_department.dept_name', 'users.first_name as fullname', DB::raw("CONCAT(users.first_name, ' ', users.last_name) AS fullname")])
        )->filterColumn(
            'fullname',
            function ($query, $keyword) {
                $sql = 'CONCAT(users.first_name," ",users.last_name) like ?';
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
        )->make(true);
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
