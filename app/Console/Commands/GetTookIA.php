<?php

namespace App\Console\Commands;

use App\Interfaces\ClientRepositoryInterface;
use App\Services\JobBatchService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GetTookIA extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:took_ia {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically dispatch process get took ia.';


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
        $type = $this->argument('type');

        Log::info('Cron dispatch took ia '.$type.' works fine.');

        switch ($type) {
            case 'new-lead':
                (new JobBatchService())->jobBatch(Collect($this->clientRepository->getClientsByCategory('new-lead')->where('took_ia', 0)->pluck('uuid')), 'took-ia');
                break;

            case 'potential':
                (new JobBatchService())->jobBatch(Collect($this->clientRepository->getClientsByCategory('potential')->where('took_ia', 0)->pluck('uuid')), 'took-ia');
                break;

            case 'mentee':
                (new JobBatchService())->jobBatch(Collect($this->clientRepository->getClientsByCategory('mentee')->where('took_ia', 0)->pluck('uuid')), 'took-ia');
                break;

            case 'non-mentee':
                (new JobBatchService())->jobBatch(Collect($this->clientRepository->getClientsByCategory('non-mentee')->where('took_ia', 0)->pluck('uuid')), 'took-ia');
                break;

            case 'alumni-mentee':
                (new JobBatchService())->jobBatch(Collect($this->clientRepository->getClientsByCategory('alumni-mentee')->where('took_ia', 0)->pluck('uuid')), 'took-ia');
                break;

            case 'alumni-non-mentee':
                (new JobBatchService())->jobBatch(Collect($this->clientRepository->getClientsByCategory('alumni-non-mentee')->where('took_ia', 0)->pluck('uuid')), 'took-ia');
                break;
        
        }
       
        return Command::SUCCESS;
    }
}
