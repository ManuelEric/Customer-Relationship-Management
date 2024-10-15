<?php

namespace App\Actions\SalesTargets;

use App\Interfaces\SalesTargetRepositoryInterface;

class DeleteSalesTargetAction
{
    private SalesTargetRepositoryInterface $salesTargetRepository;

    public function __construct(SalesTargetRepositoryInterface $salesTargetRepository)
    {
        $this->salesTargetRepository = $salesTargetRepository;
    }

    public function execute(
        $sales_target_id
    )
    {
        # delete sales target
        $deleted_sales_target = $this->salesTargetRepository->deleteSalesTarget($sales_target_id);

        return $deleted_sales_target;
    }
}