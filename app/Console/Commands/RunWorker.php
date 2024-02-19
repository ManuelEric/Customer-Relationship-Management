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
            '--stop-when-empty' => true,
            '--queue' => 'inv-send-to-client,
            inv-email-request-sign,
            verifying-client,
            verifying-client-parent,
            verifying-client-teacher,
            imports-student,
            imports-parent,
            imports-teacher,
            imports-client-event,
            imports-school-merge,
            default,
            verifying_client,
            verifying_client_parent,
            verifying_client_teacher',
        ]);
        
        Log::debug(json_encode(Artisan::output())); 

        return Command::SUCCESS;
    }
}
