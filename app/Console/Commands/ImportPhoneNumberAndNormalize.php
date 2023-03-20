<?php

namespace App\Console\Commands;

use App\Interfaces\ClientRepositoryInterface;
use Illuminate\Console\Command;

class ImportPhoneNumberAndNormalize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:student_phone_number';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import and normalize phone number';

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
        return Command::SUCCESS;
    }
}
