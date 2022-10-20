<?php

namespace App\Console\Commands;

use App\Interfaces\SchoolRepositoryInterface;
use Illuminate\Console\Command;

class CleaningSchoolDetail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleaning:school_detail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleaning school detail data from value blank, dashes or empty space';

    protected SchoolRepositoryInterface $schoolRepository;

    public function __construct(SchoolRepositoryInterface $schoolRepository)
    {
        parent::__construct();

        $this->schoolRepository = $schoolRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->schoolRepository->cleaningSchoolDetail();
        return Command::SUCCESS;
    }
}
