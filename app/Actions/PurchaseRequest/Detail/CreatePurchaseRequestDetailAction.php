<?php

namespace App\Actions\PurchaseRequest\Detail;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\UploadFileTrait;
use App\Interfaces\PurchaseDetailRepositoryInterface;

class CreatePurchaseRequestDetailAction
{
    use CreateCustomPrimaryKeyTrait, UploadFileTrait;
    private PurchaseDetailRepositoryInterface $purchaseDetailRepository;

    public function __construct(PurchaseDetailRepositoryInterface $purchaseDetailRepository)
    {
        $this->purchaseDetailRepository = $purchaseDetailRepository;
    }

    public function execute(
        Array $new_item_details
    )
    {
        # store new purchase request
        $new_purchase_request = $this->purchaseDetailRepository->createOnePurchaseDetail($new_item_details);


        return $new_purchase_request;
    }
}