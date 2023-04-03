<?php

namespace App\Console\Commands;

use App\Interfaces\InvoiceAttachmentRepositoryInterface;
use App\Interfaces\InvoiceProgramRepositoryInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PDF;

class GenerateInvoicePDF extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:invoice_pdf';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate pdf file and put it into local files';

    protected InvoiceProgramRepositoryInterface $invoiceProgramRepository;
    protected InvoiceAttachmentRepositoryInterface $invoiceAttachmentRepository;

    public function __construct(InvoiceProgramRepositoryInterface $invoiceProgramRepository, InvoiceAttachmentRepositoryInterface $invoiceAttachmentRepository)
    {
        parent::__construct();

        $this->invoiceProgramRepository = $invoiceProgramRepository;
        $this->invoiceAttachmentRepository = $invoiceAttachmentRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        
        DB::beginTransaction();
        try {

            $companyDetail = [
                'name' => env('ALLIN_COMPANY'),
                'address' => env('ALLIN_ADDRESS'),
                'address_dtl' => env('ALLIN_ADDRESS_DTL'),
                'city' => env('ALLIN_CITY')
            ];
            
            $invoices = $this->invoiceProgramRepository->getAllInvoiceProgram();
            $progressBar = $this->output->createProgressBar($invoices->count());
            $progressBar->start();
            foreach ($invoices as $invoice) {
                $invoice_id = $invoice->inv_id;
                $type = strtolower($invoice->inv_category);
                $clientProg = $invoice->clientprog;
    
                # when the invoice doesn't have an attachment
                if (!$invoice->invoiceAttachment()->exists()) {
    
                    if ($type == "idr")
                        $view = 'pages.invoice.client-program.export.invoice-pdf';
                    else
                        $view = 'pages.invoice.client-program.export.invoice-pdf-foreign';
    
                    $file_name = str_replace('/', '_', $invoice_id).'_'.$type;
                    if (!file_exists(public_path('storage/uploaded_file/invoice/client/' . $file_name.'.pdf'))) {
                        
                        # generate invoice as a PDF file
                        $pdf = PDF::loadView($view, ['clientProg' => $clientProg, 'companyDetail' => $companyDetail]);
                        Storage::put('public/uploaded_file/invoice/client/'.$file_name.'.pdf', $pdf->output());
                    }
                    
                    # initialize
                    $attachmentDetails = [
                        'inv_id' => $invoice_id,
                        'currency' => $type,
                        'sign_status' => 'not yet',
                        'send_to_client' => 'not sent',
                        'attachment' => $file_name.'.pdf'
                    ];
    
                    # insert to invoice attachment
                    $this->invoiceAttachmentRepository->createInvoiceAttachment($attachmentDetails);
    
                }
    
                $progressBar->advance();
            }
    
            DB::commit();
            $progressBar->finish();
        
        } catch (Exception $e) {

            DB::rollBack();
            Log::warning('Failed to generate invoice pdf : '. $e->getMessage() .' | Line : '. $e->getLine());

        }
        return Command::SUCCESS;
    }
}
