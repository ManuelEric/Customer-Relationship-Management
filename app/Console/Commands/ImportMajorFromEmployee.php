<?php

namespace App\Console\Commands;

use App\Interfaces\EmployeeRepositoryInterface;
use App\Interfaces\MajorRepositoryInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ImportMajorFromEmployee extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:employee_major';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import employee major from big data v1 to big data v2';

    protected EmployeeRepositoryInterface $employeeRepository;
    protected MajorRepositoryInterface $majorRepository;

    public function __construct(EmployeeRepositoryInterface $employeeRepository, MajorRepositoryInterface $majorRepository)
    {
        parent::__construct();

        $this->employeeRepository = $employeeRepository;
        $this->majorRepository = $majorRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $emplMajor = $this->employeeRepository->getDistinctMajor();

        foreach ($emplMajor as $major) {

            if (!$this->majorRepository->getMajorByName($major->empl_major)) {
                
                $majorDetails[] = [
                    'name' => $major->empl_major,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }

        $this->majorRepository->createMajors($majorDetails);
        return Command::SUCCESS;
    }
}
