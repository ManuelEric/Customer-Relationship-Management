<?php

namespace App\Console\Commands\PaymentGateway;

use App\Models\Transaction;
use Illuminate\Console\Command;

class CheckStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:check-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checking payment status of an Invoice using prismalink check status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $transactions = Transaction::whereNot('payment_status', 'SETLD')->get();
        foreach ($transactions as $trx)
        {
            
        }
    }
}
