<?php

namespace App\Interfaces;

interface SalesTargetRepositoryInterface
{
    public function getMonthlySalesTarget($programId, $filter);
    public function getMonthlySalesActual($programId, $filter);
    public function getSalesDetail($programId, $filter);
    public function rnGetSalesDetailFromClientProgram(array $date_details, array $additional_filter = []);
    public function getAllSalesTargetDataTables();
    public function getAllSalesTarget();
    public function getSalesTargetById($salesTargetId);
    public function deleteSalesTarget($salesTargetId);
    public function createSalesTarget(array $salesTargets);
    public function updateSalesTarget($salesTargetId, array $salesTargets);
}
