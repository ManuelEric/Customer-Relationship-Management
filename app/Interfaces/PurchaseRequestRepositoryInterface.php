<?php

namespace App\Interfaces;

interface PurchaseRequestRepositoryInterface 
{
    public function getAllPurchaseRequestDataTables();
    public function getPurchaseRequestById($purchaseRequestId);
    public function deletePurchaseRequest($purchaseRequestId);
    public function createPurchaseRequest(array $purchaseRequestDetails);
    public function updatePurchaseRequest($purchaseRequestId, array $newDetails);
}