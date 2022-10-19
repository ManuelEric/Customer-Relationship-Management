<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportLead extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:lead';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import lead from big data v1 to big data v2';

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
