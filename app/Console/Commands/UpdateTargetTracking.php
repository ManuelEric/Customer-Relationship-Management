<?php

namespace App\Console\Commands;

use App\Interfaces\LeadTargetRepositoryInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class UpdateTargetTracking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:target_tracking';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update data target tracking for this month.';

    private LeadTargetRepositoryInterface $leadTargetRepository;

    public function __construct(LeadTargetRepositoryInterface $leadTargetRepository)
    {
        parent::__construct();
        $this->leadTargetRepository = $leadTargetRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = date('Y-m-d');
        $current_month = date('m');
        
        # for sales
        if ($activeTarget_forSales = $this->leadTargetRepository->findThisMonthTargetByDivision($now, 'Sales')) {
            
            $achievedLead = $this->leadTargetRepository->getAchievedLeadSalesByMonth($now);
            $achievedHotLead = $this->leadTargetRepository->getAchievedHotLeadSalesByMonth($now);
            $achievedInitConsult = $this->leadTargetRepository->getAchievedInitConsultSalesByMonth($now);
            $achievedContribution = $this->

            $contribution_target = $activeTarget_forSales->contribution_target;
            

            $details = [
                'achieved_lead' => $achievedLead,
                'achieved_hotleads' => $achievedHotLead,
                'achieved_initconsult' => $achievedInitConsult,
                'contribution_achieved' => 0,
                'status' => 0,
                'updated_at' => Carbon::now(),
            ];

            $this->leadTargetRepository->updateActualLead($details, $now, 'Sales');
        }


        return Command::SUCCESS;
    }
}
