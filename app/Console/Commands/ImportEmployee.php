<?php

namespace App\Console\Commands;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\PositionRepositoryInterface;
use App\Interfaces\EmployeeRepositoryInterface;
use App\Interfaces\MajorRepositoryInterface;
use App\Interfaces\RoleRepositoryInterface;
use App\Interfaces\UniversityRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
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

    public function __construct(EmployeeRepositoryInterface $employeeRepository, PositionRepositoryInterface $positionRepository, UserRepositoryInterface $userRepository, RoleRepositoryInterface $roleRepository, UniversityRepositoryInterface $universityRepository, MajorRepositoryInterface $majorRepository)
    {
        parent::__construct();

        $this->employeeRepository = $employeeRepository;
        $this->positionRepository = $positionRepository;
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->universityRepository = $universityRepository;
        $this->majorRepository = $majorRepository;
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

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $employees = $this->employeeRepository->getAllEmployees();

        DB::beginTransaction();

        try {
        
            foreach ($employees as $employee) {

                # validate if the data that about to inserted is not exist in the table
                if (!$updatedUser = $this->userRepository->getUserByExtendedId($employee->empl_id)) {
                //     echo json_encode($test);exit;
                    # validate
                    # if $employee->empl_department doesn't exist in database
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

                    $userDetails = [
                        'nip' => null,
                        'extended_id' => $employee->empl_id,
                        'first_name' => $employee->empl_firstname,
                        'last_name' => $employee->empl_lastname == '' ? null : $employee->empl_lastname,
                        'address' => $employee->empl_address == '' ? null : $employee->empl_address,
                        'email' => $employee->empl_email == '' ? null : $employee->empl_email,
                        'phone' => $employee->empl_phone == '' ? null : $employee->empl_phone,
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
                        'active' => true,
                        'health_insurance' => $employee->empl_healthinsurance == '' ? null : $employee->empl_healthinsurance,
                        'empl_insurance' => $employee->empl_emplinsurance == '' ? null : $employee->empl_emplinsurance,
                        'export' => $employee->empl_export,
                        'notes' => null,
                        'remember_token' => null,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];

                    # insert into tbl user
                    $createdUser = $this->userRepository->createUser($userDetails);
                    
                    $userRoleDetails = array();

                    # initialize new details by role
                    for ($i = 0 ; $i < 2 ; $i++) {

                        # the first role to be assigned is employee 
                        if ($i == 0 ) {

                            $roleDetail = $this->roleRepository->getRoleByName('employee');

                            $userRoleDetails[] = [
                                'user_id' => $createdUser->id,
                                'role_id' => $roleDetail->id,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ];

                        }
                        # the second role to be assigned is depends on employee role from big data v1
                        else {

                            if ($role = $this->getRole($employee->empl_role)) {
                                
                                $roleDetail = $this->roleRepository->getRoleByName($role);
                                
                                $userRoleDetails[] = [
                                    'user_id' => $createdUser->id,
                                    'role_id' => $roleDetail->id,
                                    'created_at' => Carbon::now(),
                                    'updated_at' => Carbon::now()
                                ];

                            }

                        }

                    }

                    # insert into tbl user roles
                    # end of process insert into user roles
                    $createdUser->roles()->attach($userRoleDetails);
                    
                    # initialize new details by education 'bachelor'
                    $graduatedFrom = ltrim($employee->empl_graduatefr);
                    $graduatedMajor = ltrim($employee->empl_major);

                    $userMajorDetails = array();

                    if ( ($graduatedFrom != '') && ($graduatedFrom != null) )
                    {
                        
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
                                        'user_id' => $createdUser->id,
                                        'univ_id' => $univDetail->id,
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
                                        'user_id' => $createdUser->id,
                                        'univ_id' => $univDetail->id,
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
                                    'user_id' => $createdUser->id,
                                    'univ_id' => $univDetail->id,
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
                                    'user_id' => $createdUser->id,
                                    'univ_id' => $univDetail->id,
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
                    
                    if ( ($graduatedMagisterMajor != "") && ($graduatedMagisterMajor != null) )
                    {
                        # validate university
                        # if $graduatedMagisterFrom doesn't exist in database
                        # then create a new one

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
                            'user_id' => $createdUser->id,
                            'univ_id' => $univMagisterDetail->id,
                            'major_id' => $majorMagisterDetail->id,
                            'degree' => 'Magister',
                            'graduation_date' => null,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ];

                    }

                    # insert into tbl_user_educations
                    if (isset($userMajorDetails))
                        $createdUser->educations()->attach($userMajorDetails);
                } else {
                    
                    if (!$position = $this->positionRepository->getPositionByName($employee->empl_department)) 
                    {
                        $positionDetails = [
                            'position_name' => $employee->empl_department,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ];

                        $position = $this->positionRepository->createPosition($positionDetails);
                        
                    }
                    

                    # checking position
                    # if its NULL 
                    # then insert a position
                    if ($createdUser = User::where('extended_id', $employee->empl_id)->where('position_id', NULL)->first()) {
                        
                        $userDetails = [
                            'position_id' => $position->id
                        ];
    
                        # update position
                        $this->userRepository->updateUser($createdUser->id, $userDetails);

                    } else {

                        # if there are user on the database
                        # then put it into variable createdUser
                        $createdUser = User::where('extended_id', $employee->empl_id)->first();

                    }

                    # initialize new details by education 'bachelor'
                    $graduatedFrom = ltrim($employee->empl_graduatefr);
                    $graduatedMajor = ltrim($employee->empl_major);

                    $userMajorDetails = array();

                    if ( ($graduatedFrom != '') && ($graduatedFrom != null) )
                    {
                        
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
                                        'user_id' => $createdUser->id,
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
                                        'user_id' => $createdUser->id,
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
                                    'user_id' => $createdUser->id,
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
                                    'user_id' => $createdUser->id,
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
                    
                    if ( ($graduatedMagisterMajor != "") && ($graduatedMagisterMajor != null) )
                    {
                        # validate university
                        # if $graduatedMagisterFrom doesn't exist in database
                        # then create a new one

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
                            'user_id' => $createdUser->id,
                            'univ_id' => $univMagisterDetail->univ_id,
                            'major_id' => $majorMagisterDetail->id,
                            'degree' => 'Magister',
                            'graduation_date' => null,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ];

                    }
                    
                    # insert into tbl_user_educations
                    if (isset($userMajorDetails)) {

                        // if (!$createdUser->educations()->where('tbl_user_educations.univ_id', $univMagisterDetail->univ_id)->where('tbl_user_educations.major_id', $majorMagisterDetail->id)->first())
                            $createdUser->educations()->attach($userMajorDetails);
                    }

                }
            }
            
        DB::commit();

        } catch (Exception $e) {
            
            DB::rollBack();
            Log::error('Import employees failed : ' . $e->getMessage());
            echo $e->getMessage();
            
        }

        return Command::SUCCESS;
    }
}
