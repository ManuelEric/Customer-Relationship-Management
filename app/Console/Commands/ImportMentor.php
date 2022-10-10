<?php

namespace App\Console\Commands;

use App\Interfaces\DepartmentRepositoryInterface;
use App\Interfaces\EmployeeRepositoryInterface;
use App\Interfaces\MajorRepositoryInterface;
use App\Interfaces\RoleRepositoryInterface;
use App\Interfaces\UniversityRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use Illuminate\Console\Command;

class ImportMentor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:mentor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import mentor from big data v1 and merge into employee in big data v2';

    protected EmployeeRepositoryInterface $employeeRepository;
    protected DepartmentRepositoryInterface $departmentRepository;
    protected UserRepositoryInterface $userRepository;
    protected RoleRepositoryInterface $roleRepository;
    protected UniversityRepositoryInterface $universityRepository;
    protected MajorRepositoryInterface $majorRepository;

    public function __construct(EmployeeRepositoryInterface $employeeRepository, DepartmentRepositoryInterface $departmentRepository, UserRepositoryInterface $userRepository, RoleRepositoryInterface $roleRepository, UniversityRepositoryInterface $universityRepository, MajorRepositoryInterface $majorRepository)
    {
        parent::__construct();

        $this->employeeRepository = $employeeRepository;
        $this->departmentRepository = $departmentRepository;
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->universityRepository = $universityRepository;
        $this->majorRepository = $majorRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        return Command::SUCCESS;
    }
}
