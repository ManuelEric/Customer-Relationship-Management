<?php

namespace App\Console\Commands;

use App\Interfaces\InvoiceB2bRepositoryInterface;
use App\Interfaces\InvoiceDetailRepositoryInterface;
use App\Interfaces\ReceiptRepositoryInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ImportInvoiceDetailSchool extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:invoice_detail_school';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import invoice detail school from big data v1 to invoice detail big data v2';

    protected ReceiptRepositoryInterface $receiptRepository;
    protected InvoiceB2bRepositoryInterface $invoiceB2bRepository;
    protected InvoiceDetailRepositoryInterface $invoiceDetailRepository;

    public function __construct(InvoiceB2bRepositoryInterface $invoiceB2bRepository, InvoiceDetailRepositoryInterface $invoiceDetailRepository, ReceiptRepositoryInterface $receiptRepository)
    {
        parent::__construct();

        $this->invoiceB2bRepository = $invoiceB2bRepository;
        $this->invoiceDetailRepository = $invoiceDetailRepository;
        $this->receiptRepository = $receiptRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $invoiceSchools = $this->invoiceB2bRepository->getAllInvoiceSchoolFromCRM();
        $new_invoiceDetails = [];
        $new_receipts = [];

        foreach ($invoiceSchools as $invSch) {
            $invoiceDetails = $invSch->invoice_detail;
            if (count($invoiceDetails) > 0) {
                foreach ($invoiceDetails as $invDetail) {
                    if (!$this->invoiceDetailRepository->getInvoiceDetailByInvB2bIdnName($invSch->invsch_id, $invDetail->invdtl_statusname)) {
                        $new_invoiceDetails = [
                            // 'invdtl_id' => $invDetail->invdtl_id,
                            'invb2b_id' => $invDetail->inv_id,
                            'inv_id' => null,
                            'invdtl_installment' => $invDetail->invdtl_statusname,
                            'invdtl_duedate' => $invDetail->invdtl_duedate,
                            'invdtl_percentage' => $invDetail->invdtl_percentage,
                            'invdtl_amount' => $invDetail->invdtl_amountusd,
                            'invdtl_amountidr' => $invDetail->invdtl_amountidr,
                            'invdtl_status' => $invDetail->invdtl_status == '' ? 0 : $invDetail->invdtl_status,
                            'invdtl_cursrate' => null,
                            'invdtl_currency' => 'idr',
                            'created_at' => $invSch->invsch_date,
                            'updated_at' => $invSch->invsch_date,
                        ];

                        if (count($new_invoiceDetails) > 0) {
                            $invoiceDtlB2b = $this->invoiceDetailRepository->createOneInvoiceDetail($new_invoiceDetails);

                            if (isset($invDetail->receipt) && !$this->receiptRepository->getReceiptByReceiptId($invDetail->receipt->receipt_id)) {
                                $new_receipts = [
                                    'id' => $invDetail->receipt->receipt_num,
                                    'receipt_id' => $invDetail->receipt->receipt_id,
                                    'receipt_cat' => 'school',
                                    'inv_id' => null,
                                    'invdtl_id' => $invoiceDtlB2b->invdtl_id,
                                    'invb2b_id' => null,
                                    'receipt_method' => $invDetail->receipt->receipt_mtd,
                                    'receipt_words' => null,
                                    'receipt_amount_idr' => $invDetail->receipt->receipt_amount,
                                    'receipt_words_idr' => $invDetail->receipt->receipt_words,
                                    'receipt_notes' => $invDetail->receipt->receipt_notes == '' || $invDetail->receipt->receipt_notes == '-' ? null : $invDetail->receipt->receipt_notes,
                                    'receipt_status' => $invDetail->receipt->receipt_status,
                                    'created_at' => $invDetail->receipt->receipt_date,
                                    'updated_at' => $invDetail->receipt->receipt_date,
                                ];

                                if (count($new_receipts) > 0) {
                                    $this->receiptRepository->createReceipt($new_receipts);
                                }
                            }
                        }
                        unset($new_invoiceDetails);
                    }
                }
            }
        }



        return Command::SUCCESS;
    }
}
