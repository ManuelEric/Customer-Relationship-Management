<?php

namespace App\Console\Commands;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\InvoiceB2bRepositoryInterface;
use App\Interfaces\InvoiceAttachmentRepositoryInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PDF;

class ImportInvoiceSchoolAttachment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:invoice_school_attachment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import invoice school attachment (generate) from big data v1 to inv_attachment big data v2';

    protected InvoiceB2bRepositoryInterface $invoiceB2bRepository;
    protected InvoiceAttachmentRepositoryInterface $invoiceAttachmentRepository;

    public function __construct(InvoiceB2bRepositoryInterface $invoiceB2bRepository, InvoiceAttachmentRepositoryInterface $invoiceAttachmentRepository)
    {
        parent::__construct();

        $this->invoiceB2bRepository = $invoiceB2bRepository;
        $this->invoiceAttachmentRepository = $invoiceAttachmentRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $invoiceSchools = $this->invoiceB2bRepository->getAllInvoiceSchool();
        $progressBar = $this->output->createProgressBar($invoiceSchools->count());
        $progressBar->start();

        $companyDetail = [
            'name' => env('ALLIN_COMPANY'),
            'address' => env('ALLIN_ADDRESS'),
            'address_dtl' => env('ALLIN_ADDRESS_DTL'),
            'city' => env('ALLIN_CITY')
        ];

        DB::beginTransaction();
        try {

            foreach ($invoiceSchools as $invSch) {
    
                $attachment = $this->invoiceAttachmentRepository->getInvoiceAttachmentByInvoiceIdentifier('B2B', $invSch->invb2b_id);
    
    
                if (isset($invSch->receipt)) {
                    if (count($attachment) == 0) {
                        $file_name = str_replace('/', '_', $invSch->invb2b_id) . '_idr.pdf'; # 0001_INV_JEI_EF_I_23_idr.pdf
                        $path = 'uploaded_file/invoice/sch_prog/';
                        $attachment = $this->invoiceAttachmentRepository->getInvoiceAttachmentByInvoiceCurrency('B2B', $invSch->invb2b_id, 'idr');
    
                        $attachmentDetails = [
                            'invb2b_id' => $invSch->invb2b_id,
                            'currency' => 'idr',
                            'attachment' => 'storage/' . $path . $file_name,
                            'sign_status' => 'signed',
                            'approve_date' => Carbon::now(),
                            'send_to_client' => 'not sent'
                        ];
    
                        $pdf = PDF::loadView('pages.invoice.school-program.export.invoice-pdf', [
                            'invoiceSch' => $invSch,
                            'currency' => 'idr',
                            'companyDetail' => $companyDetail
                        ]);
    
                        # Generate PDF file
                        $content = $pdf->download();
                        Storage::disk('public')->put($path . $file_name, $content);
    
                        # if attachment exist then update attachement else insert attachement
                        if (isset($attachment)) {
                            $this->invoiceAttachmentRepository->updateInvoiceAttachment($attachment->id, $attachmentDetails);
                        } else {
                            $this->invoiceAttachmentRepository->createInvoiceAttachment($attachmentDetails);
                        }
                    }
                }
                $progressBar->advance();
            }
            $progressBar->finish();
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            $this->info($e->getMessage());

        }

        return Command::SUCCESS;
    }
}
