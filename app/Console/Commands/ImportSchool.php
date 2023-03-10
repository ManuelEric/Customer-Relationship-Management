<?php

namespace App\Console\Commands;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\SchoolRepositoryInterface;
use App\Models\School;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ImportSchool extends Command
{
    use CreateCustomPrimaryKeyTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:school';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import school from big data v1 to big data v2';

    protected SchoolRepositoryInterface $schoolRepository;

    public function __construct(SchoolRepositoryInterface $schoolRepository)
    {
        parent::__construct();

        $this->schoolRepository = $schoolRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $schools = $this->schoolRepository->getAllSchoolFromV1();
        $new_schools = [];

        foreach ($schools as $school) {
            $schoolIdV2 = $this->schoolRepository->getSchoolById($school->sch_id);
            $schoolNameV2 = $this->schoolRepository->getschoolByName($school->sch_name);

            if (!$schoolIdV2 && !$schoolNameV2 && $school->sch_id != null && $school->sch_name != null && $school->sch_name != '' && $school->sch_name != ' ' && $school->sch_name != '-') {

                $new_schools[] = [
                    'sch_id' => $school->sch_id,
                    'sch_name' => $school->sch_name,
                    'sch_type' => $school->sch_type == '' || $school->sch_type == '-' ? null : $school->sch_type,
                    'sch_mail' => $school->sch_mail == '' || $school->sch_mail == '-' ? null : $school->sch_mail,
                    'sch_phone' => $school->sch_phone == '' || $school->sch_phone == '-' ? null : $school->sch_phone,
                    'sch_insta' => $school->sch_insta == '' || $school->sch_insta == '-' ? null : $school->sch_insta,
                    'sch_city' => $school->sch_city == '' || $school->sch_city == '-' ? null : $school->sch_city,
                    'sch_location' => $school->sch_location == '' || $school->sch_location == '-' ? null : $school->sch_location,
                    'sch_score' => 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }
        if (count($new_schools) > 0) {
            $this->schoolRepository->createSchools($new_schools);
        }

        return Command::SUCCESS;
    }
}
