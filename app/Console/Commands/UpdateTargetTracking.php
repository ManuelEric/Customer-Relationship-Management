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
        
        # for sales
        // if ($activeTarget_forSales = $this->leadTargetRepository->findThisMonthTargetByDivision($now, 'Sales')) {
            
        //     $achievedLead = $this->leadTargetRepository->getAchievedLeadSalesByMonth($now)->count();
        //     $achievedHotLead = $this->leadTargetRepository->getAchievedHotLeadSalesByMonth($now)->count();
        //     $achievedInitConsult = $this->leadTargetRepository->getAchievedInitConsultSalesByMonth($now)->count();
        //     $achievedContribution = $this->leadTargetRepository->getAchievedContributionSalesByMonth($now)->count();

        //     $contribution_target = $activeTarget_forSales->contribution_target;
            
        //     # if the contribution target has achieved then put status into 1 which is complete
        //     $status = $contribution_target <= $achievedContribution ? 1 : 0;

        //     $details = [
        //         'achieved_lead' => $achievedLead,
        //         'achieved_hotleads' => $achievedHotLead,
        //         'achieved_initconsult' => $achievedInitConsult,
        //         'contribution_achieved' => $achievedContribution,
        //         'status' => $status,
        //         'updated_at' => Carbon::now(),
        //     ];

        //     $this->info(json_encode($details));

        //     $this->leadTargetRepository->updateActualLead($details, $now, 'Sales');
        // }

        # for referral
        if ($activeTarget_forReferral = $this->leadTargetRepository->findThisMonthTargetByDivision($now, 'Referral')) {

            $achievedLead = $this->leadTargetRepository->getAchievedLeadReferralByMonth($now)->count();
            $achievedHotLead = $this->leadTargetRepository->getAchievedHotLeadReferralByMonth($now)->count();
            $achievedInitConsult = $this->leadTargetRepository->getAchievedInitConsultSalesByMonth($now)->count();
            $achievedContribution = $this->leadTargetRepository->getAchievedContributionSalesByMonth($now)->count();

            $contribution_target = $activeTarget_forReferral->contribution_target;
            
            # if the contribution target has achieved then put status into 1 which is complete
            $status = $contribution_target <= $achievedContribution ? 1 : 0;

            $details = [
                'achieved_lead' => $achievedLead,
                'achieved_hotleads' => $achievedHotLead,
                'achieved_initconsult' => $achievedInitConsult,
                'contribution_achieved' => $achievedContribution,
                'status' => $status,
                'updated_at' => Carbon::now(),
            ];

        }

        return Command::SUCCESS;
    }
}
