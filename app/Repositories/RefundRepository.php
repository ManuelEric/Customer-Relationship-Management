<?php

namespace App\Repositories;

use App\Interfaces\RefundRepositoryInterface;
use App\Models\Invb2b;
use App\Models\Receipt;
use App\Models\Refund;
use DataTables;
use Illuminate\Support\Facades\DB;

class RefundRepository implements RefundRepositoryInterface
{

    public function getRefundById($refundId)
    {
        return Refund::find($refundId);
    }


    public function createRefund(array $refundDetails)
    {
        return Refund::create($refundDetails);
    }

    public function updateRefund($refundId, array $newDetails)
    {
        return Refund::whereId($refundId)->update($newDetails);
    }

    public function deleteRefundByRefundId($refundId)
    {
        return Refund::whereId($refundId)->delete();
    }

    public function deleteRefund($invoiceId)
    {
        return Refund::where('inv_id', $invoiceId)->delete();
    }
}
