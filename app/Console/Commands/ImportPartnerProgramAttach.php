<?php

namespace App\Console\Commands;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\PartnerProgramRepositoryInterface;
use App\Interfaces\PartnerProgramAttachRepositoryInterface;
use App\Interfaces\CorporateRepositoryInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ImportPartnerProgramAttach extends Command
{
    use CreateCustomPrimaryKeyTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:partner_program_attach';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import corp prog attach from big data v1 into partner prog attach big data v2';

    protected PartnerProgramRepositoryInterface $partnerProgramRepository;
    protected PartnerProgramAttachRepositoryInterface $partnerProgramAttachRepository;
    protected CorporateRepositoryInterface $corporateRepository;

    public function __construct(PartnerProgramRepositoryInterface $partnerProgramRepository, CorporateRepositoryInterface $corporateRepository, PartnerProgramAttachRepositoryInterface $partnerProgramAttachRepository)
    {
        parent::__construct();

        $this->partnerProgramRepository = $partnerProgramRepository;
        $this->partnerProgramAttachRepository = $partnerProgramAttachRepository;
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
        $partnerProgAttDetails = [];

        foreach ($partnerPrograms as $partnerProgram) {

            for ($i = 1; $i < 4; $i++) {
                if ($partnerProgram['corprog_file' . $i] != null && $partnerProgram['corprog_attach' . $i] != null) {
                    if (!$this->partnerProgramAttachRepository->getPartnerProgAttByPartnerProgIdnFileName($partnerProgram->corprog_id, $partnerProgram['corprog_file' . $i])) {
                        $partnerProgAttDetails[] = [
                            'partner_prog_id' => $partnerProgram->corprog_id,
                            'corprog_file' => $partnerProgram['corprog_file' . $i],
                            'corprog_attach' => 'attachment/partner_prog_attach/' . $partnerProgram->corprog_id . '/' . $partnerProgram['corprog_attach' . $i],
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ];
                    }
                }
            }
        }

        if (count($partnerProgAttDetails) > 0) {
            $this->partnerProgramAttachRepository->createPartnerProgramAttachs($partnerProgAttDetails);
        }
        return Command::SUCCESS;
    }
}
