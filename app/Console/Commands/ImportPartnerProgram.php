<?php

namespace App\Console\Commands;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\PartnerProgramRepositoryInterface;
use App\Interfaces\CorporateRepositoryInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ImportPartnerProgram extends Command
{
    use CreateCustomPrimaryKeyTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:partner_program';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import corp prog data from big data v1 into partner prog big data v2';

    protected PartnerProgramRepositoryInterface $partnerProgramRepository;
    protected CorporateRepositoryInterface $corporateRepository;

    public function __construct(PartnerProgramRepositoryInterface $partnerProgramRepository, CorporateRepositoryInterface $corporateRepository)
    {
        parent::__construct();

        $this->partnerProgramRepository = $partnerProgramRepository;
        $this->corporateRepository = $corporateRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $partnerPrograms = $this->partnerProgramRepository->getAllPartnerProgramFromCRM();
        $partnerProgramDetails = [];

        foreach ($partnerPrograms as $partnerProgram) {

            if (!$this->partnerProgramRepository->getPartnerProgramById($partnerProgram->corprog_id)) {

                $partnerProgramDetails[] = [
                    'id' => $partnerProgram->corprog_id,
                    'corp_id' => $partnerProgram->corp_id,
                    'prog_id' => $partnerProgram->prog_id,
                    'type' => null,
                    'first_discuss' => $partnerProgram->corprog_datefirstdiscuss,
                    'notes' => $partnerProgram->corprog_notes,
                    'refund_notes' => null,
                    'refund_date' => null,
                    'status' => $partnerProgram->corprog_status,
                    'participants' => null,
                    'start_date' => null,
                    'start_date' => null,
                    'denied_date' => null,
                    'success_date' => $partnerProgram->corprog_datelastdiscuss,
                    'total_fee' => null,
                    'is_corporate_scheme' => $partnerProgram->corprog != 1 ? 2 : 1,
                    'reason_id' => null,
                    'empl_id' => null,
                    'created_at' => $partnerProgram->corprog_datefirstdiscuss,
                    'updated_at' => $partnerProgram->corprog_datefirstdiscuss
                ];
            }
        }

        if (count($partnerProgramDetails) > 0) {
            $this->partnerProgramRepository->createPartnerPrograms($partnerProgramDetails);
        }
        return Command::SUCCESS;
    }
}
