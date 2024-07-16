<?php

namespace App\Console\Commands;

use App\Models\TokenLib;
use Database\Seeders\TokenLibSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;

class GenerateTokenLib extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refresh:token_lib';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh token library.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        TokenLib::truncate();
        Artisan::call('db:seed --class=TokenLibSeeder');
        return Command::SUCCESS;
    }
}
