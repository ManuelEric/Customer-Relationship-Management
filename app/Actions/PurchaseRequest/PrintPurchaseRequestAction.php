<?php

namespace App\Actions\PurchaseRequest;

use App\Http\Traits\UploadFileTrait;
use App\Interfaces\PurchaseRequestRepositoryInterface;
use App\Interfaces\PurchaseDetailRepositoryInterface;
use Barryvdh\DomPDF\Facade\Pdf;

class PrintPurchaseRequestAction
{
    use UploadFileTrait;
    private PurchaseRequestRepositoryInterface $purchaseRequestRepository;
    private PurchaseDetailRepositoryInterface $purchaseDetailRepository;

    public function __construct(PurchaseRequestRepositoryInterface $purchaseRequestRepository, PurchaseDetailRepositoryInterface $purchaseDetailRepository)
    {
        $this->purchaseRequestRepository = $purchaseRequestRepository;
        $this->purchaseDetailRepository = $purchaseDetailRepository;
    }

    public function execute(
        $purchase_id,
    )
    {
        $data = [
            'purchase' => $this->purchaseRequestRepository->getPurchaseRequestById($purchase_id),
            'details' => $this->purchaseDetailRepository->getAllPurchaseDetailByPurchaseId($purchase_id)
        ];

        $pdf = Pdf::loadView('pages.master.purchase.print', $data);

        return $pdf;
    }
}