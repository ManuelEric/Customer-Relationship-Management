<?php

namespace App\Console\Commands;

use App\Interfaces\ReceiptRepositoryInterface;
use App\Interfaces\InvoiceB2bRepositoryInterface;
use App\Interfaces\InvoiceDetailRepositoryInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ImportReceiptSchool extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:receipt_school';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import receipt school from big data v1 to receipt big data v2';

    protected ReceiptRepositoryInterface $receiptRepository;
    protected InvoiceB2bRepositoryInterface $invoiceB2bRepository;
    protected InvoiceDetailRepositoryInterface $invoiceDetailRepository;

    public function __construct(InvoiceB2bRepositoryInterface $invoiceB2bRepository, InvoiceDetailRepositoryInterface $invoiceDetailRepository, ReceiptRepositoryInterface $receiptRepository)
    {
        parent::__construct();

        $this->receiptRepository = $receiptRepository;
        $this->invoiceB2bRepository = $invoiceB2bRepository;
        $this->invoiceDetailRepository = $invoiceDetailRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $invoiceSchools = $this->invoiceB2bRepository->getAllInvoiceSchoolFromCRM();
        $progressBar = $this->output->createProgressBar($invoiceSchools->count());
        $progressBar->start();
        $new_receipts = [];

        DB::beginTransaction();
        try {

            foreach ($invoiceSchools as $invSch) {
                $invoiceDetails = $invSch->invoice_detail;

                # check if invoice does exist
                if ($this->invoiceB2bRepository->getInvoiceB2bByInvId($invSch->invsch_id)->count() == 0)
                    continue;

                // if (count($invoiceDetails) > 0) {
                //     foreach ($invoiceDetails as $invDetail) {
                //         if (isset($invDetail->receipt) && !$this->receiptRepository->getReceiptByInvoiceIdentifier('Installment', $invDetail->invdtl_id)) {
                //             $new_receipts[] = [
                //                 'id' => $invDetail->receipt->receipt_num,
                //                 'receipt_id' => $invDetail->receipt->receipt_id,
                //                 'receipt_cat' => 'school',
                //                 'inv_id' => null,
                //                 'invdtl_id' => $invDetail->invdtl_id,
                //                 'invb2b_id' => null,
                //                 'receipt_method' => $invDetail->receipt->receipt_mtd,
                //                 'receipt_words' => null,
                //                 'receipt_amount_idr' => $invDetail->receipt->receipt_amount,
                //                 'receipt_words_idr' => $invDetail->receipt->receipt_words,
                //                 'receipt_notes' => $invDetail->receipt->receipt_notes == '' || $invDetail->receipt->receipt_notes == '-' ? null : $invDetail->receipt->receipt_notes,
                //                 'receipt_status' => $invDetail->receipt->receipt_status,
                //                 'created_at' => $invDetail->receipt->receipt_date,
                //                 'updated_at' => $invDetail->receipt->receipt_date,
                //             ];
                //         }
                //     }
                // } else 
                if (isset($invSch->receipt) && !$this->receiptRepository->getReceiptByInvoiceIdentifier('B2B', $invSch->invsch_id) && count($invoiceDetails) == 0) {
                    $new_receipts[] = [
                        'id' => $invSch->receipt->receipt_num,
                        'receipt_id' => $invSch->receipt->receipt_id,
                        'receipt_cat' => 'school',
                        'inv_id' => null,
                        'invdtl_id' => null,
                        'invb2b_id' => $invSch->invsch_id,
                        'receipt_method' => $invSch->receipt->receipt_mtd,
                        'receipt_words' => null,
                        'receipt_amount_idr' => $invSch->receipt->receipt_amount,
                        'receipt_words_idr' => $invSch->receipt->receipt_words,
                        'receipt_notes' => $invSch->receipt->receipt_notes == '' || $invSch->receipt->receipt_notes == '-' ? null : $invSch->receipt->receipt_notes,
                        'receipt_status' => $invSch->receipt->receipt_status,
                        'created_at' => $invSch->receipt->receipt_date,
                        'updated_at' => $invSch->receipt->receipt_date,
                    ];
                }
                // }
    
                $progressBar->advance();
            }
    
            $progressBar->finish();
    
            if (count($new_receipts) > 0) {
                $this->receiptRepository->insertReceipt($new_receipts);
            }
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            $this->info($e->getMessage());

        }

        return Command::SUCCESS;
    }
}
