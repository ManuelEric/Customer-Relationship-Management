<?php

namespace App\Console\Commands\PaymentGateway;

use App\Actions\PaymentGateway\PrismaLinkCheckStatusAction;
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
    public function handle(
        PrismaLinkCheckStatusAction $check_status
    )
    {
        $transactions = Transaction::whereNot('payment_status', 'SETLD')->get();
        foreach ($transactions as $trx)
        {
            $request = [
                'plink_ref_no' => $trx->plink_ref_no,
                'merchant_ref_no' => $trx->merchant_ref_no
            ];
            $this->info('Plink_ref_no : ' . $request['plink_ref_no'] . ' and merchant_ref_no : ' . $request['merchant_ref_no']);
            $result = $check_status->execute($request);
            $this->info('Result : ' . json_encode($result));
        }
    }
}
