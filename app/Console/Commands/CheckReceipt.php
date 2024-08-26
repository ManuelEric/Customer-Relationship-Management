<?php

namespace App\Console\Commands;

use App\Interfaces\ReceiptRepositoryInterface;
use Illuminate\Console\Command;

class CheckReceipt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:receipt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected ReceiptRepositoryInterface $receiptRepository;

    public function __construct(ReceiptRepositoryInterface $receiptRepository)
    {
        parent::__construct($receiptRepository);
        $this->receiptRepository = $receiptRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $array = [];
        $diff = $this->receiptRepository->getReceiptDifferences();
        foreach ($diff as $data) {
            echo '"'.$data->receipt_id.'", ';
            // $this->info('Receipt ID : '.$data->receipt_id);
        }
        $this->info('In total : '.$diff->count());

        return Command::SUCCESS;
    }
}
