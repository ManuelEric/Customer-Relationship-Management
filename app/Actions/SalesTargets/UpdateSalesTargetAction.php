<?php

namespace App\Actions\SalesTargets;

use App\Interfaces\SalesTargetRepositoryInterface;
use Illuminate\Support\Facades\Artisan;

class UpdateSalesTargetAction
{
    private SalesTargetRepositoryInterface $salesTargetRepository;

    public function __construct(SalesTargetRepositoryInterface $salesTargetRepository)
    {
        $this->salesTargetRepository = $salesTargetRepository;
    }

    public function execute(
        $sales_target_id,
        Array $new_sales_target_details
    )
    {
        $new_sales_target_details['month_year'] .= '-01';

        $updated_sales_target = $this->salesTargetRepository->updateSalesTarget($sales_target_id, $new_sales_target_details);
            
        ## Update target tracking
        # running command insert target tracking
        Artisan::call('insert:target_tracking_monthly');

        return $updated_sales_target;
    }
}