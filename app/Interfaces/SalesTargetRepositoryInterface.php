<?php

namespace App\Interfaces;

interface SalesTargetRepositoryInterface
{
    public function getMonthlySalesTarget($programId, $filter);
    public function getMonthlySalesActual($programId, $filter);
    public function getSalesDetail($programId, $filter);
    public function getSalesDetailFromClientProgram(array $dateDetails, array $additionalFilter = []);
    public function getAllSalesTargetDataTables();
    public function getAllSalesTarget();
    public function getSalesTargetById($salesTargetId);
    public function deleteSalesTarget($salesTargetId);
    public function createSalesTarget(array $salesTargets);
    public function updateSalesTarget($salesTargetId, array $salesTargets);
}
