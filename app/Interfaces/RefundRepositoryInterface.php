<?php

namespace App\Interfaces;

interface RefundRepositoryInterface
{
    public function getAllRefundDataTables($status);
    public function getTotalRefundRequest($monthYear);
    public function getRefundById($refundId);
    public function getRefundByInvId($invoiceId);
    public function createRefund(array $refundDetails);
    public function updateRefund($refundId, array $newDetails);
    public function deleteRefundByRefundId($refundId);
    public function deleteRefund($invoiceId);
}
