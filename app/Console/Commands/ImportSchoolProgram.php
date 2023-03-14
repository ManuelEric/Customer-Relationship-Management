<?php

namespace App\Console\Commands;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\SchoolProgramRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ImportSchoolProgram extends Command
{
    use CreateCustomPrimaryKeyTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:school_program';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import sch prog data from big data v1 into big data v2';

    protected SchoolProgramRepositoryInterface $schoolProgramRepository;
    protected SchoolRepositoryInterface $schoolRepository;
    protected UserRepositoryInterface $userRepository;

    public function __construct(SchoolProgramRepositoryInterface $schoolProgramRepository, SchoolRepositoryInterface $schoolRepository, UserRepositoryInterface $userRepository)
    {
        parent::__construct();

        $this->schoolProgramRepository = $schoolProgramRepository;
        $this->schoolRepository = $schoolRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $schoolPrograms = $this->schoolProgramRepository->getAllSchoolProgramFromCRM();
        $schoolProgramDetails = [];

        foreach ($schoolPrograms as $schoolProgram) {
            $empl = isset($schoolProgram->empl_id) ? $this->userRepository->getUserByExtendedId($schoolProgram->empl_id) : null;

            if (!$this->schoolProgramRepository->getSchoolProgramById($schoolProgram->schprog_id)) {
                if ($schoolProgram->schprog_status == 1 && $schoolProgram->schoolProgFix != null) {
                    switch ($schoolProgram->schoolProgFix->schprogfix_status) {
                        case 0:
                            $runing_status = 'Not Yet';
                            break;

                        case 1:
                            $runing_status = 'On going';
                            break;

                        case 2:
                            $runing_status = 'Done';
                            break;
                    }

                    $schoolProgramDetails[] = [
                        'id' => $schoolProgram->schprog_id,
                        'sch_id' => $schoolProgram->sch_id,
                        'prog_id' => $schoolProgram->prog_id,
                        'first_discuss' => $schoolProgram->schprog_datefirstdis,
                        'status' => $schoolProgram->schprog_status,
                        'notes' => $schoolProgram->schprog_notes == '' ? null : $schoolProgram->schprog_notes,
                        'notes_detail' => $schoolProgram->schoolProgFix->schprogfix_notes == '' ? null : $schoolProgram->schoolProgFix->schprogfix_notes,
                        'refund_notes' => null,
                        'refund_date' => null,
                        'running_status' => $runing_status,
                        'total_hours' => $schoolProgram->schoolProgFix->schprogfix_totalhours,
                        'total_fee' => null,
                        'participants' => $schoolProgram->schoolProgFix->schprogfix_participantsnum,
                        'place' => $schoolProgram->schoolProgFix->schprogfix_eventplace,
                        'end_program_date' => $schoolProgram->schoolProgFix->schprogfix_eventenddate,
                        'start_program_date' => $schoolProgram->schoolProgFix->schprogfix_eventstartdate,
                        'success_date' => $schoolProgram->schprog_datelastdis,
                        'reason_id' => null,
                        'denied_date' => null,
                        'empl_id' => isset($empl) ? $empl->id : null,
                        'created_at' => $schoolProgram->schprog_datefirstdis,
                        'updated_at' => $schoolProgram->schprog_datefirstdis
                    ];
                } else {
                    $schoolProgramDetails[] = [
                        'id' => $schoolProgram->schprog_id,
                        'sch_id' => $schoolProgram->sch_id,
                        'prog_id' => $schoolProgram->prog_id,
                        'first_discuss' => $schoolProgram->schprog_datefirstdis,
                        'status' => $schoolProgram->schprog_status,
                        'notes' => $schoolProgram->schprog_notes == '' ? null : $schoolProgram->schprog_notes,
                        'notes_detail' => null,
                        'refund_notes' => null,
                        'refund_date' => null,
                        'running_status' => null,
                        'total_hours' => null,
                        'total_fee' => null,
                        'participants' => null,
                        'place' => null,
                        'end_program_date' => null,
                        'start_program_date' => null,
                        'success_date' => null,
                        'reason_id' => null,
                        'denied_date' => $schoolProgram->schprog_status == 2 ? $schoolProgram->schprog_datelastdis : null,
                        'empl_id' => isset($empl) ? $empl->id : null,
                        'created_at' => $schoolProgram->schprog_datefirstdis,
                        'updated_at' => $schoolProgram->schprog_datefirstdis
                    ];
                }
            }
        }

        if (count($schoolProgramDetails) > 0) {
            $this->schoolProgramRepository->createSchoolPrograms($schoolProgramDetails);
        }
        return Command::SUCCESS;
    }
}
