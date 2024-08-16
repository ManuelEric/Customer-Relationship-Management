<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:queue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $process = exec('pgrep -f "artisan queue:listen"');

        if (empty($process)) {
            // Process not found, so restart the queue worker
            exec('/usr/local/bin/php /home/u5794939/public_html/artisan queue:listen --queue=default,inv-send-to-client,inv-email-request-sign,verifying-client,verifying-client-parent,verifying-client-teacher,imports-student,imports-parent,imports-teacher,imports-client-event,imports-school-merge,verifying_client,verifying_client_parent,verifying_client_teacher,define-category-client,get-took-ia >> /dev/null 2>&1');
        }

        return Command::SUCCESS;
    }
}
