<?php

namespace App\Console\Commands;

use App\Interfaces\ClientRepositoryInterface;
use Illuminate\Console\Command;

class EndedActiveProgramClientInactive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ended:client_inactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ended all of the inactive client program when graduation year less than or equal to the current year';

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
        $potential = $this->clientRepository->getPotentialClients();

        # add condition
        $ids = $potential->where('graduation_year_real', '<', Carbon::now()->format('Y'))->where('st_statusact', 1)->pluck('id')->toArray();

        $progressBar = $this->output->createProgressBar(count($ids));
        if (count($ids) == 0 ) {
            Log::notice('No potential client found');
            return Command::SUCCESS;
        }
        
        $this->clientRepository->updateClients($ids, ['st_statusact' => 0]);
        $progressBar->finish();

        return Command::SUCCESS;
    }
}
