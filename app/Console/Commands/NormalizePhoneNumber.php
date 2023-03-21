<?php

namespace App\Console\Commands;

use App\Interfaces\ClientRepositoryInterface;
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

    protected ClientRepositoryInterface $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepository)
    {
        parent::__construct();
        $this->clientRepository = $clientRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $clients = $this->clientRepository->getAllClients();
        foreach ($clients as $client) 
        {
            $client_phone = $client->phone;
            if (stripos($client_phone,','))
                $this->info('this number is doubled : '.$client_phone);

            # remove - from string
            // $this->info(preg_replace('/-/i',' ',$client_phone));
        }

        return Command::SUCCESS;
    }
}
