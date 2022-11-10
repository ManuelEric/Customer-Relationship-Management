<?php

namespace App\Console\Commands;

use App\Interfaces\CorporateRepositoryInterface;
use Illuminate\Console\Command;

class CleaningCorporate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleaning:corporate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleaning corporate data from value blank or empty space';

    protected CorporateRepositoryInterface $corporateRepository;

    public function __construct(CorporateRepositoryInterface $corporateRepository)
    {
        parent::__construct();
        $this->corporateRepository = $corporateRepository;
    }
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->corporateRepository->cleaningCorporate();
        return Command::SUCCESS;
    }
}
