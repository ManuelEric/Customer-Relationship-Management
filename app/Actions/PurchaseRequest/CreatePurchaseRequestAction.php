<?php

namespace App\Actions\PurchaseRequest;

use App\Http\Requests\StorePurchaseReqRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\UploadFileTrait;
use App\Interfaces\PurchaseRequestRepositoryInterface;
use App\Models\PurchaseRequest;

class CreatePurchaseRequestAction
{
    use CreateCustomPrimaryKeyTrait, UploadFileTrait;
    private PurchaseRequestRepositoryInterface $purchaseRequestRepository;

    public function __construct(PurchaseRequestRepositoryInterface $purchaseRequestRepository)
    {
        $this->purchaseRequestRepository = $purchaseRequestRepository;
    }

    public function execute(
        StorePurchaseReqRequest $request,
        Array $new_request_details
    )
    {
        # create purchase id
        $last_id = PurchaseRequest::max('purchase_id');
        $purchase_id_without_label = $this->remove_primarykey_label($last_id, 4);
        $purchase_id_with_label = 'PCS-' . $this->add_digit((int) $purchase_id_without_label + 1, 4);

        $new_request_details['purchase_id'] = $purchase_id_with_label;

        $file_name = $purchase_id_with_label;
        $new_request_details['purchase_attachment'] = pathinfo($this->tnUploadFile($request, 'purchase_attachment', $file_name, 'project/crm/finance'))['basename'];

        # store new purchase request
        $new_purchase_request = $this->purchaseRequestRepository->createPurchaseRequest($new_request_details);

        return $new_purchase_request;
    }
}