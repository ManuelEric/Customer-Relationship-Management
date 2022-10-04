<?php

namespace App\Console\Commands;

use App\Interfaces\DepartmentRepositoryInterface;
use App\Interfaces\EmployeeRepositoryInterface;
use App\Models\Department;
use Illuminate\Console\Command;

class ImportDepartment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:department';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import employee department from big data v1 to big data v2';

    protected EmployeeRepositoryInterface $employeeRepository;
    protected DepartmentRepositoryInterface $departmentRepository;

    public function __construct(EmployeeRepositoryInterface $employeeRepository, DepartmentRepositoryInterface $departmentRepository)
    {
        parent::__construct();

        $this->employeeRepository = $employeeRepository;
        $this->departmentRepository = $departmentRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $departments = $this->employeeRepository->getDistinctDepartment();
        $this->departmentRepository->createDepartments($departments->toArray());

        return Command::SUCCESS;
    }
}
