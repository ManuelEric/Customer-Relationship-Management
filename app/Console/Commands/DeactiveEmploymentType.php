<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DeactiveEmploymentType extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deactivate:unused-employment-type';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactive oldest employment type if user has multiple active employment type';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
    }
}
