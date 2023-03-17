<?php

namespace App\Console\Commands;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\CurriculumRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Models\School;
use App\Models\v1\School as CRMSchool;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportSchoolCurriculum extends Command
{
    use CreateCustomPrimaryKeyTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:school_curriculum';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import school curriculum from tbl_sch big data v1';

    protected CurriculumRepositoryInterface $curriculumRepository;
    protected SchoolRepositoryInterface $schoolRepository;

    public function __construct(CurriculumRepositoryInterface $curriculumRepository, SchoolRepositoryInterface $schoolRepository)
    {
        parent::__construct();

        $this->curriculumRepository = $curriculumRepository;
        $this->schoolRepository = $schoolRepository;
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

            $schools = CRMSchool::where('sch_id', '!=', '')->where('sch_name', '!=', '')->where('sch_name', '!=', '-')->select([
                'sch_id',
                'sch_name',
                DB::raw('(CASE 
                    WHEN sch_type = "-" OR sch_type = "" THEN NULL ELSE sch_type
                END) as sch_type'),
                DB::raw('(CASE 
                    WHEN sch_level = "" THEN NULL ELSE sch_level
                END) as sch_level'),
                DB::raw('(CASE 
                    WHEN sch_curriculum = "" THEN NULL ELSE sch_curriculum
                END) as sch_curriculum'),
                DB::raw('(CASE 
                    WHEN sch_mail = "" THEN NULL ELSE sch_mail
                END) as sch_mail'),
                DB::raw('(CASE 
                    WHEN sch_phone = "" THEN NULL ELSE sch_phone
                END) as sch_phone'),
                DB::raw('(CASE 
                    WHEN sch_insta = "" THEN NULL ELSE sch_insta
                END) as sch_insta'),
                DB::raw('(CASE 
                    WHEN sch_city = "" THEN NULL ELSE sch_city
                END) as sch_city'),
                DB::raw('(CASE 
                    WHEN sch_location = "" THEN NULL ELSE sch_location
                END) as sch_location'),
                DB::raw('(CASE 
                    WHEN sch_lastupdate = "0000-00-00 00:00:00" THEN NULL ELSE sch_lastupdate
                END) as sch_lastupdate')
    
            ])->get();
            $progressBar = $this->output->createProgressBar($schools->count());
            $progressBar->start();
            foreach ($schools as $crm_school) {
                $crm_sch_name = $crm_school->sch_name;
    
                # check if the school exists on database v2
                if (!$school = $this->schoolRepository->getSchoolByName($crm_sch_name))
                {
    
                    # initialize school id
                    $last_id = School::max('sch_id');
                    $school_id_without_label = $this->remove_primarykey_label($last_id, 4);
                    $school_id_with_label = 'SCH-' . $this->add_digit((int)$school_id_without_label + 1, 4);
    
                    $schoolDetails = [
                        'sch_id' => $school_id_with_label,
                        'sch_name' => $crm_school->sch_name,
                        'sch_type' => $crm_school->sch_type,
                        'sch_mail' => $crm_school->sch_mail,
                        'sch_phone' => $crm_school->sch_phone,
                        'sch_insta' => $crm_school->sch_insta,
                        'sch_city' => $crm_school->sch_city,
                        'sch_location' => $crm_school->sch_location,
                        'sch_score' => 0, # for now, the default value is 0
                    ];
    
                    $school = $this->schoolRepository->createSchool($schoolDetails);
                }
    
                $school_v2_id = $school->sch_id;
                $curriculum = $crm_school->sch_curriculum;
    
                if ($curriculum != "")
                {
                    $separate_curriculum = explode(',', $curriculum);
                    foreach ($separate_curriculum as $key => $value)
                    {
                        $each_curriculum = $separate_curriculum[$key];
        
                        # check if the curriculum exists on database v2
                        if (!$curriculum_v2 = $this->curriculumRepository->getCurriculumByName($each_curriculum))
                        {
                            $curriculumDetails = [
                                'name' => $each_curriculum
                            ];
        
                            $curriculum_v2 = $this->curriculumRepository->createOneCurriculum($curriculumDetails);
                        }
        
                        $curriculum_v2_id = $curriculum_v2->id;
        
                        $schoolCurriculumDetails = [
                            'curriculum_id' => $curriculum_v2_id,
                        ];
    
                        # attach school with curriculum
                        $this->schoolRepository->attachCurriculum($school_v2_id, $schoolCurriculumDetails);
                        
                    }
                }
    
                $progressBar->advance();
            }
    
            $progressBar->finish();
            DB::commit();
            Log::info('Import school curriculum works fine');

        } catch (Exception $e) {

            DB::rollBack();
            Log::warning('Failed to import school curriculum : '.$e->getMessage().' | '.$e->getLine());

        }


        return Command::SUCCESS;
    }
}
