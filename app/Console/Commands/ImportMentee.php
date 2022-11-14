<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportMentee extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:mentee';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import mentee/student from big data v1 into big data v2';

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
