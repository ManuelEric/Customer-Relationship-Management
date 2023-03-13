<?php

namespace App\Console\Commands;

use App\Interfaces\InvoiceProgramRepositoryInterface;
use Illuminate\Console\Command;

class CheckInvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:invoice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected InvoiceProgramRepositoryInterface $invoiceProgramRepository;

    public function __construct(InvoiceProgramRepositoryInterface $invoiceProgramRepository)
    {
        parent::__construct();
        $this->invoiceProgramRepository = $invoiceProgramRepository;
    }
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $diff = $this->invoiceProgramRepository->getInvoiceDifferences();
        foreach ($diff as $data) {
            $this->info('Invoice ID : '.$data->inv_id);
        }

        return Command::SUCCESS;
    }
}
