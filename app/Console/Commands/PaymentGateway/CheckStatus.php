<?php

namespace App\Console\Commands\PaymentGateway;

use App\Actions\PaymentGateway\PrismaLinkCheckStatusAction;
use App\Models\Transaction;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

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

            try {
                $this->info('Plink_ref_no : ' . $request['plink_ref_no'] . ' and merchant_ref_no : ' . $request['merchant_ref_no']);
                [$response, $result, $message] = $check_status->execute($request);
                $this->info('Result : ' . json_encode($result));
            } catch (Exception $e) {
                Log::error('Check status : ' . $e->getMessage(). ' on line ' . $e->getLine() . ' and file ' . $e->getFile() );    
                $this->error($e->getCode(), $e->getMessage());
            }
        }
    }
}
