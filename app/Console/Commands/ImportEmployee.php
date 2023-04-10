<?php

namespace App\Console\Commands;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Interfaces\PositionRepositoryInterface;
use App\Interfaces\EmployeeRepositoryInterface;
use App\Interfaces\MajorRepositoryInterface;
use App\Interfaces\RoleRepositoryInterface;
use App\Interfaces\UniversityRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\UserTypeRepositoryInterface;
use App\Models\University;
use App\Models\User;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportEmployee extends Command
{
    use CreateCustomPrimaryKeyTrait;
    use StandardizePhoneNumberTrait;
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
    protected PositionRepositoryInterface $positionRepository;
    protected UserRepositoryInterface $userRepository;
    protected RoleRepositoryInterface $roleRepository;
    protected UniversityRepositoryInterface $universityRepository;
    protected MajorRepositoryInterface $majorRepository;
    protected UserTypeRepositoryInterface $userTypeRepository;

    public function __construct(EmployeeRepositoryInterface $employeeRepository, PositionRepositoryInterface $positionRepository, UserRepositoryInterface $userRepository, RoleRepositoryInterface $roleRepository, UniversityRepositoryInterface $universityRepository, MajorRepositoryInterface $majorRepository, UserTypeRepositoryInterface $userTypeRepository)
    {
        parent::__construct();

        $this->employeeRepository = $employeeRepository;
        $this->positionRepository = $positionRepository;
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->universityRepository = $universityRepository;
        $this->majorRepository = $majorRepository;
        $this->userTypeRepository = $userTypeRepository;
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

            default:
                $role = false; # which mean he/she doesn't have any crm role

        }

        return $role;
    }

    public function getDepartment($role)
    {
        switch ($role) {

            case "client":
                $dept_id = 1; # client management
                break;

            case "bizdev":
                $dept_id = 2; # business development
                break;

            case "finance":
                $dept_id = 3; # finance & operation
                break;

            case "hr":
                $dept_id = 5; # HR
                break;

            default:
                $dept_id = null; #
        }

        return $dept_id;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        DB::beginTransaction();
        try {
            
            $employees = $this->employeeRepository->getAllEmployees();
            $progressBar = $this->output->createProgressBar($employees->count());
            $progressBar->start();
            foreach ($employees as $employee) {

                if ($employee->empl_id !== 'EMPL-0024')
                    continue;

                $position = $this->createPositionIfNotExists($employee);

                $selectedUser = $this->createUserIfNotExists($employee, $position);
                
                $this->attachUserRoleOrDepartmentIfNotExists($employee, $selectedUser);
                
                $this->attachUserEducationIfNotExists($employee, $selectedUser);

                $progressBar->advance();
            }
            
        $progressBar->finish();
        DB::commit();

        } catch (Exception $e) {
            
            DB::rollBack();
            Log::warning('Import employees failed : ' . $e->getMessage(). ' | Line '. $e->getLine());
            
        }

        return Command::SUCCESS;
    }

    private function createPositionIfNotExists($employee)
    {
        # validate
        # if employee position doesn't exist in the database v2
        # then create a new one
        if (!$position = $this->positionRepository->getPositionByName($employee->empl_department)) 
                    {
            $positionDetails = [
                'position_name' => $employee->empl_department,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];

            $position = $this->positionRepository->createPosition($positionDetails);
            
        }

        return $position;
    }

    private function createUserIfNotExists($employee, $position)
    {
        $userDetails = [
            'nip' => null,
            'extended_id' => $employee->empl_id,
            'first_name' => $employee->empl_firstname,
            'last_name' => $employee->empl_lastname == '' ? null : $employee->empl_lastname,
            'address' => $employee->empl_address == '' ? null : $employee->empl_address,
            'email' => $employee->empl_email == '' ? null : $employee->empl_email,
            'phone' => $employee->empl_phone == '' ? null : $this->setPhoneNumber($employee->empl_phone),
            'emergency_contact' => $employee->empl_emergency_contact == '' || $employee->empl_emergency_contact == "-" ? null : $employee->empl_emergency_contact,
            'datebirth' => $employee->empl_datebirth == '0000-00-00' ? null : $employee->empl_datebirth,
            'position_id' => $position->id,
            'password' => $employee->empl_password == '' ? null : $employee->empl_password,
            'hiredate' => $employee->empl_hiredate,
            'nik' => $employee->empl_nik == '' ? null : $employee->empl_nik,
            'idcard' => $employee->empl_idcard == '' ? null : $employee->empl_idcard,
            'cv' => $employee->empl_cv == '' ? null : $employee->empl_cv,
            'bankname' => $employee->empl_bankaccountname == '' ? null : $employee->empl_bankaccountname,
            'bankacc' => $employee->empl_bankaccount == '' ? null : $employee->empl_bankaccount,
            'npwp' => $employee->empl_npwp == '' ? null : $employee->empl_npwp,
            'tax' => $employee->empl_tax == '' ? null : $employee->empl_tax,
            'active' => $employee->empl_isactive === 1 ? true : false,
            'health_insurance' => $employee->empl_healthinsurance == '' ? null : $employee->empl_healthinsurance,
            'empl_insurance' => $employee->empl_emplinsurance == '' ? null : $employee->empl_emplinsurance,
            'export' => $employee->empl_export,
            'notes' => null,
            'remember_token' => null,
            'created_at' => $employee->empl_lastupdatedate,
            'updated_at' => $employee->empl_lastupdatedates,
        ];

        # if user doesn not exists in the database v2
        if (!$createdUser = $this->userRepository->getUserByExtendedId($employee->empl_id)) 
        {
            # insert into tbl user
            $createdUser = $this->userRepository->createUser($userDetails);
        } else {

            # update position
            if ($createdUser->position_id == NULL) {

                $userDetails = [
                    'position_id' => $position->id
                ];

                # update position
                $this->userRepository->updateUser($createdUser->id, $userDetails);

            }

        }

        return $createdUser;
    }

    private function attachUserRoleOrDepartmentIfNotExists($employee, $selectedUser)
    {
        $userRoleDetails = array();

        # if imported user doesn't have employee role
        if (!$selectedUser->roles()->where('role_name', 'Employee')->first())
        {
            $roleDetail = $this->roleRepository->getRoleByName('employee');

            $userRoleDetails[] = [
                'user_id' => $selectedUser->id,
                'role_id' => $roleDetail->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
        }

        //! delete soon 
        # role is equal to department
        if ($role = $this->getRole($employee->empl_role)) {
            
            # if imported user doesn't have role from v1
            if (!$selectedUser->roles()->where('role_name', $role)->first())
            {
                $roleDetail = $this->roleRepository->getRoleByName($role);
                
                $userRoleDetails[] = [
                    'user_id' => $selectedUser->id,
                    'role_id' => $roleDetail->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
            }

            # put user into department
            $departmentId = $this->getDepartment($role);
            if (!$selectedUser->user_type()->wherePivot('department_id', $departmentId)->first())
            {   
                $userTypev1 = $employee->empl_status;
                $userTypev2 = $this->userTypeRepository->getUserTypeByTypeName($userTypev1);
                $userTypev2Id = $userTypev2->id;
                $userTypev2Name = $userTypev2->type_name;

                // $userTypeId = 1; # hardcode user type fulltime
                $hireDate = $this->getValueWithoutSpace($employee->empl_hiredate);
                $endDate = $this->getValueWithoutSpace($employee->empl_statusenddate);
                $statusActive = $employee->empl_isactive === 1 ? 'active' : 'inactive';
                $status = $statusActive === 'active' ? true : false;

                # kalau statusnya active dan end datenya ada isinya
                # maka ubah end datenya menjadi null
                # asumsikan bahwa client tsb masih aktif

                $loop = 1;
                if ($statusActive === 'active' && $userTypev2Name != 'Full-Time' && $endDate !== NULL) {
                    
                    # create a non full-time record
                    $loop = 2;
                    
                }
                
                for ($typeRecord = 0; $typeRecord < $loop ; $typeRecord++) {

                    if ($loop === 2) 
                        $status = false;

                    # kalau sebelumnya bukan full time
                    # maka insert record selama probation dan tambahkan record full-time
                    if ($typeRecord === 1 && $loop === 2) {

                        $userTypev2Id = 1; # which means full-time
                        $hireDate = $endDate;
                        $endDate = NULL;
                        $status = true;

                    }

                    $typeDetail = [
                        'department_id' => $departmentId,
                        'start_date' => $hireDate,
                        'end_date' => $endDate,
                        'status' => $status
                    ];
    
                    if (!$selectedUser->user_type()->wherePivot('user_type_id', $userTypev2Id)->first())
                        $selectedUser->user_type()->attach($userTypev2Id, $typeDetail);
                    else
                        $selectedUser->user_type()->updateExistingPivot($userTypev2Id, $typeDetail);

                }
                
                        

                
                
                
            }
            

        }
    
        # insert into tbl user roles
        # end of process insert into user roles
        $selectedUser->roles()->attach($userRoleDetails);
    }
    
    private function attachUserEducationIfNotExists($employee, $selectedUser)
    {
        # initialize new details by education 'bachelor'
        $graduatedFrom = ltrim($employee->empl_graduatefr);
        $graduatedMajor = ltrim($employee->empl_major);

        $userMajorDetails = array();

        if ( ($graduatedFrom != '') && ($graduatedFrom != null) )
        {
            
            $graduatedFrom = $this->checkUniversityName($graduatedFrom);
            
            if (!$univDetail = $this->universityRepository->getUniversityByName($graduatedFrom)) {

                $last_id = University::max('univ_id');
                $univ_id_without_label = $this->remove_primarykey_label($last_id, 5);
                $univ_id_with_label = 'UNIV-' . $this->add_digit((int)$univ_id_without_label+1, 3);

                $univDetails = [
                    'univ_id' => $univ_id_with_label,
                    'univ_name' => $graduatedFrom,
                    'univ_address' => null,
                    'univ_country' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];

                $univDetail = $this->universityRepository->createUniversity($univDetails);

            }

            # if she/he (employee) has more than one major on table employee v1
            # then do this
            if ( count($multiMajor = explode(' ; ', $graduatedMajor)) > 0 ) {
                
                for ($i = 0 ; $i < count($multiMajor) ; $i++) {
                    
                    if ($majorDetail = $this->majorRepository->getMajorByName($multiMajor[$i])) {
                        
                        $userMajorDetails[] = [
                            'user_id' => $selectedUser->id,
                            'univ_id' => $univDetail->univ_id,
                            'major_id' => $majorDetail->id,
                            'degree' => 'Bachelor',
                            'graduation_date' => null,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ];

                    } 

                    # if multiMajor[$i] doesn't exist in database
                    # then create a new one
                    
                    else {
                        
                        $majorDetail = [
                            'name' => $multiMajor[$i],
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ];

                        $createdMajor = $this->majorRepository->createMajors($majorDetail);

                        $userMajorDetails[] = [
                            'user_id' => $selectedUser->id,
                            'univ_id' => $univDetail->univ_id,
                            'major_id' => $createdMajor->id,
                            'degree' => 'Bachelor',
                            'graduation_date' => null,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ];

                    }

                }
            }

            # if she/he has only one major on table employee v1

            else {
                
                if ($majorDetail = $this->majorRepository->getMajorByName($graduatedMajor)) {

                    $userMajorDetails[] = [
                        'user_id' => $selectedUser->id,
                        'univ_id' => $univDetail->univ_id,
                        'major_id' => $majorDetail->id,
                        'degree' => 'Bachelor',
                        'graduation_date' => null,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ];
                }

                # if multiMajor[$i] doesn't exist in database
                # then create a new one
                
                else {
                    
                    $majorDetail = [
                        'name' => $graduatedMajor,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];

                    $createdMajor = $this->majorRepository->createMajors($majorDetail);

                    $userMajorDetails[] = [
                        'user_id' => $selectedUser->id,
                        'univ_id' => $univDetail->univ_id,
                        'major_id' => $createdMajor->id,
                        'degree' => 'Bachelor',
                        'graduation_date' => null,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ];

                }
                

            }
        }

        # initialize new details by education 'magister'
        $graduatedMagisterFrom = ltrim($employee->empl_graduatefr_magister);
        $graduatedMagisterMajor = ltrim($employee->empl_major_magister);
        
        if ( ($graduatedMagisterMajor != "") || ($graduatedMagisterMajor != null) )
        {
            # validate university
            # if $graduatedMagisterFrom doesn't exist in database
            # then create a new one

            $graduatedMagisterFrom = $this->checkUniversityName($graduatedMagisterFrom);

            if (!$univMagisterDetail = $this->universityRepository->getUniversityByName($graduatedMagisterFrom)) 
            {

                $last_id = University::max('univ_id');
                $univ_id_without_label = $this->remove_primarykey_label($last_id, 5);
                $univ_id_with_label = 'UNIV-' . $this->add_digit((int)$univ_id_without_label+1, 3);

                $univDetails = [
                    'univ_id' => $univ_id_with_label,
                    'univ_name' => $graduatedMagisterFrom,
                    'univ_address' => null,
                    'univ_country' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];

                $univMagisterDetail = $this->universityRepository->createUniversity($univDetails);
        
            }

            # validate major magister
            # if $graduatedMagisterMajor doesn't exist in database
            # then create a new one
            
            if (!$majorMagisterDetail = $this->majorRepository->getMajorByName($graduatedMagisterMajor))
            {
                $majorDetails = [
                    'name' => $graduatedMagisterMajor,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];

                $majorMagisterDetail = $this->majorRepository->createMajor($majorDetails);
                
            }

            $userMajorDetails[] = [
                'user_id' => $selectedUser->id,
                'univ_id' => $univMagisterDetail->univ_id,
                'major_id' => $majorMagisterDetail->id,
                'degree' => 'Magister',
                'graduation_date' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];

        }

        # insert into tbl_user_educations
        if (isset($userMajorDetails) || count($userMajorDetails) > 0)
            $selectedUser->educations()->sync($userMajorDetails);
    }

    public function checkUniversityName($univName)
    {
        switch($univName) {

            case "Pennsylvania University":
                return "Pennsylvania State University";
                break;

            default:
                return $univName;

        }
    }

    private function getValueWithoutSpace($value)
    {
        return $value == "" || $value == "-" || $value == "0000-00-00" || $value == 'N/A' ? NULL : $value;
    }
}
