<?php

namespace App\Interfaces;

interface SalesTargetRepositoryInterface
{
    public function getAllSalesTargetDataTables();
    public function getSalesTargetById($salesTargetId);
    public function deleteSalesTarget($salesTargetId);
    public function createSalesTarget(array $salesTargets);
    public function updateSalesTarget($salesTargetId, array $salesTargets);
}
