<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendReminderForProbation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:reminder_probation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sending a reminder to HR Team if there are employees contract that must be renewed or almost finished.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        

        return Command::SUCCESS;
    }
}
