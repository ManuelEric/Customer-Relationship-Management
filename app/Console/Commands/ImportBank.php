<?php

namespace App\Console\Commands;

use App\Models\Bank;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ImportBank extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:bank';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import bank data from https://bios.kemenkeu.go.id/api/ws/ref/bank';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $response = Http::get('https://bios.kemenkeu.go.id/api/ws/ref/bank');
        foreach ($response->json('data') as $value) 
        {
            Bank::firstOrCreate([
                'code' => $value['kode'],
                'bank_name' => $value['uraian']
            ]);
        }

        # additional bank that excludes from https://bios.kemenkeu.go.id/api/ws/ref/bank
        Bank::firstOrCreate([
            'code' => '023',
            'bank_name' => 'BANK UOB'
        ]);
        Bank::firstOrCreate([
            'code' => '535',
            'bank_name' => 'BANK SEABANK'
        ]);
    }
}
