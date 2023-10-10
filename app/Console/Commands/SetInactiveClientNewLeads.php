<?php

namespace App\Console\Commands;

use App\Interfaces\ClientRepositoryInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class SetInactiveClientNewLeads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set:inactive_client_new_leads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set status new leads client to inactive when graduation year less than or equal to current year';

    private ClientRepositoryInterface $clientRepository;

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
        $new_leads = $this->clientRepository->getNewLeads();

        # add condition
        $ids = $new_leads->where('graduation_year_real', '<=', Carbon::now()->format('Y'))->where('st_statusact', 1)->pluck('id')->toArray();

        $progressBar = $this->output->createProgressBar(count($ids));
        if (count($ids) == 0 ) {
            Log::notice('No new leads client found');
            return Command::SUCCESS;
        }
        
        $this->clientRepository->updateClients($ids, ['st_statusact' => 0]);
        $progressBar->finish();

        return Command::SUCCESS;
    }
}
