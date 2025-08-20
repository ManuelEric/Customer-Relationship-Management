<?php

namespace App\Console\Commands\Temp;

use Illuminate\Console\Command;

class GenerateStudentClub extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:student-club';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate user stream `Student Club` for external mentor agreement based on engagement type `Professional Sharing`';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
    }
}
