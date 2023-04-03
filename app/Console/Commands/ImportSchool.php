<?php

namespace App\Console\Commands;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Interfaces\SchoolRepositoryInterface;
use App\Models\School;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ImportSchool extends Command
{
    use CreateCustomPrimaryKeyTrait;
    use StandardizePhoneNumberTrait;

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
        $progressBar = $this->output->createProgressBar($schools->count());
        $progressBar->start();

        foreach ($schools as $school) {
            $schoolIdV2 = $this->schoolRepository->getSchoolById($school->sch_id);
            $schoolNameV2 = $this->schoolRepository->getschoolByName($school->sch_name);

            if (!$schoolIdV2 && !$schoolNameV2 && $school->sch_id != null && $school->sch_name != null && $school->sch_name != '' && $school->sch_name != ' ' && $school->sch_name != '-') {

                $schoolPhoneV1 = $this->getValueWithoutSpace($school->sch_phone);
                if ($schoolPhoneV1 != NULL)
                {
                    $schoolPhoneV1 = str_replace('-', '', $schoolPhoneV1);
                    $schoolPhoneV1 = str_replace(' ', '', $schoolPhoneV1);
                    $schoolPhoneV1 = str_replace(array('(', ')'), '', $schoolPhoneV1);

                    switch (substr($schoolPhoneV1, 0, 1)) {

                        case 0:
                            $schoolPhoneV1 = "+62".substr($schoolPhoneV1, 1);
                            break;

                        case 6:
                            $schoolPhoneV1 = "+".$schoolPhoneV1;
                            break;

                    }
                }

                if ($school->sch_type == 'International')
                    $score = 6; # up market
                elseif ($school->sch_type == 'National')
                    $score = 3; # mid market
                else
                    $score = 0;

                $new_schools[] = [
                    'sch_id' => $school->sch_id,
                    'sch_name' => $school->sch_name,
                    'sch_type' => $school->sch_type == '' || $school->sch_type == '-' ? null : $school->sch_type,
                    'sch_mail' => $school->sch_mail == '' || $school->sch_mail == '-' ? null : $school->sch_mail,
                    'sch_phone' => $schoolPhoneV1,
                    'sch_insta' => $school->sch_insta == '' || $school->sch_insta == '-' ? null : $school->sch_insta,
                    'sch_city' => $school->sch_city == '' || $school->sch_city == '-' ? null : $school->sch_city,
                    'sch_location' => $school->sch_location == '' || $school->sch_location == '-' ? null : $school->sch_location,
                    'sch_score' => $score,
                    'created_at' => $this->getValueWithoutSpace($school->sch_lastupdate) ?? Carbon::now(), 
                    'updated_at' => Carbon::now(),
                ];
            }
            $progressBar->advance();
        }

        if (count($new_schools) > 0) {
            $this->schoolRepository->createSchools($new_schools);
        }

        $progressBar->finish();

        return Command::SUCCESS;
    }

    private function getValueWithoutSpace($value)
    {
        return $value == "" || $value == "-" || $value == "tidak ada" || $value == "no contact" || $value == "0000-00-00" || $value == "0000-00-00 00:00:00" || $value == 'N/A' ? NULL : $value;
    }
}
