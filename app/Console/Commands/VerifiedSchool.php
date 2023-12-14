<?php

namespace App\Console\Commands;

use App\Interfaces\SchoolRepositoryInterface;
use App\Models\School;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class VerifiedSchool extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verified:school';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verified schools that fullfilling the criteria';

    private SchoolRepositoryInterface $schoolRepository;

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
        $schools = School::where(function($subQuery) {
            $subQuery->whereNotNull('sch_name')->orWhere('sch_name', '!=', '');
        })->whereNotNull('sch_type')->pluck('sch_id')->toArray();
        
        $this->schoolRepository->updateSchools($schools, ['is_verified' => 'Y']);

        return Command::SUCCESS;
    }
}
