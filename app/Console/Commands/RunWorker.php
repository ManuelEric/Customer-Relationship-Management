<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class RunWorker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:worker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Running worker for queue';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        Artisan::call('queue:work', [
            '--queue' => 'inv-send-to-client,inv-email-request-sign',
            '--stop-when-empty' => true
        ]);
        
        Log::debug(json_encode(Artisan::output())); 

        return Command::SUCCESS;
    }
}
