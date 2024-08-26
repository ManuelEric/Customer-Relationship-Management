<?php

namespace App\Console\Commands;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\ReceiptRepositoryInterface;
use App\Interfaces\InvoiceB2bRepositoryInterface;
use App\Interfaces\ReceiptAttachmentRepositoryInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PDF;

class ImportReceiptSchoolAttachment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:receipt_school_attachment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import receipt school attachment (generate) from big data v1 to inv_attachment big data v2';

    protected ReceiptRepositoryInterface $receiptRepository;
    protected InvoiceB2bRepositoryInterface $invoiceB2bRepository;
    protected ReceiptAttachmentRepositoryInterface $receiptAttachmentRepository;

    public function __construct(InvoiceB2bRepositoryInterface $invoiceB2bRepository, ReceiptAttachmentRepositoryInterface $receiptAttachmentRepository, ReceiptRepositoryInterface $receiptRepository)
    {
        parent::__construct();

        $this->receiptRepository = $receiptRepository;
        $this->invoiceB2bRepository = $invoiceB2bRepository;
        $this->receiptAttachmentRepository = $receiptAttachmentRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $receiptSchools = $this->receiptRepository->getAllReceiptSchool();

        $companyDetail = [
            'name' => env('ALLIN_COMPANY'),
            'address' => env('ALLIN_ADDRESS'),
            'address_dtl' => env('ALLIN_ADDRESS_DTL'),
            'city' => env('ALLIN_CITY')
        ];

        $progressBar = $this->output->createProgressBar($receiptSchools->count());
        $progressBar->start();

        DB::beginTransaction();
        try {

            foreach ($receiptSchools as $receipt) {
    
                $attachment = $this->receiptAttachmentRepository->getReceiptAttachmentByReceiptId($receipt->receipt_id, 'idr');
                $invb2b_id = isset($receipt->invdtl_id) ? $receipt->invoiceInstallment->invb2b_id : $receipt->invb2b_id;
                $invoiceSch = $this->invoiceB2bRepository->getInvoiceB2bByInvId($invb2b_id)->first();
    
    
                if (!isset($attachment)) {
                    $file_name = str_replace('/', '_', $receipt->receipt_id) . '_idr.pdf'; # 0001_REC_JEI_EF_I_23_idr.pdf
                    $path = 'uploaded_file/receipt/sch_prog/';
    
                    $receiptAttachments = [
                        'receipt_id' => $receipt->receipt_id,
                        'attachment' => 'storage/' . $path . $file_name,
                        'currency' => 'idr',
                        'sign_status' => 'signed',
                        'approve_date' => Carbon::now(),
                        'send_to_client' => 'not sent'
                    ];
    
                    $pdf = PDF::loadView('pages.receipt.school-program.export.receipt-pdf', ['receiptSch' => $receipt, 'invoiceSch' => $invoiceSch, 'currency' => 'idr', 'companyDetail' => $companyDetail]);
    
    
                    # Generate PDF file
                    $content = $pdf->download();
                    Storage::disk('public')->put($path . $file_name, $content);
    
    
                    $this->receiptAttachmentRepository->createReceiptAttachment($receiptAttachments);
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
