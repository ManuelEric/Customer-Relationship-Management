<?php

namespace App\Interfaces;

interface RefundRepositoryInterface 
{
    public function createRefund(array $refundDetails);
    public function deleteRefund($invoiceId);
}