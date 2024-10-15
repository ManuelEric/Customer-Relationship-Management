<?php

namespace App\Actions\PurchaseRequest\Detail;

use App\Http\Traits\DeleteFileIfExistTrait;
use App\Interfaces\PurchaseDetailRepositoryInterface;

class DeletePurchaseRequestDetailAction
{
    use DeleteFileIfExistTrait;
    private PurchaseDetailRepositoryInterface $purchaseDetailRepository;

    public function __construct(PurchaseDetailRepositoryInterface $purchaseDetailRepository)
    {
        $this->purchaseDetailRepository = $purchaseDetailRepository;
    }

    public function execute(
        $detail_id,
    )
    {        
        # delete purchase request detail
        $deleted_purchase_request_detail = $this->purchaseDetailRepository->deletePurchaseDetail($detail_id);

        return $deleted_purchase_request_detail;
    }
}