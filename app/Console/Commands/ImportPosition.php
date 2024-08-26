<?php

namespace App\Console\Commands;

use App\Interfaces\EmployeeRepositoryInterface;
use App\Interfaces\PositionRepositoryInterface;
use Illuminate\Console\Command;

class ImportPosition extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:position';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import employee department from big data v1 to big data v2';

    protected EmployeeRepositoryInterface $employeeRepository;
    protected PositionRepositoryInterface $positionRepository;

    public function __construct(EmployeeRepositoryInterface $employeeRepository, PositionRepositoryInterface $positionRepository)
    {
        parent::__construct();

        $this->employeeRepository = $employeeRepository;
        $this->positionRepository = $positionRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $departments = $this->employeeRepository->getDistinctDepartment();
        $this->positionRepository->createPositions($departments->toArray());
        return Command::SUCCESS;
    }
}
