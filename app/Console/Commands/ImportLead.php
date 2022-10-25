<?php

namespace App\Console\Commands;

use App\Interfaces\LeadRepositoryInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ImportLead extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:lead';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import lead from big data v1 to big data v2';

    protected LeadRepositoryInterface $leadRepository;

    public function __construct(LeadRepositoryInterface $leadRepository)
    {
        parent::__construct();

        $this->leadRepository = $leadRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $leads = $this->leadRepository->getAllLeadFromV1(); 
        foreach ($leads as $lead) {

            $new_leads[] = [
                'lead_id' => $lead->lead_id,
                'main_lead' => $lead->main_lead,
                'sub_lead' => null,
                'score' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];

        }

        $this->leadRepository->createLeads($new_leads);

        return Command::SUCCESS;
    }
}
