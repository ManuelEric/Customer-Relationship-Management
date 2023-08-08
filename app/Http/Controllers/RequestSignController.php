<?php

namespace App\Http\Controllers;

use App\Interfaces\InvoiceProgramRepositoryInterface;
use App\Interfaces\ReceiptRepositoryInterface;
use Illuminate\Http\Request;

class RequestSignController extends Controller
{
    protected InvoiceProgramRepositoryInterface $invoiceProgramRepository;
    protected ReceiptRepositoryInterface $receiptRepository;

    public function __construct(InvoiceProgramRepositoryInterface $invoiceProgramRepository, ReceiptRepositoryInterface $receiptRepository)
    {
        $this->invoiceProgramRepository = $invoiceProgramRepository;
        $this->receiptRepository = $receiptRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            switch ($request->type) {

                case "invoice":
                    $model = $this->invoiceProgramRepository->getInvoicesNeedToBeSigned(true);
                    $response = $this->invoiceProgramRepository->getDatatables($model);
                    break;

                case "receipt":
                    $model = $this->receiptRepository->getReceiptsNeedToBeSigned(true);
                    $response = $this->receiptRepository->getDatatables($model);
                    break;

            }

            return $response;
        }

        $total_invoiceNeedToBeSigned = $this->invoiceProgramRepository->getInvoicesNeedToBeSigned(false)->count();
        $total_receiptNeedToBeSigned = $this->receiptRepository->getReceiptsNeedToBeSigned(false)->count();

        return view('pages.request-sign.index')->with(
            [
                'total_invoiceNeedToBeSigned' => $total_invoiceNeedToBeSigned,
                'total_receiptNeedToBeSigned' => $total_receiptNeedToBeSigned
            ]
        );
    }
}
