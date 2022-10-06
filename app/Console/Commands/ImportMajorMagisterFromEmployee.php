<?php

namespace App\Console\Commands;

use App\Interfaces\EmployeeRepositoryInterface;
use App\Interfaces\MajorRepositoryInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ImportMajorMagisterFromEmployee extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:employee_major_magister';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import employee major magister from big data v1 to big data v2';

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
        $emplMajorMagister = $this->employeeRepository->getDistinctMajorMagister();

        foreach ($emplMajorMagister as $majorMagister) {

            if (!$this->majorRepository->getMajorByName($majorMagister->empl_major_magister)) {

                $majorMagisterDetails[] = [
                    'name' => $majorMagister->empl_major_magister,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }

        $this->majorRepository->createMajors($majorMagisterDetails);
        return Command::SUCCESS;
    }
}
