<?php

namespace App\Interfaces;

interface RefundRepositoryInterface
{
    public function getRefundById($refundId);
    public function createRefund(array $refundDetails);
    public function updateRefund($refundId, array $newDetails);
    public function deleteRefundByRefundId($refundId);
    public function deleteRefund($invoiceId);
}
