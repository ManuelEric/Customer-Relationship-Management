<?php

namespace App\Console\Commands;

use App\Interfaces\SchoolDetailRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ImportSchoolDetail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:school_detail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import school detail data from big data v1 into big data v2';

    protected SchoolDetailRepositoryInterface $schoolDetailRepository;
    protected SchoolRepositoryInterface $schoolRepository;

    public function __construct(SchoolDetailRepositoryInterface $schoolDetailRepository, SchoolRepositoryInterface $schoolRepository)
    {
        parent::__construct();

        $this->schoolDetailRepository = $schoolDetailRepository;
        $this->schoolRepository = $schoolRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $schoolDetails = $this->schoolDetailRepository->getAllSchoolDetailFromCRM();
        $newSchoolDetails = [];
        $progressBar = $this->output->createProgressBar($schoolDetails->count());
        $progressBar->start();

        foreach ($schoolDetails as $schoolDetail) {
            $school = $this->schoolRepository->getSchoolById($schoolDetail->sch_id);
            if (!$this->schoolDetailRepository->getSchoolDetailById($schoolDetail->schdetail_id) && $school != null && $schoolDetail->schdetail_fullname != "" && $schoolDetail->schdetail_fullname !=  null) {

                $schdetail_phone = $this->getValueWithoutSpace($schoolDetail->schdetail_phone);
                if ($schdetail_phone != NULL)
                {
                    $schdetail_phone = str_replace('-', '', $schdetail_phone);
                    $schdetail_phone = str_replace(' ', '', $schdetail_phone);
                    $schdetail_phone = str_replace(array('(', ')'), '', $schdetail_phone);

                    switch (substr($schdetail_phone, 0, 1)) {

                        case 0:
                            $schdetail_phone = "+62".substr($schdetail_phone, 1);
                            break;

                        case 6:
                            $schdetail_phone = "+".$schdetail_phone;
                            break;

                    }
                }

                $newSchoolDetails[] = [
                    'schdetail_id' => $schoolDetail->schdetail_id,
                    'sch_id' => $schoolDetail->sch_id,
                    'schdetail_fullname' => $schoolDetail->schdetail_fullname,
                    'schdetail_email' => $schoolDetail->schdetail_email == '' || $schoolDetail->schdetail_email == '-' ? null : $schoolDetail->schdetail_email,
                    'schdetail_grade' => $schoolDetail->schdetail_grade,
                    'schdetail_position' => $schoolDetail->schdetail_position,
                    'schdetail_phone' => $schdetail_phone,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
            }

            $progressBar->advance();
        }

        if (count($newSchoolDetails) > 0) {
            $this->schoolDetailRepository->createSchoolDetail($newSchoolDetails);
        }

        $progressBar->finish();
        return Command::SUCCESS;
    }

    private function getValueWithoutSpace($value)
    {
        return $value == "" || $value == "-" || $value == "0000-00-00" || $value == 'N/A' ? NULL : $value;
    }
}
