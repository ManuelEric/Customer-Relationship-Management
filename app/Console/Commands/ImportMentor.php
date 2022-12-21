<?php

namespace App\Console\Commands;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\CountryRepositoryInterface;
use App\Interfaces\PositionRepositoryInterface;
use App\Interfaces\MentorRepositoryInterface;
use App\Interfaces\MajorRepositoryInterface;
use App\Interfaces\RoleRepositoryInterface;
use App\Interfaces\UniversityRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Models\University;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportMentor extends Command
{
    use CreateCustomPrimaryKeyTrait;
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

    protected MentorRepositoryInterface $mentorRepository;
    protected PositionRepositoryInterface $positionRepository;
    protected UserRepositoryInterface $userRepository;
    protected RoleRepositoryInterface $roleRepository;
    protected UniversityRepositoryInterface $universityRepository;
    protected MajorRepositoryInterface $majorRepository;
    protected CountryRepositoryInterface $countryRepository;

    public function __construct(MentorRepositoryInterface $mentorRepository, PositionRepositoryInterface $positionRepository, UserRepositoryInterface $userRepository, RoleRepositoryInterface $roleRepository, UniversityRepositoryInterface $universityRepository, MajorRepositoryInterface $majorRepository, CountryRepositoryInterface $countryRepository)
    {
        parent::__construct();

        $this->mentorRepository = $mentorRepository;
        $this->positionRepository = $positionRepository;
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->universityRepository = $universityRepository;
        $this->majorRepository = $majorRepository;
        $this->countryRepository = $countryRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $mentors = $this->mentorRepository->getAllMentors();
        
        DB::beginTransaction();

        try {

            foreach ($mentors as $mentor) 
            {

                $extended_id = $mentor->mt_id;
                $first_name = $mentor->mt_firstn;
                $last_name = $mentor->mt_lastn;
                $fullname = $first_name.' '.$last_name;
                $email = $mentor->mt_email;
    
                # if user doesnt exist in the database
                # then create a new one
                
                if (!$user = $this->userRepository->getUserByFullNameOrEmail($fullname, $email)) {

                    # initialized user detail
                    $userDetails = [
                        'nip' => null,
                        'extended_id' => $extended_id,
                        'first_name' => $mentor->mt_firstn,
                        'last_name' => $mentor->mt_lastn == '' ? null : $mentor->mt_lastn,
                        'address' => $mentor->mt_address == '' ? null : $mentor->mt_address,
                        'email' => $mentor->mt_email == '' || $mentor->mt_email == '-' ? null : $mentor->mt_email,
                        'phone' => $mentor->mt_phone == '' ? null : $mentor->mt_phone,
                        'emergency_contact' => null,
                        'datebirth' => null,
                        'position_id' => null,
                        'password' => $mentor->mt_password == '' ? null : $mentor->mt_password,
                        'hiredate' => null,
                        'nik' => null,
                        'idcard' => null,
                        'cv' => $mentor->mt_cv == '' ? null : $mentor->mt_cv,
                        'bankname' => $mentor->mt_banknm == '' ? null : $mentor->mt_banknm,
                        'bankacc' => $mentor->mt_bankacc == '' ? null : $mentor->mt_bankacc,
                        'npwp' => $mentor->mt_npwp == '' ? null : $mentor->mt_npwp,
                        'tax' => null,
                        'active' => $mentor->mt_status == 1 ? true : false,
                        'health_insurance' => null,
                        'empl_insurance' => null,
                        'export' => false,
                        'notes' => $mentor->mt_notes,
                        'remember_token' => null,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
    
                    $user = $this->userRepository->createUser($userDetails);

                    # initialize new details by education 'bachelor'
                    $graduatedFrom = $mentor->univ_id;
                    $graduatedMajor = $mentor->mt_major;

                    $userMajorDetails = array();

                    if ( ($graduatedFrom != '') && ($graduatedFrom != null) )
                    {
                        # check if university is exists in database university v2
                        if (!$univDetail = $this->universityRepository->getUniversityByUnivId($graduatedFrom)) {

                            # if not exist then create a new one
                            # initialize
                            $last_id = University::max('univ_id');
                            $univ_id_without_label = $this->remove_primarykey_label($last_id, 5);
                            $univ_id_with_label = 'UNIV-' . $this->add_digit($univ_id_without_label + 1, 3);

                            # get country name 
                            $countryDetail = $this->countryRepository->getCountryNameByUnivCountry($mentor->university->univ_country);

                            $newUnivDetails = [
                                'univ_id' => $univ_id_with_label,
                                'univ_name' => $mentor->university->univ_name,
                                'univ_address' => $mentor->university->univ_address,
                                'univ_country' => $countryDetail->name,
                            ]; 

                            # insert new university
                            $univDetail = $this->universityRepository->createUniversity($newUnivDetails);

                        }

                        # if she/he (employee) has more than one major on table employee v1
                        # then do this
                        if ( count($multiMajor = explode(' ; ', $graduatedMajor)) > 0 ) {
                            
                            for ($i = 0 ; $i < count($multiMajor) ; $i++) {
                                
                                # validate just in case if multiMajor[$i] is empty
                                if ($multiMajor[$i]) {

                                    if ($majorDetail = $this->majorRepository->getMajorByName($multiMajor[$i])) {
                                        
                                        $userMajorDetails[] = [
                                            'user_id' => $user->id,
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
    
                                        $createdMajor = $this->majorRepository->createMajor($majorDetail);
    
                                        $userMajorDetails[] = [
                                            'user_id' => $user->id,
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
                        }

                        # if she/he has only one major on table employee v1
                        else {
                            
                            if ($majorDetail = $this->majorRepository->getMajorByName($graduatedMajor)) {

                                $userMajorDetails[] = [
                                    'user_id' => $user->id,
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

                                $createdMajor = $this->majorRepository->createMajor($majorDetail);

                                $userMajorDetails[] = [
                                    'user_id' => $user->id,
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
                }

                # insert into tbl_user_educations
                if (isset($userMajorDetails))
                    $user->educations()->attach($userMajorDetails);

                $userRoleDetails = array();
    
                # check whether user is mentor, mentor & tutor, or just a tutor
                switch ($mentor->mt_istutor) {
    
                    case 1: # mentor
    
                        # check if user already has role mentor
                        if (!$this->userRepository->getUserRoles($user->id, 'mentor')) {
    
                            # if user doesn't have role mentor
                            # then add it 
    
                            $roleDetail = $this->roleRepository->getRoleByName("mentor");
            
                            $userRoleDetails[] = [
                                'user_id' => $user->id,
                                'role_id' => $roleDetail->id,
                                'extended_id' => $extended_id,
                                'tutor_subject' => null,
                                'feehours' => null,
                                'feesession' => null,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ];
                        }
                        break;
    
                    case 2: # mentor & tutor
    
                        # check if user already has role mentor
                        if (!$this->userRepository->getUserRoles($user->id, 'mentor')) {
    
                            # if user doesn't have role mentor
                            # then add it 
    
                            $roleDetail = $this->roleRepository->getRoleByName("mentor");
            
                            $userRoleDetails[] = [
                                'user_id' => $user->id,
                                'role_id' => $roleDetail->id,
                                'extended_id' => $extended_id,
                                'tutor_subject' => null,
                                'feehours' => null,
                                'feesession' => null,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ];
                        }
    
                        # check if user already has role tutor
                        if (!$this->userRepository->getUserRoles($user->id, 'tutor')) {
    
                            # if user doesn't have role tutor
                            # then add it 
    
                            $roleDetail = $this->roleRepository->getRoleByName("tutor");
            
                            $userRoleDetails[] = [
                                'user_id' => $user->id,
                                'role_id' => $roleDetail->id,
                                'extended_id' => $extended_id,
                                'tutor_subject' => $mentor->mt_tsubject == "" ? null : $mentor->mt_tsubject,
                                'feehours' => $mentor->mt_feehours,
                                'feesession' => $mentor->mt_feesession,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ];
    
                        }
                        break;
    
                    case 3: # tutor
    
                        # check if user already has role tutor
                        if (!$this->userRepository->getUserRoles($user->id, 'tutor')) {
    
                            # if user doesn't have role tutor
                            # then add it 
    
                            $roleDetail = $this->roleRepository->getRoleByName("tutor");
            
                            $userRoleDetails[] = [
                                'user_id' => $user->id,
                                'role_id' => $roleDetail->id,
                                'extended_id' => $extended_id,
                                'tutor_subject' => $mentor->mt_tsubject == "" ? null : $mentor->mt_tsubject,
                                'feehours' => $mentor->mt_feehours,
                                'feesession' => $mentor->mt_feesession,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ];
    
                        }
                        break;
    
                    }
    
                    $user->roles()->attach($userRoleDetails);
    
            }
            DB::commit();

        } catch (Exception $e) {
            
            DB::rollBack();
            Log::error('Import mentors failed : ' . $e->getMessage());
            echo $e->getMessage();

        }

        return Command::SUCCESS;
    }
}
