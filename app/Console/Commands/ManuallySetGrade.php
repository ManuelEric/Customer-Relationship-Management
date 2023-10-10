<?php

namespace App\Console\Commands;

use App\Http\Traits\CalculateGradeTrait;
use App\Interfaces\ClientRepositoryInterface;
use Illuminate\Console\Command;

class ManuallySetGrade extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'manually:set_client_grade';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manually set client grade';

    use CalculateGradeTrait;
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
        $requested_month = ['09', '10'];
        $clients = $this->clientRepository->getClientByMonthCreatedAt($requested_month);
        $progressBar = $this->output->createProgressBar(count($clients));
        
        $progressBar->start();
        foreach ($clients as $client) {

            if ($client->graduation_year == NULL) 
                continue;

    
            $newDetails = [
                'st_grade' => $this->getGradeByGraduationYear($client->graduation_year)
            ];

            $this->clientRepository->updateClient($client->id, $newDetails);

            $progressBar->advance();
        }

        $progressBar->finish();

        return Command::SUCCESS;
    }
}
