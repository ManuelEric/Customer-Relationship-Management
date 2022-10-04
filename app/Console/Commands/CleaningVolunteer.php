<?php

namespace App\Console\Commands;

use App\Interfaces\VolunteerRepositoryInterface;
use Illuminate\Console\Command;

class CleaningVolunteer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleaning:volunteer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleaning volunteer data from blank or empty space';

    private VolunteerRepositoryInterface $volunteerRepository;

    public function __construct(VolunteerRepositoryInterface $volunteerRepository)
    {
        parent::__construct();

        $this->volunteerRepository = $volunteerRepository;
    }
    

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->volunteerRepository->cleaningVolunteer();
        return Command::SUCCESS;
    }
}
