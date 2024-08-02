<?php

namespace App\Console\Commands;

use Illuminate\Bus\Batch;
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

        $resources = [
            'default',
            'inv-send-to-client',
            'inv-email-request-sign',
            'verifying-client',
            'verifying-client-parent',
            'verifying-client-teacher',
            'imports-student',
            'imports-parent',
            'imports-teacher',
            'imports-client-event',
            'imports-school-merge',
            'verifying_client',
            'verifying_client_parent',
            'verifying_client_teacher',
            'define-category-client',
            'get-took-ia'
        ];

        Artisan::call('queue:listen', [
            // '--stop-when-empty' => 1,
            // '--daemon' => 1,
            '--queue' => implode(',', $resources)
        ]);
        
        
        Log::debug('Workers has been ran : '.json_encode(Artisan::output()));

        

        return Command::SUCCESS;
    }
}
