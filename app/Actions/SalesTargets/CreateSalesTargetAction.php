<?php

namespace App\Actions\SalesTargets;

use App\Interfaces\SalesTargetRepositoryInterface;
use Illuminate\Support\Facades\Artisan;

class CreateSalesTargetAction
{
    private SalesTargetRepositoryInterface $salesTargetRepository;

    public function __construct(SalesTargetRepositoryInterface $salesTargetRepository)
    {
        $this->salesTargetRepository = $salesTargetRepository;
    }

    public function execute(
        Array $new_sales_target_details
    )
    {
        $new_sales_target_details['month_year'] .= '-01';

        # store new sales target
        $new_sales_target = $this->salesTargetRepository->createSalesTarget($new_sales_target_details);
       
        # running command insert target tracking
        Artisan::call('insert:target_tracking_monthly');

        return $new_sales_target;
    }
}