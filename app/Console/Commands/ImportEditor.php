<?php

namespace App\Console\Commands;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\CountryRepositoryInterface;
use App\Interfaces\PositionRepositoryInterface;
use App\Interfaces\EditorRepositoryInterface;
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

class ImportEditor extends Command
{
    use CreateCustomPrimaryKeyTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:editor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import editor from big data v1 and merge into employee in big data v2';

    protected PositionRepositoryInterface $positionRepository;
    protected UserRepositoryInterface $userRepository;
    protected RoleRepositoryInterface $roleRepository;
    protected UniversityRepositoryInterface $universityRepository;
    protected MajorRepositoryInterface $majorRepository;
    protected CountryRepositoryInterface $countryRepository;
    protected EditorRepositoryInterface $editorRepository;

    public function __construct(EditorRepositoryInterface $editorRepository, PositionRepositoryInterface $positionRepository, UserRepositoryInterface $userRepository, RoleRepositoryInterface $roleRepository, UniversityRepositoryInterface $universityRepository, MajorRepositoryInterface $majorRepository, CountryRepositoryInterface $countryRepository)
    {
        parent::__construct();

        $this->editorRepository = $editorRepository;
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
        $editors = $this->editorRepository->getAllEditors();

        DB::beginTransaction();

        try {

            foreach ($editors as $editor) 
            {

                $extended_id = $editor->editor_id;
                $first_name = $editor->editor_fn;
                $last_name = $editor->editor_ln;
                $fullname = $first_name.' '.$last_name;
                $email = $editor->editor_mail;

                # if user doesnt exist in the database
                # then create a new one

                if (!$user = $this->userRepository->getUserByFullNameOrEmail($fullname, $email)) {

                    # initialized user detail
                    $userDetails = [
                        'nip' => null,
                        'extended_id' => $extended_id,
                        'first_name' => $editor->editor_fn,
                        'last_name' => $editor->editor_ln == '' ? null : $editor->editor_ln,
                        'address' => $editor->editor_address == '' ? null : $editor->editor_address,
                        'email' => $editor->editor_mail == '' ? null : $editor->editor_mail,
                        'phone' => $editor->editor_phone == '' || $editor->editor_phone == '-' ? null : $editor->editor_phone,
                        'emergency_contact' => null,
                        'datebirth' => null,
                        'position_id' => null,
                        'password' => $editor->editor_passw == '' ? null : $editor->editor_passw,
                        'hiredate' => null,
                        'nik' => null,
                        'idcard' => null,
                        'cv' => $editor->editor_cv == '' ? null : $editor->editor_cv,
                        'bankname' => $editor->editor_bankname == '' ? null : $editor->editor_bankname,
                        'bankacc' => $editor->editor_bankacc == '' || $editor->editor_bankacc == "0" ? null : $editor->editor_bankacc,
                        'npwp' => $editor->editor_npwp == '' ? null : $editor->editor_npwp,
                        'tax' => null,
                        'active' => $editor->editor_status == 1 ? true : false,
                        'health_insurance' => null,
                        'empl_insurance' => null,
                        'export' => false,
                        'notes' => $editor->editor_notes,
                        'remember_token' => null,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];

                    $user = $this->userRepository->createUser($userDetails);

                    # initialize new details by education 'bachelor'
                    $graduatedFrom = $editor->univ_id;
                    $graduatedMajor = ltrim($editor->editor_major);

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
                            $countryDetail = $this->countryRepository->getCountryNameByUnivCountry($editor->university->univ_country);

                            $newUnivDetails = [
                                'univ_id' => $univ_id_with_label,
                                'univ_name' => $editor->university->univ_name,
                                'univ_address' => $editor->university->univ_address,
                                'univ_country' => $countryDetail->name,
                            ]; 

                            # insert new university
                            $univDetail = $this->universityRepository->createUniversity($newUnivDetails);

                        }
                            
                        # if she/he has only one major on table employee v1
                        if ($majorDetail = $this->majorRepository->getMajorByName($graduatedMajor)) {

                            $userMajorDetails[] = [
                                'user_id' => $user->id,
                                'univ_id' => $univDetail->id,
                                'major_id' => $majorDetail->id,
                                'degree' => 'Bachelor',
                                'graduation_date' => null,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ];
                        }

                        # if editor major doesn't exist in database
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

                # insert into tbl_user_educations
                if (isset($userMajorDetails))
                $user->educations()->attach($userMajorDetails);

                $userRoleDetails = array();

                # check whether editor is associate, senior, or managing
                switch ($editor->editor_position) {
        
                    case 1: # associate

                        # check if user already has role associate editor
                        if (!$this->userRepository->getUserRoles($user->id, 'associate editor')) {

                            # if user doesn't have role associate editor
                            # then add it 

                            $roleDetail = $this->roleRepository->getRoleByName("associate editor");
            
                            $userRoleDetails[] = [
                                'user_id' => $user->id,
                                'role_id' => $roleDetail->id,
                                'extended_id' => $extended_id,
                                'tutor_subject' => null,
                                'feehours' => $editor->editor_phours == "0" ? null : $editor->editor_phours,
                                'feesession' => null,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ];
                        }
                        break;

                    case 2: # senior

                        # check if user already has role senior editor
                        if (!$this->userRepository->getUserRoles($user->id, 'senior editor')) {

                            # if user doesn't have role senior editor
                            # then add it 

                            $roleDetail = $this->roleRepository->getRoleByName("senior editor");
            
                            $userRoleDetails[] = [
                                'user_id' => $user->id,
                                'role_id' => $roleDetail->id,
                                'extended_id' => $extended_id,
                                'tutor_subject' => null,
                                'feehours' => $editor->editor_phours == "0" ? null : $editor->editor_phours,
                                'feesession' => null,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ];
                        }

                    case 3: # managing

                        # check if user already has role managing editor
                        if (!$this->userRepository->getUserRoles($user->id, 'managing editor')) {

                            # if user doesn't have role managing editor
                            # then add it 

                            $roleDetail = $this->roleRepository->getRoleByName("managing editor");
            
                            $userRoleDetails[] = [
                                'user_id' => $user->id,
                                'role_id' => $roleDetail->id,
                                'extended_id' => $extended_id,
                                'tutor_subject' => null,
                                'feehours' => $editor->editor_phours == "0" ? null : $editor->editor_phours,
                                'feesession' => null,
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
            Log::error('Import editors failed : ' . $e->getMessage());
            echo $e->getMessage();

        }
        
        return Command::SUCCESS;
    }
}
