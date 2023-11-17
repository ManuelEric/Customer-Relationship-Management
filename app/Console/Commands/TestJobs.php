<?php

namespace App\Console\Commands;

use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\InvoiceAttachmentRepositoryInterface;
use App\Jobs\Invoice\ProcessEmailToClientJob;
use App\Repositories\ClientProgramRepository;
use Illuminate\Console\Command;

class TestJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:jobs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a test jobs';

    private ClientProgramRepositoryInterface $clientProgramRepository;
    private InvoiceAttachmentRepositoryInterface $invoiceAttachmentRepository;

    public function __construct(ClientProgramRepositoryInterface $clientProgramRepository, InvoiceAttachmentRepositoryInterface $invoiceAttachmentRepository)
    {
        parent::__construct();
        $this->clientProgramRepository = $clientProgramRepository;
        $this->invoiceAttachmentRepository = $invoiceAttachmentRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $clientProg = $this->clientProgramRepository->getClientProgramById(2102);
        $invoice = $clientProg->invoice;
        $invoice_id = $invoice->inv_id;
        $currency = 'idr';
        $attachment = $invoice->invoiceAttachment()->where('currency', $currency)->first();

        $data['email'] = 'manuel.eric@all-inedu.com';
        $data['recipient'] = 'Eric';
        $data['cc'] = [];
        $data['param'] = [
            'clientprog_id' => 2102,
            'program_name' => 'Innovators-in-Residence: Senior Program'
        ];
        $data['title'] = "Test";
        ProcessEmailToClientJob::dispatch($data, $attachment, $this->invoiceAttachmentRepository)->onQueue('inv-send-to-client');

        return Command::SUCCESS;
    }
}
