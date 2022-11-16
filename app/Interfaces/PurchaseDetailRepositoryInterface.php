<?php

namespace App\Interfaces;

interface PurchaseDetailRepositoryInterface 
{
    // public function getAllPurchaseDetailByPurchaseIdDataTables($purchaseRequestId);
    public function getAllPurchaseDetailByPurchaseId($purchaseRequestId);
    public function getPurchaseDetailById($purchaseDetailId);
    public function deletePurchaseDetail($purchaseDetailId);
    public function createOnePurchaseDetail(array $purchaseDetails);
    public function createManyPurchaseDetail(array $purchaseDetails);
    public function updatePurchaseDetail($purchaseDetailId, array $newDetails);
}