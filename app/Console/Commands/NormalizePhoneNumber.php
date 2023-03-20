<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class NormalizePhoneNumber extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'normalize:phone_number';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Normalize the phone number on the database. Replace 08 into +62';

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
