<?php

namespace App\Console\Commands;

use App\Interfaces\ClientRepositoryInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class SetInactiveClientToTrash extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set:inactive_client_to_trash';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set inactive client to trash';

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
        $clients = $this->clientRepository->getAllClients();

        # add condition
        $inactiveClients = $clients->where('st_statusact', 0);

        $progressBar = $this->output->createProgressBar(count($inactiveClients));

        foreach ($inactiveClients as $inactiveClient) {
            $this->clientRepository->updateClient($inactiveClient->id, ['deleted_at' => $inactiveClient->updated_at]);
        }
        
        $progressBar->finish();

        return Command::SUCCESS;
    }
}
