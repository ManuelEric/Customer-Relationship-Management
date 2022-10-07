<?php

namespace App\Console\Commands;

use App\Interfaces\DepartmentRepositoryInterface;
use App\Interfaces\EmployeeRepositoryInterface;
use App\Interfaces\RoleRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ImportEmployee extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:employee';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import employee from big data v1 to big data v2';

    protected EmployeeRepositoryInterface $employeeRepository;
    protected DepartmentRepositoryInterface $departmentRepository;
    protected UserRepositoryInterface $userRepository;
    protected RoleRepositoryInterface $roleRepository;

    public function __construct(EmployeeRepositoryInterface $employeeRepository, DepartmentRepositoryInterface $departmentRepository, UserRepositoryInterface $userRepository, RoleRepositoryInterface $roleRepository)
    {
        parent::__construct();

        $this->employeeRepository = $employeeRepository;
        $this->departmentRepository = $departmentRepository;
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
    }

    public function getRole($id)
    {
        switch ($id) {
            case 0:
                $role = 'admin';
                break;
            case 1:
                $role = 'client';
                break;
            case 2:
                $role = 'bizdev';
                break;
            case 3:
                $role = 'finance';
                break;
            case 4:
                $role = 'hr';
                break;
        }

        return $role;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $employees = $this->employeeRepository->getAllEmployees();

        foreach ($employees as $employee) {

            if (!$this->userRepository->getUserByExtendedId($employee->empl_id)) {

                $userDetails = [
                    'nip' => null,
                    'extended_id' => $employee->empl_id,
                    'first_name' => $employee->empl_firstname,
                    'last_name' => $employee->empl_lastname,
                    'address' => $employee->empl_address,
                    'email' => $employee->empl_email == '' ? null : $employee->empl_email,
                    'phone' => $employee->empl_phone == '' ? null : $employee->empl_phone,
                    'emergency_contact' => $employee->empl_emergency_contact == '' ? null : $employee->empl_emergency_contact,
                    'datebirth' => $employee->empl_datebirth,
                    'department_id' => $this->departmentRepository->getDepartmentByName($employee->empl_department)->id,
                    'password' => $employee->empl_password,
                    'hiredate' => $employee->empl_hiredate,
                    'nik' => $employee->empl_nik == '' ? null : $employee->empl_nik,
                    'idcard' => $employee->empl_idcard == '' ? null : $employee->empl_idcard,
                    'cv' => $employee->empl_cv == '' ? null : $employee->empl_cv,
                    'bankname' => $employee->empl_bankaccountname == '' ? null : $employee->empl_bankaccountname,
                    'bankacc' => $employee->empl_bankaccount == '' ? null : $employee->empl_bankaccount,
                    'npwp' => $employee->empl_npwp == '' ? null : $employee->empl_npwp,
                    'tax' => $employee->empl_tax == '' ? null : $employee->empl_tax,
                    'active' => true,
                    'health_insurance' => $employee->empl_healthinsurance == '' ? null : $employee->empl_healthinsurance,
                    'empl_insurance' => $employee->empl_emplinsurance == '' ? null : $employee->empl_emplinsurance,
                    'export' => $employee->emply_export,
                    'notes' => null,
                    'remember_token' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];

                # insert into tbl user
                $createdUser = $this->userRepository->createUser($userDetails);

                # initialize new details by role
                for ($i = 0 ; $i < 2 ; $i++) {

                    # the first role to be assigned is employee 
                    if ($i == 0 ) {

                        $userRoleDetails[] = [
                            'user_id' => $createdUser->id,
                            'role_id' => $this->roleRepository->getRoleByName('employee'),
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ];

                    }
                    # the second role to be assigned is depends on employee role from big data v1
                    else {

                        $userRoleDetails[] = [
                            'user_id' => $createdUser->id,
                            'role_id' => $this->roleRepository->getRoleByName($this->getRole($employee->empl_role)),
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ];

                    }

                }

                # insert into tbl user roles
                $createdUser->roles()->attach($userRoleDetails);

                # initialize #2
                $graduatedFrom = $employee->empl_graduatefr;
                $graduatedMajor = $employee->empl_major;

                # if she/he has more than one major on table employee v1
                # then do this
                if ( count($multiMajor = explode(';', $graduatedMajor)) > 0 ) { 
                    
                    for ($i = 0 ; $i < count($multiMajor) ; $i++) {

                        

                    }
                }
            }
        }

        return Command::SUCCESS;
    }
}
