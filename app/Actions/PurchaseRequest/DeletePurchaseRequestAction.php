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

            # delete file if does exist
            $this->tnDeleteFile('storage/uploaded_file/finance/', $purchase->purchase_attachment);
        }

        return null;
    }
}