<?php

namespace App\Console\Commands;

use App\Interfaces\InvoiceB2bRepositoryInterface;
use App\Interfaces\InvoiceDetailRepositoryInterface;
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

    protected InvoiceB2bRepositoryInterface $invoiceB2bRepository;
    protected InvoiceDetailRepositoryInterface $invoiceDetailRepository;

    public function __construct(InvoiceB2bRepositoryInterface $invoiceB2bRepository, InvoiceDetailRepositoryInterface $invoiceDetailRepository)
    {
        parent::__construct();

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
        $new_invoiceDetails = [];

        foreach ($invoiceSchools as $invSch) {
            $invoiceDetails = $invSch->invoice_detail;
            if (count($invoiceDetails) > 0) {
                foreach ($invoiceDetails as $invDetail) {
                    if (!$this->invoiceDetailRepository->getInvoiceDetailByInvB2bIdnName($invSch->invsch_id, $invDetail->invdtl_statusname)) {
                        $new_invoiceDetails[] = [
                            'invdtl_id' => $invDetail->invdtl_id,
                            'invb2b_id' => $invDetail->inv_id,
                            'inv_id' => null,
                            'invdtl_installment' => $invDetail->invdtl_statusname,
                            'invdtl_duedate' => $invDetail->invdtl_duedate,
                            'invdtl_percentage' => $invDetail->invdtl_percentage,
                            'invdtl_amount' => $invDetail->invdtl_amountusd,
                            'invdtl_amountidr' => $invDetail->invdtl_amountidr,
                            'invdtl_status' => $invDetail->invdtl_status,
                            'invdtl_cursrate' => null,
                            'invdtl_currency' => 'idr',
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ];
                    }
                }
            }
        }
        if (count($new_invoiceDetails) > 0) {
            $this->invoiceDetailRepository->createInvoiceDetail($new_invoiceDetails);
        }

        return Command::SUCCESS;
    }
}
