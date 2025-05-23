<?php

namespace App\Actions\PurchaseRequest;

use App\Http\Traits\DeleteFileIfExistTrait;
use App\Interfaces\PurchaseRequestRepositoryInterface;

class DeletePurchaseRequestAction
{
    use DeleteFileIfExistTrait;
    private PurchaseRequestRepositoryInterface $purchaseRequestRepository;

    public function __construct(PurchaseRequestRepositoryInterface $purchaseRequestRepository)
    {
        $this->purchaseRequestRepository = $purchaseRequestRepository;
    }

    public function execute(
        $purchase_id,
        $purchase
    )
    {        
        # delete program
        if ($this->purchaseRequestRepository->deletePurchaseRequest($purchase_id)) {

            if($purchase->purchase_attachment != null){
                # delete file if does exist
                $this->tnDeleteFile('project/crm/finance/', $purchase->purchase_attachment);
            }
        }

        return null;
    }
}