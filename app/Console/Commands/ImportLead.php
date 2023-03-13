<?php

namespace App\Console\Commands;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\LeadRepositoryInterface;
use App\Models\Lead;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ImportLead extends Command
{
    use CreateCustomPrimaryKeyTrait;
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
        $new_leads = [];

        $count = 1;
        foreach ($leads as $lead) {
            $leadIdV2 = $this->leadRepository->getLeadById($lead->lead_id);
            $mainLeadV2 = $this->leadRepository->getLeadByMainLead($lead->main_lead);

            if (!$leadIdV2) {
                if ($lead->lead_id != "" && $lead->lead_id != NULL) {
                    $new_leads[] = [
                        'lead_id' => $lead->lead_id,
                        'main_lead' => $lead->main_lead,
                        'sub_lead' => null,
                        'score' => 0,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                }
            } else if ($leadIdV2 && !$mainLeadV2) {
                $last_id = Lead::max('lead_id');
                $lead_id_without_label = $this->remove_primarykey_label($last_id, 2);
                $lead_id_with_label = 'LS' . $this->add_digit($lead_id_without_label + $count, 3);

                $new_leads[] = [
                    'lead_id' => $lead_id_with_label,
                    'main_lead' => $lead->main_lead,
                    'sub_lead' => null,
                    'score' => 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
                $count++;
            }
        }

        if (count($new_leads) > 0) {
            $this->leadRepository->createLeads($new_leads);
        }

        return Command::SUCCESS;
    }
}
