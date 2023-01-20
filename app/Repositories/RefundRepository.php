<?php

namespace App\Repositories;

use App\Interfaces\RefundRepositoryInterface;
use App\Models\Refund;

class RefundRepository implements RefundRepositoryInterface 
{
    public function createRefund(array $refundDetails)
    {
        return Refund::create($refundDetails);
    }

    public function deleteRefund($invoiceId)
    {
        return Refund::where('inv_id', $invoiceId)->delete();
    }
}