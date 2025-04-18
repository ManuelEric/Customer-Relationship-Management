<?php

namespace App\Actions\PurchaseRequest;

use App\Http\Requests\StorePurchaseReqRequest;
use App\Http\Traits\UploadFileTrait;
use App\Interfaces\PurchaseRequestRepositoryInterface;

class UpdatePurchaseRequestAction
{
    use UploadFileTrait;
    private PurchaseRequestRepositoryInterface $purchaseRequestRepository;

    public function __construct(PurchaseRequestRepositoryInterface $purchaseRequestRepository)
    {
        $this->purchaseRequestRepository = $purchaseRequestRepository;
    }

    public function execute(
        StorePurchaseReqRequest $request,
        $purchase_id,
        Array $new_request_details
    )
    {
        
        $file_name = $purchase_id;
        $file_format = $request->file('purchase_attachment')->getClientOriginalExtension();
        $new_request_details['purchase_attachment'] = $file_name . '.' . $file_format;
        $this->tnUploadFile($request, 'purchase_attachment', $file_name, 'project/crm/finance/');

        # update purchase request
        $updated_purchase_request =  $this->purchaseRequestRepository->updatePurchaseRequest($purchase_id, $new_request_details);

        return $updated_purchase_request;
    }
}