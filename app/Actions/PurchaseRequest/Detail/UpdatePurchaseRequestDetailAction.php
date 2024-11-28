<?php

namespace App\Actions\PurchaseReques\Detail;

use App\Interfaces\PurchaseDetailRepositoryInterface;

class UpdatePurchaseRequestDetailAction
{
    private PurchaseDetailRepositoryInterface $purchaseDetailRepository;

    public function __construct(PurchaseDetailRepositoryInterface $purchaseDetailRepository)
    {
        $this->purchaseDetailRepository = $purchaseDetailRepository;
    }

    public function execute(
        $detail_id,
        $new_item_details
    )
    {        
        # update purchase request detail
        $updated_purchase_request_detail = $this->purchaseDetailRepository->updatePurchaseDetail($detail_id, $new_item_details);

        return $updated_purchase_request_detail;
    }
}